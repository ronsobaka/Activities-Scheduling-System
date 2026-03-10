<?php
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

    if ($connection->connect_error) {
        echo json_encode(["error" => "Database connection failed"]);
        exit();
    }

    // Handle GET request to fetch activities
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $date = $_GET['date'] ?? null;
        $month = $_GET['month'] ?? null;
        $year = $_GET['year'] ?? null;

        
        if ($date) {

            $stmt = $connection->prepare("
                SELECT a.id, a.name, a.startTime, a.endTime, a.location, a.equipment, a.notes,
                    GROUP_CONCAT(aa.userID) as assigned_staff
                FROM activities a
                LEFT JOIN activityassignments aa ON a.id = aa.activityID
                WHERE a.userID = ? AND a.activityDate = ?
                GROUP BY a.id
            ");
            $stmt->bind_param("is", $userID, $date);
            $stmt->execute();
            $result = $stmt->get_result();

            $activities = [];
            while ($row = $result->fetch_assoc()) {
                $activities[] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "startTime" => $row['startTime'],
                    "endTime" => $row['endTime'],
                    "location" => $row['location'],
                    "equipment" => $row['equipment'],
                    "notes" => $row['notes'],
                    "selectedStaff" => $row['assigned_staff'] ? explode(',', $row['assigned_staff']) : []
                ];
            }

            echo json_encode($activities);

        } else if ($month && $year) {

            $monthNum = array_search($month, ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]) + 1;
            $startDate = "$year-$monthNum-01";
            $endDate = date("Y-m-t", strtotime($startDate));
            
            $stmt = $connection->prepare("
                SELECT a.id, a.name, a.startTime, a.endTime, a.location, a.equipment, a.notes, a.activityDate,
                    GROUP_CONCAT(aa.userID) as assigned_staff
                FROM activities a
                LEFT JOIN activityassignments aa ON a.id = aa.activityID
                WHERE a.userID = ? AND a.activityDate BETWEEN ? AND ?
                GROUP BY a.id
            ");
            $stmt->bind_param("iss", $userID, $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $activities = [];
            while ($row = $result->fetch_assoc()) {
                $date = $row['activityDate'];
                if (!isset($activities[$date])) {
                    $activities[$date] = [];
                }
                $activities[$date][] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "startTime" => $row['startTime'],
                    "endTime" => $row['endTime'],
                    "location" => $row['location'],
                    "equipment" => $row['equipment'],
                    "notes" => $row['notes'],
                    "selectedStaff" => $row['assigned_staff'] ? explode(',', $row['assigned_staff']) : []
                ];
            }
            
            echo json_encode($activities);
        } else {
            echo json_encode(["error" => "Date or month parameter required"]);
        }
    }

    // Handle POST request to save activity

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        if (!$data || !isset($data['activities']) || !isset($data['date'])) {
            echo json_encode(["error" => "Invalid input"]);
            exit();
        }

        $date = $data['date'];
        $activities = $data['activities'];

        $connection->begin_transaction();
  
        try {

            $getIDsStmt = $connection->prepare("SELECT id FROM activities WHERE userID = ? AND activityDate = ?");
            $getIDsStmt->bind_param("is", $userID, $date);
            $getIDsStmt->execute();
            $result = $getIDsStmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $deleteAssignmentsStmt = $connection->prepare("DELETE FROM activityassignments WHERE activityID = ?");
                $deleteAssignmentsStmt->bind_param("i", $row['id']);
                $deleteAssignmentsStmt->execute();
            }


            $deleteStmt = $connection->prepare("DELETE FROM activities WHERE userID = ? AND activityDate = ?");
            $deleteStmt->bind_param("is", $userID, $date);
            $deleteStmt->execute();

            if (!empty($activities)) {
                $insertStmt = $connection->prepare(
                    "INSERT INTO activities (userID, activityDate, name, startTime, endTime, location, equipment, notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );

                foreach ($activities as $activity) {
                    $insertStmt->bind_param(
                        "isssssss",
                        $userID,
                        $date,
                        $activity['name'],
                        $activity['startTime'],
                        $activity['endTime'],
                        $activity['location'],
                        $activity['equipment'],
                        $activity['notes']
                    );
                    $insertStmt->execute();
                }
            }

            $connection->commit();

            $selectStmt = $connection->prepare("SELECT id, name, startTime, endTime, location, equipment, notes FROM activities WHERE userID = ? AND activityDate = ?");
            $selectStmt->bind_param("is", $userID, $date);
            $selectStmt->execute();
            $result = $selectStmt->get_result();

            $savedActivities = [];
            while ($row = $result->fetch_assoc()) {
                $savedActivities[] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "startTime" => $row['startTime'],
                    "endTime" => $row['endTime'],
                    "location" => $row['location'],
                    "equipment" => $row['equipment'],
                    "notes" => $row['notes']
                ];
            }

            echo json_encode([
                "success" => true,
                "activities" => [$date => $savedActivities]
            ]);
            exit();

        } catch (Exception $e) {
            $connection->rollback();
            echo json_encode(["error" => "Failed to save activities: " . $e->getMessage()]);
        }

        exit();
    }
?>