<?php
session_start();
require_once '../../globalFunctions.php';

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Not Logged In']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$connection = new mysqli("localhost", "root", "", "finalproject");

if ($connection->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$userID = $_SESSION['userID'];


//getting data from database for JS
$response = ["unavailable" => [], "conditions" => []];
//unavailable dates
$stmt = $connection->prepare("SELECT unavailableDate FROM unavailableDates WHERE userID");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    $date = $row['unavaialableDate'];

    $dateParts = explode("-", $date);
    $dateKey = sprintf("%d-%d-%d", $dateParts[0], intval($dateParts[1]), intval($dateParts[2]));
    $response["unavailable"][$dateKey] = true;
}

//conditions
$stmt = $connection->prepare("SELECT conditionDate, startTime, endTime, reason FROM conditions WHERE userID");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    $condition = $row['conditionDate'];

    $conditionParts = explode("-", $date);
    $conditionKey = sprintf("%d-%d-%d", $conditionParts[0], intval($conditionParts[1]), intval($conditionParts[2]));
    $response["condition"][$conditionKey] = true;
}

$connection->begin_transaction();

try {      //Removing existing data
    $stmt = $connection->prepare("DELETE FROM unavailableDates WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();

    $stmt = $connection->prepare("DELETE FROM conditions WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();

    // Save unavailable dates
    if (!empty($data['unavailable'])) {
        $stmt = $connection->prepare("INSERT INTO unavailableDates (userID, unavailableDate) VALUES (?, ?)");
        foreach (array_keys($data['unavailable']) as $dateKey) {
            $dateParts = explode('-', $dateKey);
            $formattedDate = sprintf("%04d-%02d-%02d", $dateParts[0], $dateParts[1], $dateParts[2]);

            $stmt->bind_param("is", $userID, $formattedDate);
            $stmt->execute();
        }
    }

    if (!empty($data['conditions'])) {
        $stmt = $connection->prepare("INSERT INTO conditions (userID, conditionDate, startTime, endTime, reason) VALUES (?,?,?,?,?");
        foreach(array_keys($data['conditions']) as $dateKey) {
            $dateParts = explode('-', $dateKey);
            $formattedDate = sprintf("%04d-%02d-%02d", $dateParts[0], $dateParts[1], $dateParts[2]);

            $stmt->bind_param("is", $userID, $formattedDate);
            $stmt->execute();
        }
    }
    $connection->commit();
    echo json_encode(['success' => true, 'message' => 'Data saved successfully']);

} catch (Exception $e) {
    $connection->rollback();
    echo json_encode(['success' => false, 'message' => 'Save failed: ' . $e->getMessage()]);
}
?>