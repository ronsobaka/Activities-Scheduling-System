<?php
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
    ]);

    $timeout = 30 * 60;
    if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $timeout) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
    $_SESSION['last_activity'] = time();

    if (!isset($_SESSION['userID']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
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
    include "mainHTML.php";
?>