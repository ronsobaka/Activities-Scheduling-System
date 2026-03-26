<?php
session_start();
require_once '../globalFunctions.php';

if (!isAuthenticated()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$connection = getDBConnection();
$userID = $_SESSION['userID'];
$isManager = $_SESSION['isManager'];

if ($_GET['action'] === 'stats') {
    $stats = [];
    
    // Upcoming shifts
    $query = "SELECT COUNT(*) as count FROM activities a 
              JOIN activityassignments aa ON a.id = aa.activityID 
              WHERE aa.userID = ? AND a.activityDate >= CURDATE()";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['upcomingShifts'] = $result->fetch_assoc()['count'];
    
    // Total staff (managers only)
    if ($isManager) {
        $result = $connection->query("SELECT COUNT(*) as count FROM user WHERE roleID = 3");
        $stats['totalStaff'] = $result->fetch_assoc()['count'];
        
        $result = $connection->query("SELECT COUNT(*) as count FROM user WHERE status = 'pending'");
        $stats['pendingApprovals'] = $result->fetch_assoc()['count'];
    } else {
        $stats['totalStaff'] = 0;
        $stats['pendingApprovals'] = 0;
    }
    
    // Activities this week
    $result = $connection->query("SELECT COUNT(*) as count FROM activities WHERE activityDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
    $stats['activitiesThisWeek'] = $result->fetch_assoc()['count'];
    
    echo json_encode($stats);
    
} else if ($_GET['action'] === 'upcoming') {
    $shifts = [];
    
    $query = "SELECT a.id, a.name, a.startTime, a.endTime, a.location, a.activityDate 
              FROM activities a 
              JOIN activityassignments aa ON a.id = aa.activityID 
              WHERE aa.userID = ? AND a.activityDate >= CURDATE() 
              ORDER BY a.activityDate ASC, a.startTime ASC 
              LIMIT 10";
    
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $shifts[] = $row;
    }
    
    echo json_encode($shifts);
}
?>