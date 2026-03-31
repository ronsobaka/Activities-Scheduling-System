<?php
    session_start();
    require_once '../../globalFunctions.php';
    // Check if user is logged in
    if (!isAuthenticated()) {
        header("Location: ../login/loginHTML.php");
        exit();
    }

    // Check if user has permission for this specific page
    if (!canAccess($_SESSION['roleID'], 'System Settings')) {
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
    <!-- Adding bootstrap5 CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="topnav">
        <a href="../dashboardHTML.php">Home</a>
        <a href="../availability/availabilityHTML.php">Availability</a>
        <?php if ($_SESSION['isManager']): ?>
            <a href="../scheduleManager/scheduleManagerHTML.php">Schedule Manager</a>
            <div class="dropdown">
            <button class="dropBtn">Manage ▼</button>
            <div class="dropdown-content">
                <a href="../approveStaff/approveStaffHTML.php">Approve Staff</a>
                <a href="systemSettingsHTML.php" class="active">System Settings</a>
            </div>
        </div> 
        <?php endif; ?>
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
                <a href="#" class="list-group-item list-group-item-action active" data-card="staff">Staff Management</a>
                <a href="#" class="list-group-item list-group-item-action" data-card="roles">Role Management</a>
                <a href="#" class="list-group-item list-group-item-action" data-card="permissions">Permissions</a>
                <a href="#" class="list-group-item list-group-item-action" data-card="system">System Settings</a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Staff Management Card -->
            <div id="staffManagementCard" class="card">
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
                        <tbody id="usersTableBody">
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

            <!-- Role Management Card -->
            <div id="roleManagementCard" class="card" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Role Management</h4>
                    <button class="btn btn-primary" onclick="showAddRoleModal()">Add New Role</button>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Description</th>
                                <th>Colour</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="rolesTableBody">
                            <!-- Roles will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Add/Edit Role Modal -->
            <div class="modal fade" id="roleModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="roleModalTitle">Add Role</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="roleForm">
                                <input type="hidden" id="roleID">
                                <div class="mb-3">
                                    <label for="roleName" class="form-label">Role Name</label>
                                    <input type="text" class="form-control" id="roleName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="roleDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="roleDescription" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="roleColour" class="form-label">Colour</label>
                                    <input type="color" class="form-control form-control-color" id="roleColour" value="#1c0696">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveRole()">Save Role</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions Card -->
            <div id="permissionsCard" class="card" style="display: none;">
                <div class="card-header">
                    <h4>Role Permissions</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-select" id="permissionRoleSelect">
                                <option value="">Select a role</option>
                                <!-- Roles will be loaded here -->
                            </select>
                        </div>
                    </div>
                    
                    <div id="permissionsTable" style="display: none;">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Feature</th>
                                    <th>Description</th>
                                    <th>Access</th>
                                </tr>
                            </thead>
                            <tbody id="permissionsTableBody">
                                <!-- Permissions will be loaded here -->
                            </tbody>
                        </table>
                        <button class="btn btn-primary" id="savePermissionsBtn"onclick="savePermissions()">Save Permissions</button>
                    </div>
                </div>
            </div>

            <!-- System Settings Card -->
            <div id="systemSettingsCard" class="card" style="display: none;">
                <div class="card-header">
                    <h4>System Settings</h4>
                </div>
                <div class="card-body">
                    <form id="systemSettingsForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="siteName" class="form-label">Site Name</label>
                                    <input type="text" class="form-control" id="siteName" value="Staff Scheduling System">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="siteEmail" class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" id="siteEmail" value="admin@example.com">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="defaultRole" class="form-label">Default Role for New Staff</label>
                                    <select class="form-select" id="defaultRole">
                                        <option value="4">Staff</option>
                                        <option value="5">Part Time Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sessionTimeout" class="form-label">Session Timeout (minutes)</label>
                                    <input type="number" class="form-control" id="sessionTimeout" value="30" min="5" max="120">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="dateFormat" class="form-label">Date Format</label>
                                    <select class="form-select" id="dateFormat">
                                        <option value="d/m/Y">DD/MM/YYYY</option>
                                        <option value="m/d/Y">MM/DD/YYYY</option>
                                        <option value="Y-m-d">YYYY-MM-DD</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="allowSelfRegistration" checked>
                                        <label class="form-check-label" for="allowSelfRegistration">Allow Self Registration</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="requireApproval" checked>
                                        <label class="form-check-label" for="requireApproval">Require Admin Approval for New Staff</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="maintenanceMessage" class="form-label">Maintenance Message</label>
                                    <textarea class="form-control" id="maintenanceMessage" rows="2" placeholder="Leave empty if no maintenance..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-primary" id="saveSystemSettingsBtn">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/systemSettings.js"></script>
    <script src="js/userManagement.js"></script>
    <script src="js/roleManagement.js"></script>
    <script src="js/permissionManagement.js"></script>
</body>
</html>