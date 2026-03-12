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
                <a href="#" class="list-group-item list-group-item-action active">Staff Management</a>
                <a href="#" class="list-group-item list-group-item-action">Role Management</a>
                <a href="#" class="list-group-item list-group-item-action">Permissions</a>
                <a href="#" class="list-group-item list-group-item-action">System Settings</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4>Staff Management</h4>
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

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editUserForm">
                                        <input type="hidden" id="editUserID">
                                        
                                        <div class="mb-3">
                                            <label for="editFirstName" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="editFirstName" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="editLastName" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="editLastName" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="editEmail" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="editEmail" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="editRole" class="form-label">Role</label>
                                            <select class="form-select" id="editRole">
                                                <option value="">Select Role</option>
                                                <!-- Roles will be loaded dynamically -->
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="editStatus" class="form-label">Status</label>
                                            <select class="form-select" id="editStatus">
                                                <option value="active">Active</option>
                                                <option value="disabled">Disabled</option>
                                                <option value="pending">Pending</option>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="saveUserChanges()">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="systemSettings.js"></script>
</body>
</html>
