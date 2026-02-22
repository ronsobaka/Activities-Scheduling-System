<?php
    require_once '../globalFunctions.php';
    if (!isset($_SESSION['userID']) || !isset($_SESSION['roleID'])) {
        header("Location: ../login/loginHTML.php");
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
        <a>Home</a>
        <a href="availability/availabilityHTML.php">Availability</a>
        <?php if ($_SESSION['isManager']): ?>
            <a href="scheduleManager/scheduleManagerHTML.php">Schedule Manager</a>
        <?php endif; ?>
        <a href="#contact">Contact</a>
        <a href="#about">About</a>
    </div>
    <div class="divider"></div>
    <div class="title">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['firstName']);?>!</h1>
    </div>
  
    

    <script src="main.js"></script> 
</body>
</html>
