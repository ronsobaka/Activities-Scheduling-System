<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
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
        $action = $_GET['action'] ?? null;


        if ($action === 'features') {
            $query = "SELECT featureID, name, description FROM features";
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $result = $stmt->get_result();
            $features = [];

            while ($row = $result->fetch_assoc()) {
                $features[] = $row;
            }

            echo json_encode($features);
        } else if ($action === 'getPermissions' && isset($_GET['roleID'])) {
            $roleID = $_GET['roleID'];
            $query = "SELECT roleID, featureID FROM rolepermissions WHERE roleID = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("i", $roleID);
            $stmt->execute();
            $result = $stmt->get_result();
            $permissions = [];

            while ($row = $result->fetch_assoc()) {
                $permissions[] = $row;
            }
            
            echo json_encode($permissions);
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($data['action'] === 'save') {
            $roleID = $data['roleID'];
            $featureIDs = $data['featureIDs'];
            
            $connection->begin_transaction();
            
            try {
                // Delete existing permissions
                $deleteStmt = $connection->prepare("DELETE FROM rolepermissions WHERE roleID = ?");
                $deleteStmt->bind_param("i", $roleID);
                $deleteStmt->execute();
                
                // Insert new permissions
                if (!empty($featureIDs)) {
                    $insertStmt = $connection->prepare("INSERT INTO rolepermissions (roleID, featureID) VALUES (?, ?)");
                    foreach ($featureIDs as $featureID) {
                        $insertStmt->bind_param("ii", $roleID, $featureID);
                        $insertStmt->execute();
                    }
                }
                
                $connection->commit();
                echo json_encode(['success' => true]);
                
            } catch (Exception $e) {
                $connection->rollback();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        }
    }
?>