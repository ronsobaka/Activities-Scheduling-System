<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ob_clean();
    session_start();
    require_once '../../globalFunctions.php';
    require_once '../../PHPMailer/src/Exception.php';
    require_once '../../PHPMailer/src/PHPMailer.php';
    require_once '../../PHPMailer/src/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

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

            // ---- SHIFT NOTIFICATION EMAILS ----
            if (!empty($staffIds)) {

                // Get activity details
                $activityStmt = $connection->prepare("
                    SELECT name, activityDate, startTime, endTime, location, equipment, notes 
                    FROM activities 
                    WHERE id = ?
                ");
                $activityStmt->bind_param("i", $activityID);
                $activityStmt->execute();
                $activity = $activityStmt->get_result()->fetch_assoc();

                $formattedDate = date('l j F Y', strtotime($activity['activityDate']));
                $startTime = substr($activity['startTime'], 0, 5);
                $endTime = substr($activity['endTime'], 0, 5);

                // Get each staff member's email and name
                $placeholders = implode(',', array_fill(0, count($staffIds), '?'));
                $staffStmt = $connection->prepare("
                    SELECT firstName, lastName, email 
                    FROM user 
                    WHERE userID IN ($placeholders)
                ");
                $types = str_repeat('i', count($staffIds));
                $staffStmt->bind_param($types, ...$staffIds);
                $staffStmt->execute();
                $staffResult = $staffStmt->get_result();

                while ($staff = $staffResult->fetch_assoc()) {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'ronniejf2004@gmail.com';
                        $mail->Password   = 'mljh zisl lemg knrt';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        $mail->setFrom('ronniejf2004@gmail.com', 'Staff Scheduling');
                        $mail->addAddress($staff['email']);
                        $mail->Subject = "You've been assigned to {$activity['name']}";
                        $mail->isHTML(true);

                        $locationRow = $activity['location'] 
                            ? "<tr><td style='padding: 8px; color: #666;'>Location</td><td style='padding: 8px;'>{$activity['location']}</td></tr>" 
                            : "";
                        $equipmentRow = $activity['equipment'] 
                            ? "<tr><td style='padding: 8px; color: #666;'>Equipment</td><td style='padding: 8px;'>{$activity['equipment']}</td></tr>" 
                            : "";
                        $notesRow = $activity['notes'] 
                            ? "<tr><td style='padding: 8px; color: #666;'>Notes</td><td style='padding: 8px;'>{$activity['notes']}</td></tr>" 
                            : "";

                        $mail->Body = "
                            <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto;'>
                                <h2 style='background: linear-gradient(to right, #7aa9b8, #1c0696); color: white; padding: 20px; text-align: center;'>
                                    Staff Scheduling
                                </h2>
                                <div style='padding: 20px;'>
                                    <p>Hi {$staff['firstName']},</p>
                                    <p>You have been assigned to the following activity:</p>
                                    
                                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0; background: #f9f9f9; border-radius: 5px;'>
                                        <tr style='background: #040d8a; color: white;'>
                                            <td colspan='2' style='padding: 10px; font-weight: bold;'>{$activity['name']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 8px; color: #666;'>Date</td>
                                            <td style='padding: 8px;'>{$formattedDate}</td>
                                        </tr>
                                        <tr style='background: #f0f0f0;'>
                                            <td style='padding: 8px; color: #666;'>Time</td>
                                            <td style='padding: 8px;'>{$startTime} - {$endTime}</td>
                                        </tr>
                                        {$locationRow}
                                        {$equipmentRow}
                                        {$notesRow}
                                    </table>

                                    <p style='color: #888; font-size: 0.85em;'>
                                        Please log in to the staff scheduling system to view your full schedule.
                                    </p>
                                </div>
                            </div>
                        ";

                        $mail->send();

                    } catch (Exception $e) {
                        // Log email failure but don't stop execution
                        error_log("Failed to send shift notification to {$staff['email']}: " . $e->getMessage());
                    }
                }
            }
            // ---- END SHIFT NOTIFICATION EMAILS ----

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

        $allStaffQuery = "
            SELECT u.userID, u.firstName, u.lastName, u.roleID, r.colour as roleColour 
            FROM user u 
            LEFT JOIN roles r ON u.roleID = r.roleID 
            WHERE u.status = 'active'
        ";
        $stmt = $connection->prepare($allStaffQuery);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $staffAvailability[$row['userID']] = [
                "userID" => $row['userID'],
                "firstName" => $row['firstName'],
                "lastName" => $row['lastName'],
                "roleColour" => $row['roleColour'] ?? '#1c0696',
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
        
        while ($row = $result->fetch_assoc()) {
            if (isset($staffAvailability[$row['userID']])) {
                $staffAvailability[$row['userID']]['availability'] = "unavailable";
            }
        }

        $conditionedStaffQuery = "SELECT userID, starttime, endtime, reason FROM conditions WHERE conditiondate = ?";
        $stmt = $connection->prepare($conditionedStaffQuery);
        $stmt->bind_param("s", $dbDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            if (isset($staffAvailability[$row['userID']])) {
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
                if (isset($staffAvailability[$row['userID']])) {
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
?>