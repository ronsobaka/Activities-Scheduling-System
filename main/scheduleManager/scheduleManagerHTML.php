<?php
    session_start();
    require_once '../../globalFunctions.php';
    if ((!isset($_SESSION['userID'])) || (!isset($_SESSION['roleID']))) {
        header("Location: ../../login/loginHTML.php");
        exit();
    }

    if ($_SESSION['roleID'] != 1 && $_SESSION['roleID'] != 2) {
        header("Location: ../main.php");
        exit();
    }
    $userID = $_SESSION['userID'];
    $roleID = $_SESSION['roleID'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../main.css">
    <link rel="stylesheet" href="scheduleManager.css">
</head>
<body>
    <div class="topnav">
        <a href="../main.php">Home</a>
        <a href="../availability/availabilityHTML.php">Availability</a>
        <?php if ($_SESSION['isManager']): ?>
            <a class="active" href="../scheduleManager/scheduleManagerHTML.php">Schedule Manager</a>
        <?php endif; ?>
        <a href="#contact">Contact</a>
        <a href="#about">About</a>
    </div>
    <div class="divider"></div>
    <div class="title"> 
        <h1>Schedule Manager</h1>
    </div>

    <div id="scheduleManagerContainer" class="schedule-manager-container">
        <div class="schedule-manager-header">
            <button id="prevMonth">Previous Month</button>
            <h2 id="scheduleManagerTitle">Schedule manager</h2>
            <div class="header-right">
                <div class="view-dropdown-container">
                    <label for="viewDropdown">View:</label>
                    <select id="viewDropdown">
                        <option value="month">Month</option>
                        <option value="week">Week</option>
                        <option value="day">Day</option>
                    </select>
                </div>
                <button id="nextMonth">Next Month</button>
            </div>
        </div>

        <div id="scheduleManagerContent" class="schedule-manager-content">
            <!-- Schedule content will be dynamically generated here -->
        </div>

    <script src="scheduleManager.js"></script>
</body>
</html>