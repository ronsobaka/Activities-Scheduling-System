<?php
    session_start();
    require_once '../../globalFunctions.php';

    // Check if user is logged in
    if (!isAuthenticated()) {
        header("Location: ../login/loginHTML.php");
        exit();
    }

    // Check if user has permission
    if (!canAccess($_SESSION['roleID'], 'Approve Staff')) {
        header("Location: ../unauthorized.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Approve Staff - Staff Scheduling System</title>
</head>
<body>
    <div class="topnav">
        <a href="../mainHTML.php">Home</a>
        <a href="../availability/availabilityHTML.php">Availability</a>
        <?php if ($_SESSION['isManager']): ?>
            <a href="../scheduleManager/scheduleManagerHTML.php">Schedule Manager</a>
            <div class="dropdown">
                <button class="dropBtn">Manage ▼</button>
                <div class="dropdown-content">
                    <a href="approveStaffHTML.php" class="active">Approve Staff</a>
                    <a href="../systemSettings/systemSettingsHTML.php">System Settings</a>
                </div>
            </div> 
        <?php endif; ?>
    </div>
    <div class="divider"></div>
    <div class="title">
        <h1>Staff Approval</h1>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Pending Staff Registrations</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="pendingStaffTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="approveStaff.js"></script>
</body>
</html>