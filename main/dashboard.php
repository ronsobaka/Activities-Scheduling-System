<?php
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
    
    // Upcoming shifts for this user
    $query = "SELECT COUNT(*) as count FROM activities a 
              JOIN activityassignments aa ON a.id = aa.activityID 
              WHERE aa.userID = ? AND a.activityDate >= CURDATE()";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['upcomingShifts'] = $result->fetch_assoc()['count'];
    
    // Manager-only stats
    if ($isManager) {
        $result = $connection->query("SELECT COUNT(*) as count FROM user WHERE roleID = 3");
        $stats['totalStaff'] = $result->fetch_assoc()['count'];
        
        $result = $connection->query("SELECT COUNT(*) as count FROM user WHERE status = 'pending'");
        $stats['pendingApprovals'] = $result->fetch_assoc()['count'];
        
        $result = $connection->query("SELECT COUNT(*) as count FROM activities WHERE activityDate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
        $stats['activitiesThisWeek'] = $result->fetch_assoc()['count'];
    }
    
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
    
} else if ($_GET['action'] === 'today') {
    $today = date('Y-m-d');
    $activities = [];
    
    $query = "SELECT id, name, startTime, endTime, location FROM activities WHERE activityDate = ? ORDER BY startTime ASC";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    
    echo json_encode($activities);
    
} else if ($_GET['action'] === 'onsite') {
    $today = date('Y-m-d');
    $staff = [];
    
    $query = "SELECT u.userID, u.firstName, u.lastName, a.name as activity, a.startTime, a.endTime
              FROM activities a
              JOIN activityassignments aa ON a.id = aa.activityID
              JOIN user u ON aa.userID = u.userID
              WHERE a.activityDate = ?
              ORDER BY a.startTime ASC";
    
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $staff[] = $row;
    }
    
    echo json_encode($staff);
}
?>