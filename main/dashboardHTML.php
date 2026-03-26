<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard - Staff Scheduling</title>
</head>
<body>
    <div class="topnav">
        <a href="mainHTML.php" class="active">Home</a>
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
        <a href="#about">Upcoming shifts</a>
    </div>
    <div class="divider"></div>
    <div class="title">
        <h1>Dashboard</h1>
    </div>

    <div class="container mt-4">
        <div class="row mb-4">
            <!-- Welcome Card -->
            <div class="col-md-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3>Welcome back, <?php echo htmlspecialchars($_SESSION['firstName']); ?>!</h3>
                        <p class="mb-0">Here's what's happening today.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h1 class="display-4" id="upcomingShiftsCount">-</h1>
                        <p class="text-muted">Upcoming Shifts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h1 class="display-4" id="totalStaffCount">-</h1>
                        <p class="text-muted">Total Staff</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h1 class="display-4" id="pendingApprovalsCount">-</h1>
                        <p class="text-muted">Pending Approvals</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h1 class="display-4" id="activitiesThisWeek">-</h1>
                        <p class="text-muted">Activities This Week</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Shifts Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Your Upcoming Shifts</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Activity</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                 </tr>
                            </thead>
                            <tbody id="upcomingShiftsTable">
                                 <tr><td colspan="4" class="text-center">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="dashboard.js"></script>
</body>
</html>