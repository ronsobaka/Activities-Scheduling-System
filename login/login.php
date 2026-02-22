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

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode([
            "success" => false,
            "message" => "Please fill in all fields"
        ]);
        exit;
    }

    $stmt = $connection->prepare("SELECT userID, roleID, password, firstName FROM `user` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password"
        ]);
        exit;
    }   

    $row = $result->fetch_assoc();

    if (!password_verify($password, $row['password'])) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password"
        ]);
        exit;
    }

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