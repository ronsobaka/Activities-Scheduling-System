<?php
    session_start();
    require_once '../globalFunctions.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
            echo json_encode([
                "success" => false,
                "message" => "Security token invalid. Please refresh the page."
            ]);
            exit;
        }
    }

    header("Content-Type: application/json");

    $connection = new mysqli("localhost", "root", "", "finalproject");

    if ($connection->connect_error) {
        echo json_encode([
            "success" => false,
            "message" => "Database connection failed"
        ]);
        exit;
    }

    // ---- BRUTE FORCE PROTECTION ----
    $ip = $_SERVER['REMOTE_ADDR'];
    $maxAttempts = 5;
    $lockoutMinutes = 15;

    // Clear attempts older than 15 minutes
    $cleanup = $connection->prepare("DELETE FROM login_attempts WHERE ip_address = ? AND attempted_at < NOW() - INTERVAL ? MINUTE");
    $cleanup->bind_param("si", $ip, $lockoutMinutes);
    $cleanup->execute();

    // Count recent attempts
    $checkAttempts = $connection->prepare("SELECT COUNT(*) as attempts FROM login_attempts WHERE ip_address = ?");
    $checkAttempts->bind_param("s", $ip);
    $checkAttempts->execute();
    $attemptsResult = $checkAttempts->get_result()->fetch_assoc();

    if ($attemptsResult['attempts'] >= $maxAttempts) {
        echo json_encode([
            "success" => false,
            "message" => "Too many failed login attempts. Please try again in {$lockoutMinutes} minutes."
        ]);
        exit;
    }
    // ---- END BRUTE FORCE CHECK ----



    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode([
            "success" => false,
            "message" => "Please fill in all fields"
        ]);
        exit;
    }

    $stmt = $connection->prepare("SELECT userID, roleID, password, firstName, status FROM `user` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Log failed attempt
        $log = $connection->prepare("INSERT INTO login_attempts (ip_address) VALUES (?)");
        $log->bind_param("s", $ip);
        $log->execute();

        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password. You have " . ($maxAttempts - $attemptsResult['attempts'] - 1) . " attempts left before lockout."
        ]);
        exit;
    }

    $row = $result->fetch_assoc();

    if ($row['status'] === 'pending') {
        echo json_encode([
            "success" => false,
            "message" => "Please verify your email address before logging in. Check your inbox."
        ]);
        exit;
    }

    if ($row['status'] === 'email_verified') {
        echo json_encode([
            "success" => false,
            "message" => "Your email is verified but your account is awaiting manager approval."
        ]);
        exit;
    }

    if ($row['status'] !== 'active') {
        echo json_encode([
            "success" => false,
            "message" => "Your account has been disabled. Please contact your manager."
        ]);
        exit;
    }

    if (!password_verify($password, $row['password'])) {
        // Log failed attempt
        $log = $connection->prepare("INSERT INTO login_attempts (ip_address) VALUES (?)");
        $log->bind_param("s", $ip);
        $log->execute();

        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password. You have " . ($maxAttempts - $attemptsResult['attempts'] - 1) . " attempts left before lockout."
        ]);
        exit;
    }

    // Success — clear this IP's attempts
    $clear = $connection->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
    $clear->bind_param("s", $ip);
    $clear->execute();

    session_regenerate_id(true);

    $_SESSION['userID'] = $row['userID'];
    $_SESSION['loggedIn'] = true;
    $_SESSION['roleID'] = $row['roleID'];
    $_SESSION['firstName'] = $row['firstName'];
    $_SESSION['isManager'] = ($row['roleID'] == 1 || $row['roleID'] == 2);

    echo json_encode([
        "success" => true,
        "message" => "Login Successful! Logging you in..."
    ]);
?>