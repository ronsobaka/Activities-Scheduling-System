<?php
    function generateCSRFToken() {

        if (empty($_SESSION['csrfToken'])) {
            $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
            $_SESSION['csrfTokenTime'] = time();
        }
        return $_SESSION['csrfToken'];
    }

    function validateCSRFToken($submittedToken) {
        if (empty($submittedToken)) {
            return false;
        }
        if (!isset($_SESSION['csrfToken'])) {
            return false;
        }
        if (!hash_equals($_SESSION['csrfToken'], $submittedToken)) {
            return false;
        }
        if (!isset($_SESSION['csrfTokenTime'])) {
            return false;
        }
        if (time() - $_SESSION['csrfTokenTime'] > 900) {
            return false;
        }
        return true;
    }

    function csrfField() {
        $token = generateCSRFToken();
        return '<input type="hidden" id="csrfToken" name="csrfToken" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Create a function to get database connection
    function getDBConnection() {
        static $connection = null;
        
        if ($connection === null) {
            $connection = new mysqli("localhost", "root", "", "finalproject");
            
            if ($connection->connect_error) {
                die("Database connection failed: " . $connection->connect_error);
            }
        }
        
        return $connection;
    }

    // Check if user is logged in
    function isAuthenticated() {
        return isset($_SESSION['userID']) && isset($_SESSION['roleID']);
    }

    // Check if user has permission for a specific feature
    function canAccess($roleID, $featureName) {
        // Validate inputs
        if (!$roleID || !is_numeric($roleID) || !$featureName) {
            return false;
        }
        
        $connection = getDBConnection(); // Get connection here
        
        $query = "SELECT COUNT(*) as count FROM rolepermissions rp 
                JOIN features f ON rp.featureID = f.featureID 
                WHERE rp.roleID = ? AND f.name = ?";
        $stmt = $connection->prepare($query);
        
        if (!$stmt) {
            return false; // Query failed
        }
        
        $stmt->bind_param("is", $roleID, $featureName);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }


    function getSetting($key) {
        static $settings = null;
        
        if ($settings === null) {
            $connection = getDBConnection();
            $result = $connection->query("SELECT * FROM systemsettings LIMIT 1");
            $settings = $result->fetch_assoc();
        }
        
        return $settings[$key] ?? null;
    }

    // Then create these helper functions:
    function formatDate($date) {
        $format = getSetting('dateFormat') ?: 'd/m/Y';
        return date($format, strtotime($date));
    }

    function showMaintenanceBanner() {
        $message = getSetting('maintenanceMessage');
        if ($message) {
            echo '<div class="alert alert-warning text-center mb-0">⚠️ ' . htmlspecialchars($message) . '</div>';
        }
    }
?>