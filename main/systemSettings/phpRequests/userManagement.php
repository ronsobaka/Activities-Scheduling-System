<?php
    session_start();
    require_once '../../../globalFunctions.php';

    header('Content-Type: application/json');

    if ($_SESSION['roleID'] !== 1) {
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }

    $connection = new mysqli("localhost", "root", "", "finalproject");
    if ($connection->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['action']) && $_GET['action'] === 'roles') {
            $query = "SELECT roleID, roleName FROM roles ORDER BY roleName";
            $result = $connection->query($query);
            
            if (!$result) {
                echo json_encode(['error' => 'Query failed: ' . $connection->error]);
                exit();
            }
            
            $roles = [];
            while ($row = $result->fetch_assoc()) {
                $roles[] = $row;
            }
            echo json_encode($roles);
            
        } else {
            $query = "SELECT u.userID, u.firstName, u.lastName, u.email, u.status, u.roleID, r.roleName 
                FROM user u 
                LEFT JOIN roles r ON u.roleID = r.roleID 
                ORDER BY u.userID DESC";
            
            $result = $connection->query($query);
            
            if (!$result) {
                echo json_encode(['error' => 'Query failed: ' . $connection->error]);
                exit();
            }
            
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
            exit();
        }
        
        if (isset($data['action']) && $data['action'] === 'status') {
            $query = "UPDATE user SET status = ? WHERE userID = ?";
            $stmt = $connection->prepare($query);
            if (!$stmt) {
                echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $connection->error]);
                exit();
            }
            $stmt->bind_param("si", $data['newStatus'], $data['userID']);
        } else {
            $query = "UPDATE user SET firstName = ?, lastName = ?, email = ?, roleID = ?, status = ? WHERE userID = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("sssisi", 
                $data['firstName'],
                $data['lastName'],
                $data['email'],
                $data['roleID'],
                $data['status'],
                $data['userID']
            );
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    }

    $connection->close();
?>