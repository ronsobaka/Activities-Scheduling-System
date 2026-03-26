<?php
    session_start();
    require_once '../../../globalFunctions.php';

    if (!isAuthenticated() || !canAccess($_SESSION['roleID'], 'System Settings')) {
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }

    $connection = getDBConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get system settings (you'll need to create this table)
        $query = "SELECT * FROM systemsettings LIMIT 1";
        $result = $connection->query($query);
        
        if ($result && $result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            // Return default settings if none exist
            echo json_encode([
                'siteName' => 'Staff Scheduling System',
                'siteEmail' => 'admin@example.com',
                'defaultRole' => '4',
                'sessionTimeout' => '30',
                'dateFormat' => 'd/m/Y',
                'allowSelfRegistration' => 1,
                'requireApproval' => 1,
                'maintenanceMessage' => ''
            ]);
        }
        
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // First, check if settings exist
        $checkQuery = "SELECT COUNT(*) as count FROM systemsettings";
        $result = $connection->query($checkQuery);
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            $query = "UPDATE systemsettings SET 
                siteName = ?, siteEmail = ?, defaultRole = ?, 
                sessionTimeout = ?, dateFormat = ?, allowSelfRegistration = ?, 
                requireApproval = ?, maintenanceMessage = ?";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ssiisiss", 
                $data['siteName'], $data['siteEmail'], $data['defaultRole'],
                $data['sessionTimeout'], $data['dateFormat'], $data['allowSelfRegistration'],
                $data['requireApproval'], $data['maintenanceMessage']
            );
        } else {
            $query = "INSERT INTO systemsettings 
                (siteName, siteEmail, defaultRole, sessionTimeout, dateFormat, 
                allowSelfRegistration, requireApproval, maintenanceMessage) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("ssiisiss", 
                $data['siteName'], $data['siteEmail'], $data['defaultRole'],
                $data['sessionTimeout'], $data['dateFormat'], $data['allowSelfRegistration'],
                $data['requireApproval'], $data['maintenanceMessage']
            );
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        $stmt->close();
    }
?>