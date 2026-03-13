<?php
    require_once '../globalFunctions.php';
    // Check if user is logged in
    if (!isAuthenticated()) {
        header("Location: ../login/loginHTML.php");
        exit();
    }

    // Check if user has permission for this specific page
    if (!canAccess($_SESSION['roleID'], 'Dashboard')) {
        header("Location: ../unauthorized.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="topnav">
        <a class="active">Home</a>
        <a href="availability/availabilityHTML.php">Availability</a>
        <?php if ($_SESSION['isManager']): ?>
            <a href="scheduleManager/scheduleManagerHTML.php">Schedule Manager</a>
            <div class="dropdown">
            <button class="dropBtn">Manage ▼</button>
            <div class="dropdown-content">
                <a href="approveStaff/approveStaffHTML.php">Approve Staff</a>
                <a href="systemSettings/systemSettingsHTML.php">System Settings</a>
            </div>
        </div> 
        <?php endif; ?>
    </div>
    <div class="divider"></div>
    <div class="title">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['firstName']);?>!</h1>
    </div>
  
    

    <script src="main.js"></script> 
</body>
</html>
