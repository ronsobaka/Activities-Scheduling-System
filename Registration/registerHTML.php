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
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <div class="title">
        <h1>Staff Scheduling Registration Page</h1>
    </div>
    <div id="register-container">

        <h2>Please Register to begin</h2>

        <form id="registerForm">

            <?php echo csrfField(); ?>
            <label>Email:</label>
            <input type="text" id="email" name="email" autocomplete="email" required>

            <label>First Name:</label>
            <input type="text" id="firstName" name="firstName" autocomplete="given-name" required>

            <label>Last Name:</label>
            <input type="text" id="lastName" name="LastName" autocomplete="family-name" required>

            <label>Password:</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required>

            <label>Confirm Password:</label>
            <input type="password" id="passwordConfirm" name="passwordConfirm" autocomplete="new-password" required>

            

            <p id="registerMessage"></p>

            <button id="registerBtn" type="submit">Register</button>
        </form>
    </div>
<script src="register.js"></script>
</body>
</html>