<?php
    
    session_start();
    require_once '../globalFunctions.php';

    if ($_SERVER['REQUEST_METHOD']=== 'POST') {
        if (!validateCSRFToken($_POST['csrfToken']?? '')) {
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
    $password2 = $_POST['password2'] ?? '';
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';

    if (empty($email) || empty($password) || empty($password2) || empty($firstName) || empty($lastName)) {
        echo json_encode([
            "success" => false,
            "message" => "Please fill in all fields"
        ]);
        exit;
    }

    //email validaton

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email address"
        ]);
        exit;
    }

    $stmt = $connection->prepare("SELECT userID FROM `user` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Email is already registered!"
        ]);
        exit;
    }

    //password validation

    function fail($message) {
        echo json_encode([
            "success" => false,
            "message" => $message
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit;
    }

    if ($password !== $password2) {
        fail("Passwords do not match!");
    }

    if (strlen($password) < 8 || strlen($password) > 16) {
        fail("Password must be between 8 and 16 characters!");
    }

    if (!preg_match("/[0-9]/", $password)) {
        fail("Password must contain at least 1 number!");
    }

    if (!preg_match("/[A-Z]/", $password)) {
        fail("Password must contain at least 1 uppercase letter!");
    }

    if (!preg_match("/[a-z]/", $password)) {
        fail("Password must contain at least 1 lowercase letter!");
    }

    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        fail("Password must contain at least one special character!");
    }


    //Inserting data

    $hashedPass = password_hash($password, PASSWORD_DEFAULT);
    $status = "pending";
    $roleID = 3;
    $stmt->close();
    $stmt = $connection->prepare("INSERT INTO `user` (email, password, roleID, status, firstName, lastName) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssisss", $email, $hashedPass, $roleID, $status, $firstName, $lastName);
    
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Registration successful! Come back to the login page once you have been approved!"
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Registration failed"
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

?>