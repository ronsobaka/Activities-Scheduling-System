<?php
    session_start();
    require_once '../globalFunctions.php';
    require_once '../PHPMailer/src/Exception.php';
    require_once '../PHPMailer/src/PHPMailer.php';
    require_once '../PHPMailer/src/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

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

    function fail($message) {
        echo json_encode([
            "success" => false,
            "message" => $message
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        exit;
    }

    if ($password !== $password2) fail("Passwords do not match!");
    if (strlen($password) < 8 || strlen($password) > 16) fail("Password must be between 8 and 16 characters!");
    if (!preg_match("/[0-9]/", $password)) fail("Password must contain at least 1 number!");
    if (!preg_match("/[A-Z]/", $password)) fail("Password must contain at least 1 uppercase letter!");
    if (!preg_match("/[a-z]/", $password)) fail("Password must contain at least 1 lowercase letter!");
    if (!preg_match('/[^a-zA-Z0-9]/', $password)) fail("Password must contain at least one special character!");

    $hashedPass = password_hash($password, PASSWORD_DEFAULT);
    $status = "pending";
    $roleID = 3;
    $stmt->close();
    $stmt = $connection->prepare("INSERT INTO `user` (email, password, roleID, status, firstName, lastName) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("ssisss", $email, $hashedPass, $roleID, $status, $firstName, $lastName);

    if (!$stmt->execute()) {
        echo json_encode([
            "success" => false,
            "message" => "Registration failed"
        ]);
        exit;
    }

    $userID = $connection->insert_id;

    // Generate verification token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $verifyStmt = $connection->prepare("INSERT INTO email_verifications (userID, token, expires_at) VALUES (?, ?, ?)");
    $verifyStmt->bind_param("iss", $userID, $token, $expires);
    $verifyStmt->execute();

    $verifyLink = "http://" . $_SERVER['HTTP_HOST'] . "/finalProject/Registration/verifyEmail.php?token=" . $token;

    // Send verification email
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
        $mail->Subject = 'Verify your email address';
        $mail->isHTML(true);
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto;'>
                <h2 style='background: linear-gradient(to right, #7aa9b8, #1c0696); color: white; padding: 20px; text-align: center;'>
                    Staff Scheduling
                </h2>
                <div style='padding: 20px;'>
                    <p>Hi {$firstName},</p>
                    <p>Thanks for registering! Please verify your email address by clicking the button below.</p>
                    <p>This link expires in <strong>24 hours</strong>.</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$verifyLink}' 
                           style='background-color: #040d8a; color: white; padding: 12px 30px; 
                                  text-decoration: none; border-radius: 5px; font-size: 1em;'>
                            Verify Email
                        </a>
                    </div>
                    <p style='color: #888; font-size: 0.85em;'>
                        If you didn't register for this account, you can safely ignore this email.
                    </p>
                </div>
            </div>
        ";

        $mail->send();

        echo json_encode([
            "success" => true,
            "message" => "Registration successful! Please check your email to verify your account before logging in."
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    } catch (Exception $e) {
        // Registration succeeded but email failed - still let them register
        error_log("Verification email failed: " . $e->getMessage());
        echo json_encode([
            "success" => true,
            "message" => "Registration successful! We couldn't send a verification email - please contact your manager."
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
?>