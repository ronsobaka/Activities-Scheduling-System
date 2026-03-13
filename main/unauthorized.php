<?php
session_start();
require_once 'globalFunctions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="topnav">
        <a href="mainHTML.php">Home</a>
        <a href="availability/availabilityHTML.php">Availability</a>
        <?php if (isset($_SESSION['isManager']) && $_SESSION['isManager']): ?>
            <a href="scheduleManager/scheduleManagerHTML.php">Schedule Manager</a>
        <?php endif; ?>
    </div>
    <div class="divider"></div>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-header bg-danger text-white">
                        <h4>Access Denied</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <span style="font-size: 80px;">🚫</span>
                        </div>
                        <h5 class="card-title">You don't have permission to access this page</h5>
                        <p class="card-text text-muted mb-4">
                            If you believe this is a mistake, please contact your system administrator.
                        </p>
                        <a href="mainHTML.php" class="btn btn-primary">Return to Dashboard</a>
                    </div>
                    <div class="card-footer text-muted">
                        <?php if (isset($_SESSION['firstName'])): ?>
                            Logged in as: <?php echo htmlspecialchars($_SESSION['firstName']); ?> 
                            (Role: <?php echo $_SESSION['roleID'] ?? 'Unknown'; ?>)
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>