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
?>