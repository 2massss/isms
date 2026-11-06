
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireRole($role) {

    if (!isset($_SESSION["id"])) {
        header("Location: ../auth/login.php");
        exit;
    }

    if ($_SESSION["role"] !== $role) {
        http_response_code(403);
        exit("403 - Access denied.");
    }
}

?>
