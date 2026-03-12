    <?php
    session_start();
    require_once '../../../globalFunctions.php';

    if ($_SESSION['roleID'] !== 1) {
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }

    $connection = new mysqli("localhost", "root", "", "finalproject");
    if ($connection->connect_error) {
        die("Database connection failed: " . $connection->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        $query = "SELECT u.userID, u.firstName, u.lastName, u.email, u.status, r.roleName 
        FROM user u 
        LEFT JOIN roles r ON u.roleID = r.roleID 
        ORDER BY u.userID DESC";

        $result = $connection->query($query);
        $users = [];

        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($users);
    } else if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $userID = $data['userID'];
        $newStatus = $data['newStatus'];

        $query = "UPDATE user SET status = ? WHERE userID = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("si", $newStatus, $userID);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $connection->error]);
        }
        $stmt->close();
    }

    $connection->close();
    
?>