
<?php

session_start();

if (!isset($_SESSION["id"])) {
    header("Location: auth/login.php");
    exit;
}

if ($_SESSION["role"] === "admin") {
    header("Location: admin/dash.php");
} else {
    header("Location: staff/dash.php");
}

exit;

?>
