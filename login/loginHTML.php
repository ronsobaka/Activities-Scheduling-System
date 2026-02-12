<?php
    session_start();
    require_once '../globalFunctions.php';
    $csrfToken = generateCSRFToken();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="title">
        <h1>Staff Scheduling Login Page</h1>
    </div>
    <div id="login-container">

        <h2>Please login to begin</h2>

        <form id="loginForm">

            <?php echo csrfField(); ?>  

            <label>Email:</label>
            <input type="text" id="email" name="email" autocomplete="email" required>

            <label>Password:</label>
            <input type="password" id="password" name="password" autocomplete="current-password">

            <p id="loginMessage"></p>

            <p class="forgot-password">
                <a href="../Forgot Password/forgotPassword.html">Forgot password?</a>
            </p>

            <button id="loginBtn" type="submit">Log in</button>
            
            <p id="register-link">
                Not signed up yet? <a href="../Registration/registerHTML.php">Register Here</a>
            </p>
        </form>
    </div>
    <script src="login.js"></script>
</body>
</html>