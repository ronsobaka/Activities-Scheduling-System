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

            $stmt = $connection->prepare("SELECT id, name, start_time, end_time, staff_required, location, equipment, notes FROM activities WHERE userID = ? AND activity_date = ?");
            $stmt->bind_param("is", $userID, $date);
            $stmt->execute();
            $result = $stmt->get_result();

            $activities = [];
            while ($row = $result->fetch_assoc()) {
                $activities[] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "startTime" => $row['start_time'],
                    "endTime" => $row['end_time'],
                    "staffRequired" => $row['staff_required'],
                    "location" => $row['location'],
                    "equipment" => $row['equipment'],
                    "notes" => $row['notes']
                ];
            }

            echo json_encode($activities);

        } else if ($month && $year) {

            $monthNum = array_search($month, ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]) + 1;
            $startDate = "$year-$monthNum-01";
            $endDate = date("Y-m-t", strtotime($startDate));
            
            $stmt = $connection->prepare("SELECT id, name, start_time, end_time, staff_required, location, equipment, notes, activity_date FROM activities WHERE userID = ? AND activity_date BETWEEN ? AND ?");
            $stmt->bind_param("iss", $userID, $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $activities = [];
            while ($row = $result->fetch_assoc()) {
                $date = $row['activity_date'];
                if (!isset($activities[$date])) {
                    $activities[$date] = [];
                }
                $activities[$date][] = [
                    "id" => $row['id'],
                    "name" => $row['name'],
                    "startTime" => $row['start_time'],
                    "endTime" => $row['end_time'],
                    "staffRequired" => $row['staff_required'],
                    "location" => $row['location'],
                    "equipment" => $row['equipment'],
                    "notes" => $row['notes']
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
        //delete existing data
        try {
            $deleteStmt = $connection->prepare("DELETE FROM activities WHERE userID = ? AND activity_date = ?");
            $deleteStmt->bind_param("is", $userID, $date);
            $deleteStmt->execute();

            if (!empty($activities)) {
                $insertStmt = $connection->prepare(
                    "INSERT INTO activities (userID, activity_date, name, start_time, end_time, staff_required, location, equipment, notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                foreach ($activities as $activity) {
                    $insertStmt->bind_param(
                        "issssisss",
                        $userID,
                        $date,
                        $activity['name'],
                        $activity['startTime'],
                        $activity['endTime'],
                        $activity['staffRequired'],
                        $activity['location'],
                        $activity['equipment'],
                        $activity['notes']
                    );
                    $insertStmt->execute();
                }
            }

            $connection->commit();
            echo json_encode(["success" => true]);

        } catch (Exception $e) {
            $connection->rollback();
            echo json_encode(["error" => "Failed to save activities: " . $e->getMessage()]);
        }

        exit();
    }
?>