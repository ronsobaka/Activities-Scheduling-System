<?php
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
    ]);
    require_once '../globalFunctions.php';
    $timeout = 30 * 60;

    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $timeout) {
        session_destroy();
        header("Location: ../login/loginHTML.php");
        exit;
    }
    $_SESSION['last_activity'] = time();

    if (!isset($_SESSION['userID']) || $_SESSION['loggedIn'] !== true) {
        header("Location: ../login/loginHTML.php");
        exit;
    }

    $connection = new mysqli("localhost", "root", "", "finalproject");
    if ($connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }

    $userID = $_SESSION['userID'];
    $stmt = $connection->prepare("SELECT firstName, lastName, roleID, status FROM user WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user['status'] != 'active') {
        session_destroy();
        header("Location: ../login/loginHTML.php?error=not_active");
        exit;
    }


    $isManager = ($user['roleID'] == 1 || $user['roleID'] == 2);
    $isStaff = ($user['roleID'] == 3);
    include "dashboardHTML.php";
?>