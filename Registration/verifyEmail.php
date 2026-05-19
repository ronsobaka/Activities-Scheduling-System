<?php
session_start();
require_once '../globalFunctions.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: ../login/loginHTML.php");
    exit();
}

$connection = getDBConnection();

// Check token is valid and not expired
$stmt = $connection->prepare("SELECT userID FROM email_verifications WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = "This verification link is invalid or has expired.";
} else {
    $row = $result->fetch_assoc();
    $userID = $row['userID'];

    // Update user status to email_verified
    $updateStmt = $connection->prepare("UPDATE `user` SET status = 'email_verified' WHERE userID = ? AND status = 'pending'");
    $updateStmt->bind_param("i", $userID);
    $updateStmt->execute();

    // Delete the used token
    $deleteStmt = $connection->prepare("DELETE FROM email_verifications WHERE token = ?");
    $deleteStmt->bind_param("s", $token);
    $deleteStmt->execute();

    $success = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Email Verification</title>
</head>
<body>
    <div class="title" style="background: linear-gradient(to right, #7aa9b8, #1c0696); color: white; padding: 20px; text-align: center;">
        <h1>Staff Scheduling</h1>
    </div>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <?php if (isset($success)): ?>
                            <div style="font-size: 4em;">✅</div>
                            <h3 class="mt-3">Email Verified!</h3>
                            <p class="text-muted">Your email has been verified successfully. Your account is now pending manager approval — you'll be able to log in once approved.</p>
                            <a href="../login/loginHTML.php" class="btn btn-primary mt-3">Back to Login</a>
                        <?php else: ?>
                            <div style="font-size: 4em;">❌</div>
                            <h3 class="mt-3">Verification Failed</h3>
                            <p class="text-muted"><?php echo htmlspecialchars($error); ?></p>
                            <a href="registerHTML.php" class="btn btn-primary mt-3">Register Again</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>