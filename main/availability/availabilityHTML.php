<?php
    session_start();
    require_once '../../globalFunctions.php';
    if (!isset($_SESSION['userID']) || !isset($_SESSION['roleID']) || $_SESSION['loggedIn'] !== true) {
        header("Location: ../login/loginHTML.php");
        exit();
    }
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../main.css">
</head>
<body>
    <div class="topnav">
        <a href="../main.php">Home</a>
        <a class="active">Availability</a>
        <?php if ($_SESSION['isManager']): ?>
            <a href="../scheduleManager/scheduleManagerHTML.php">Schedule Manager</a>
            <div class="dropdown">
                <button class="dropBtn">Manage ▼</button>
                <div class="dropdown-content">
                    <a href="../approveStaff/approveStaffHTML.php">Approve Staff</a>
                    <a href="../systemSettings/systemSettingsHTML.php">System Settings</a>  
                </div>
            </div> 
        <?php endif; ?>        
        <a href="#about">About</a>
    </div>
    <div class="divider"></div>
    <div class="title">
        <h1>Your Availability</h1>
    </div>
    
    <div class="calendar-container">
        <div class="calendar-header">
            <button id="prevMonth">Previous Month</button>
            <h2 id="weekRange"></h2>
            <button id="nextMonth">Next Month</button>
        </div>
        <div id="calendarGrid" class="calendar-grid">
            <div class="calendar-weekdays">
                <div class="weekday">Monday</div>
                <div class="weekday">Tuesday</div>
                <div class="weekday">Wednesday</div>
                <div class="weekday">Thursday</div>
                <div class="weekday">Friday</div>
                <div class="weekday">Saturday</div>
                <div class="weekday">Sunday</div>
            </div>

			<div class="calendar-days" id="calendarDays">
				<!-- Calendar days will be generated here by JavaScript -->
			</div>
		</div> 
	</div>  
	 	<div class="conditions-container">
            <div class="conditions-header">
					<h3 id="conditionsTitle"></h3>
					<p id="conditionsDescription">Please enter the specific times you will be unavailable as well as the reason if you are able to.</p>
            </div>

			<div class="conditions-items" id="conditionsItems">
				<button id="addConditionBtn" class="add-condition-btn">
					+ Add Absent Period
				</button>

				<div id="conditionForm" class="condition-form">
					<input type="time" id="conditionStart" class="condition-input" value="09:00">
					<input type="time" id="conditionEnd" class="condition-input" value="17:00">
					<input type="text" id="conditionReason" class="condition-input" placeholder="Enter Reason: e.g. Dentist, Morning only" required>
					<button id="saveConditionBtn" class="save-btn">Add</button>
				</div>

				<div id="conditionsList" class="conditions-list">
					<!-- Conditions will be saved here -->
				</div>
            </div>
        </div>
        <button id="saveAllBtn" class="save-all-changes">Save All Changes</button>
    <script src="availability.js"></script> 
</body>
</html>