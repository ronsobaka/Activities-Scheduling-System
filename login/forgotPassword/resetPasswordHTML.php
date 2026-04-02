<?php
require_once '../../globalFunctions.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: ../loginHTML.php");
    exit();
}

$connection = getDBConnection();

$query = "SELECT userID FROM passwordresets WHERE token = ? AND expires_at > NOW()";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

$validToken = ($result->num_rows > 0);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Reset Password</title>
</head>
<body>
    <div class="divider"></div>
    <div class="title">
        <h1>Reset Password</h1>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Create New Password</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$validToken): ?>
                            <div class="alert alert-danger">Invalid or expired reset link. Please request a new one.</div>
                            <a href="forgotPasswordHTML.php" class="btn btn-primary">Request New Link</a>
                        <?php else: ?>
                            <form id="resetPasswordForm">
                                <input type="hidden" id="token" value="<?php echo htmlspecialchars($token); ?>">
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" required>
                                    <small class="text-muted">8-16 characters, with number, uppercase, lowercase, and special character.</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="resetPassword.js"></script>
</body>
</html>