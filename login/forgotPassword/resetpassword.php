<?php
session_start();
require_once '../../globalFunctions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$token = $_POST['token'] ?? '';
$newPassword = $_POST['password'] ?? '';

if (empty($token) || empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

if (strlen($newPassword) < 8 || strlen($newPassword) > 16) {
    echo json_encode(['success' => false, 'message' => 'Password must be between 8 and 16 characters']);
    exit();
}

if (!preg_match("/[0-9]/", $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one number']);
    exit();
}

if (!preg_match("/[A-Z]/", $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one uppercase letter']);
    exit();
}

if (!preg_match("/[a-z]/", $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one lowercase letter']);
    exit();
}

if (!preg_match('/[^a-zA-Z0-9]/', $newPassword)) {
    echo json_encode(['success' => false, 'message' => 'Password must contain at least one special character']);
    exit();
}

$connection = getDBConnection();

$query = "SELECT userID FROM passwordresets WHERE token = ? AND expires_at > NOW()";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired reset link']);
    exit();
}

$row = $result->fetch_assoc();
$userID = $row['userID'];

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$updateQuery = "UPDATE user SET password = ? WHERE userID = ?";
$updateStmt = $connection->prepare($updateQuery);
$updateStmt->bind_param("si", $hashedPassword, $userID);
$updateStmt->execute();

$deleteQuery = "DELETE FROM passwordresets WHERE token = ?";
$deleteStmt = $connection->prepare($deleteQuery);
$deleteStmt->bind_param("s", $token);
$deleteStmt->execute();

echo json_encode(['success' => true, 'message' => 'Password reset successful']);
?>