<?php
    session_start();
    require_once '../../../globalFunctions.php';

    if ($_SESSION['roleID'] !== 1) {
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }

    $connection = new mysqli("localhost", "root", "", "finalproject");
    if ($connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT roleName, roleDescription, colour, roleID FROM roles ORDER BY roleName";
        $result = $connection->query($query);
        $roles = [];
    
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($roles);
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
            exit();
        }
        
        if (isset($data['action']) && $data['action'] === 'delete') {
            // First check if any users have this role
            $checkQuery = "SELECT u.userID, u.firstName, u.lastName FROM user u WHERE u.roleID = ?";
            $checkStmt = $connection->prepare($checkQuery);
            $checkStmt->bind_param("i", $data['roleID']);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            $assignedStaff = [];

            while ($row = $result->fetch_assoc()) {
                $assignedStaff[] = $row;
            }

            if (count($assignedStaff) > 0) {
                echo json_encode([
                    'success' => false, 
                    'error' => 'Cannot delete role - users are assigned to it',
                    'assignedStaff' => $assignedStaff  // Return the list of staff
                ]);
                exit();
            }
            
            // If no users have this role, delete it
            $query = "DELETE FROM roles WHERE roleID = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("i", $data['roleID']);
            
        } else if (isset($data['roleID']) && !empty($data['roleID'])) {
            $query = "UPDATE roles SET roleName = ?, roleDescription = ?, colour = ? WHERE roleID = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param ("sssi",
                $data['roleName'],
                $data['roleDescription'],
                $data['colour'],
                $data['roleID']
            );
        } else {
            $query = "INSERT INTO roles (roleName, roleDescription, colour) VALUES (?, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("sss", $data['roleName'], $data['roleDescription'], $data['colour']);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    }
?>