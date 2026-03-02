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

        <div id="activityModal" class="activity-modal">
            <div class="activity-modal-content">
                <div class="activity-modal-header">
                    <h3 id="modalDateTitle">March 15, 2026</h3>
                    <button id="closeActivityModal" class="close-modal">&times;</button>
                </div>
                
                <div class="activity-modal-body">
                    <div class="activities-list" id="activitiesList">
                        <!-- Activities will be listed here -->
                        <p class="no-activities">No activities scheduled for this day.</p>
                    </div>
                    
                    <button id="addActivityBtn" class="add-activity-btn">+ Add Activity</button>
                    
                    <!-- Add Activity Form (hidden by default) -->
                    <div id="activityForm" class="activity-form" style="display: none;">
                        <h4>New Activity</h4>
                        
                        <label>Activity Name:</label>
                        <input type="text" id="activityName" placeholder="e.g., Team Meeting">
                        
                        <label>Start Time:</label>
                        <input type="time" id="activityStart" value="09:00">
                        
                        <label>End Time:</label>
                        <input type="time" id="activityEnd" value="17:00">

                        <label>Location:</label>
                        <input type="text" id="activityLocation" placeholder="e.g., Room 101">
                        
                        <label>Equipment:</label>
                        <input type="text" id="activityEquipment" placeholder="e.g., Projector, Chairs">
                        
                        <label>Notes:</label>
                        <textarea id="activityNotes" rows="3"></textarea>

                        <button id="assignStaffBtn" class="assign-staff-btn">Assign Staff</button>
                        <div id= "selectedStaffContainer" class="selected-staff-container">
                            <h4>Assigned Staff:</h4>
                            <div id="selectedStaffView" class="selected-staff-list">
                                <!-- Selected staff will be displayed here -->
                            </div>
                        </div>
                        <div class="form-buttons">
                            <button id="saveActivityBtn" class="save-btn">Save Activity</button>
                            <button id="cancelActivityBtn" class="cancel-btn">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="staffAssignmentModal" class="staff-assignment-modal" style="display: none;">
            <div class="staff-assignment-modal-header">
                <h3 id="staffAssignmentTitle">Assign Staff</h3>
                <button id="closeStaffAssignmentModal" class="close-modal">&times;</button>
            </div>

            <div class="staff-assignment-modal-content">
                <div id="staffListContainer" class="staff-list-container">
    
                    <div id="selectedStaffList" class="selected-staff-list">
                        <!-- Selected staff will be displayed here -->
                    </div>

                    <div id="availableStaffList" class="available-staff-list">
                        <!-- Available staff will be listed here -->
                    </div>

                    <div id="conditionedStaffList" class="unavailable-staff-list">
                        <!-- Unavailable staff will be listed here -->
                    </div>

                    <div id="unavailableStaffList" class="unavailable-staff-list">
                        <!-- Unavailable staff will be listed here -->
                    </div>
                </div>

                <div class="form-buttons">
                    <button id="saveStaffAssignments" class="save-btn">Save Assignments</button>
                    <button id="cancelStaffBtn" class="cancel-btn">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script src="scheduleManager.js"></script>
</body>
</html>