<?php
session_start();
require_once '../../globalFunctions.php';

if (!isAuthenticated() || !canAccess($_SESSION['roleID'], 'Approve Staff')) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$connection = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get email verified staff awaiting approval
    $query = "SELECT userID, firstName, lastName, email, created_at 
              FROM user 
              WHERE status = 'email_verified' 
              ORDER BY created_at ASC";
    
    $result = $connection->query($query);
    $pending = [];
    
    while ($row = $result->fetch_assoc()) {
        $pending[] = $row;
    }
    
    echo json_encode($pending);
    
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userID = $data['userID'];
    
    if ($data['action'] === 'approve') {
        $settingsResult = $connection->query("SELECT defaultRole FROM systemsettings LIMIT 1");
        $defaultRole = 4;
        if ($settingsResult && $settingsResult->num_rows > 0) {
            $row = $settingsResult->fetch_assoc();
            $defaultRole = $row['defaultRole'];
        }
        
        $query = "UPDATE user SET status = 'active', roleID = ? WHERE userID = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $defaultRole, $userID);
        
    } else if ($data['action'] === 'reject') {
        $query = "DELETE FROM user WHERE userID = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $userID);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}
?>