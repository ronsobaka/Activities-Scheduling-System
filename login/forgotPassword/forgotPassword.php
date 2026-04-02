<?php
    session_start();
    require_once '../../globalFunctions.php';

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }

    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit();
    }

    $connection = getDBConnection();

    $query = "SELECT userID FROM user WHERE email = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Email not found']);
        exit();
    }

    $user = $result->fetch_assoc();
    $userID = $user['userID'];

    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $deleteQuery = "DELETE FROM passwordresets WHERE userID = ?";
    $deleteStmt = $connection->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $userID);
    $deleteStmt->execute();

    $insertQuery = "INSERT INTO passwordresets (userID, token, expires_at) VALUES (?, ?, ?)";
    $insertStmt = $connection->prepare($insertQuery);
    $insertStmt->bind_param("iss", $userID, $token, $expires);
    $insertStmt->execute();

    $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/finalProject/login/forgotPassword/resetPasswordHTML.php?token=" . $token;

    echo json_encode([
        'success' => true,
        'message' => 'Reset link generated (testing mode)',
        'reset_link' => $resetLink
    ]);
?>