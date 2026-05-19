<?php
session_start();
require_once '../../globalFunctions.php';
require_once '../../PHPMailer/src/Exception.php';
require_once '../../PHPMailer/src/PHPMailer.php';
require_once '../../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

// Always return success even if email not found - prevents email enumeration
if ($result->num_rows === 0) {
    echo json_encode(['success' => true, 'message' => 'If that email exists, a reset link has been sent.']);
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

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ronniejf2004@gmail.com';
    $mail->Password   = 'mljh zisl lemg knrt';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('ronniejf2004@gmail.com', 'Staff Scheduling');
    $mail->addAddress($email);
    $mail->Subject = 'Password Reset Request';
    $mail->isHTML(true);
    $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto;'>
            <h2 style='background: linear-gradient(to right, #7aa9b8, #1c0696); color: white; padding: 20px; text-align: center;'>
                Staff Scheduling
            </h2>
            <div style='padding: 20px;'>
                <p>Hi,</p>
                <p>You requested a password reset. Click the button below to create a new password.</p>
                <p>This link expires in <strong>1 hour</strong>.</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetLink}' 
                       style='background-color: #040d8a; color: white; padding: 12px 30px; 
                              text-decoration: none; border-radius: 5px; font-size: 1em;'>
                        Reset Password
                    </a>
                </div>
                <p style='color: #888; font-size: 0.85em;'>
                    If you didn't request this, you can safely ignore this email.
                </p>
            </div>
        </div>
    ";

    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'If that email exists, a reset link has been sent.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Email could not be sent. Please try again later.'
    ]);
}
?>