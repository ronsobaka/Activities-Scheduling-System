<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ob_clean();
    session_start();
    require_once '../../globalFunctions.php';

    header("content-type: application/json"); 

    $userID = $_SESSION['userID'] ?? null;
    $roleID = $_SESSION['roleID'] ?? null;

    if (!$userID || !$roleID || ($roleID != 1 && $roleID != 2)) {
        echo json_encode(["error" => "Unauthorized"]);
        exit();
    }

    $connection = new mysqli("localhost", "root", "", "finalproject");

    if ($connection->connect_error || !$connection) {
        echo json_encode(["error" => "Database connection failed"]);
        exit();
    }

    // Handle POST request - Save staff assignments
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['activityID']) || !isset($data['staffIds'])) {
            echo json_encode(["error" => "Activity ID and staff IDs are required"]);
            exit();
        }

        $activityID = $data['activityID'];
        $staffIds = $data['staffIds'];

        $connection->begin_transaction();

        try {
            // Delete existing assignments
            $deleteStmt = $connection->prepare("DELETE FROM activityassignments WHERE activityID = ?");
            $deleteStmt->bind_param("i", $activityID);
            $deleteStmt->execute();

            // Insert new assignments
            if (!empty($staffIds)) {
                $insertStmt = $connection->prepare("INSERT INTO activityassignments (activityID, userID) VALUES (?, ?)");
                foreach ($staffIds as $staffId) {
                    $insertStmt->bind_param("ii", $activityID, $staffId);
                    $insertStmt->execute();
                }
            }

            $connection->commit();
            echo json_encode(["success" => true]);

        } catch (Exception $e) {
            $connection->rollback();
            echo json_encode(["error" => "Failed to save assignments: " . $e->getMessage()]);
        }
        exit();
    }

    // Handle GET request - Fetch staff availability
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $date = $_GET['date'] ?? null;
        $activityID = $_GET['activityID'] ?? null;

        if (!$date) {
            echo json_encode(["error" => "Date parameter is required"]);
            exit();
        } else {
            $parts = explode('-', $date);
            $dbDate = sprintf("%04d-%02d-%02d", $parts[0], $parts[1], $parts[2]);
        }

        if (!$activityID) {
            $activityID = 0;
        }

        $staffAvailability = [];

        $allStaffQuery = "SELECT userID, firstName, lastName FROM user WHERE roleID = 3";
        $stmt = $connection->prepare($allStaffQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row  = $result->fetch_assoc()) {
            $staffAvailability[$row['userID']]= [
                "userID" => $row['userID'],
                "firstName" => $row['firstName'],
                "lastName" => $row['lastName'],
                "availability" => "available",
                "selected" => false,
                "conditions" => []
            ];
        }

        $unavailableStaffQuery = "SELECT userID FROM unavailabledates WHERE unavailabledate = ?";
        $stmt = $connection->prepare($unavailableStaffQuery);
        $stmt->bind_param("s", $dbDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while($row = $result->fetch_assoc()) {
            if (isset($staffAvailability[$row['userID']]) ) {
                $staffAvailability[$row['userID']]['availability'] = "unavailable";
            }
        }

        $conditionedStaffQuery = "SELECT userID, starttime, endtime, reason FROM conditions WHERE conditiondate = ?";
        $stmt = $connection->prepare($conditionedStaffQuery);
        $stmt->bind_param("s", $dbDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while($row = $result->fetch_assoc()) {
            if (isset($staffAvailability[$row['userID']]) ) {
                $staffAvailability[$row['userID']]['availability'] = "conditioned";
                $staffAvailability[$row['userID']]['conditions'][] = [
                    "startTime" => $row['starttime'],
                    "endTime" => $row['endtime'],
                    "reason" => $row['reason']
                ];
            }
        }

        $selectedStaffQuery = "SELECT userID FROM activityassignments WHERE activityid = ?";
        $stmt = $connection->prepare($selectedStaffQuery);
        if ($activityID) {
            $stmt->bind_param("i", $activityID);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                if (isset($staffAvailability[$row['userID']]) ) {
                    $staffAvailability[$row['userID']]['selected'] = true;
                }
            }
        }

        $otherAssignments = [];

        $otherAssignmentsQuery = "
            SELECT a.id, a.starttime, a.endtime, a.name, aa.userID
            FROM activities a
            JOIN activityassignments aa ON a.id = aa.activityid
            WHERE a.activitydate = ? AND a.id != ?
        ";
        $stmt = $connection->prepare($otherAssignmentsQuery);
        $stmt->bind_param("si", $dbDate, $activityID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $otherAssignments[] = [
                "activityID" => $row['id'],
                "name" => $row['name'],
                "startTime" => $row['starttime'],
                "endTime" => $row['endtime'],
                "userID" => $row['userID']
            ];
        }

        $response = [
            "staff" => array_values($staffAvailability),
            "otherAssignments" => $otherAssignments
        ];

        echo json_encode($response);
        exit();
    }
?>s