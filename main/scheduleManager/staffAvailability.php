<?php
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
            echo json_encode(["error" => "Activity ID parameter is required"]);
            exit();
        }

        $staffAvailability = [];

        //fetch all staff for the day

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

        //unavailable staff for the day

        $unavailableStaffQuery = "SELECT userID FROM unavailabledates WHERE available_date = ?";

        $stmt = $connection->prepare($unavailableStaffQuery);
        $stmt->bind_param("s", $dbDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while($row = $result->fetch_assoc()) {
            if (isset($staffAvailability[$row['userID']]) ) {
                $staffAvailability[$row['userID']]['availability'] = "unavailable";
            }
        }

        //conditioned staff for the day

        $conditionedStaffQuery = "SELECT userID, startTime, endTime, reason FROM conditions WHERE condition_date = ?";

        $stmt = $connection->prepare($conditionedStaffQuery);
        $stmt->bind_param("s", $dbDate);
        $stmt->execute();
        $result = $stmt->get_result();

        
        while($row = $result->fetch_assoc()) {
            if (isset($staffAvailability[$row['userID']]) ) {
                $staffAvailability[$row['userID']]['availability'] = "conditioned";
                $staffAvailability[$row['userID']]['conditions'][] = [
                    "startTime" => $row['startTime'],
                    "endTime" => $row['endTime'],
                    "reason" => $row['reason']
                ];
            }
        }

        //selected staff for activity

        $selectedStaffQuery = "SELECT userID FROM activityassignments WHERE activity_id = ?";

        $stmt = $connection->prepare($selectedStaffQuery);
        $stmt->bind_param("i", $activityID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if (isset($staffAvailability[$row['userID']]) ) {
                $staffAvailability[$row['userID']]['selected'] = true;
            }
        }

        //other activites for on the day

        $otherAssignmentsQuery = "
            SELECT "activities.id, activities.startTime, activities.endTime, activities.name, actitivity_assignments.userID

        $staffAvailability = array_values($staffAvailability);
        echo json_encode($staffAvailability);
    }


?>