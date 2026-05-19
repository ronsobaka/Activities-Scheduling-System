<?php
require_once '../../globalFunctions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../main/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Forgot Password</title>
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
                        <h4>Forgot Your Password?</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>
                        
                        <form id="forgotPasswordForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <a href="../loginHTML.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="forgotPassword.js"></script>
</body>
</html>