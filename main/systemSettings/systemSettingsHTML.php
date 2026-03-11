<?php
    session_start();
    require_once '../../globalFunctions.php';
    if ($_SESSION['roleID'] !== 1) {
        header("Location: ../../login/loginHTML.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../main.css">
    <!-- Adding bootstrap5 CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="topnav">
        <a href="../mainHTML.php">Home</a>
        <a href="../availability/availabilityHTML.php">Availability</a>
        <?php if ($_SESSION['isManager']): ?>
            <a href="scheduleManager/scheduleManagerHTML.php">Schedule Manager</a>
            <div class="dropdown">
            <button class="dropBtn">Manage ▼</button>
            <div class="dropdown-content">
                <a href="../approveStaff/approveStaffHTML.php">Approve Staff</a>
                <a href="systemSettingsHTML.php" class="active">System Settings</a>
            </div>
        </div> 
        <?php endif; ?>
        <a href="#about">Upcoming shifts</a>
    </div>
    <div class="divider"></div>
    <div class="title">
        <h1>System Settings</h1>
    </div>

    <div class="container mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action active">User Management</a>
                <a href="#" class="list-group-item list-group-item-action">Role Management</a>
                <a href="#" class="list-group-item list-group-item-action">Permissions</a>
                <a href="#" class="list-group-item list-group-item-action">System Settings</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4>User Management</h4>
                </div>
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">🔍</span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email...">
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Your user data here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="systemSettings.js"></script>
</body>
</html>
