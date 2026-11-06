
<?php
session_start();
require_once "../config/db.php";

// Redirect if already logged in
if (isset($_SESSION["id"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: ../admin/dash.php");
    } else {
        header("Location: ../staff/dash.php");
    }
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please fill in all fields.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, username, password, role
             FROM users
             WHERE username = ? AND status = 'active'
             LIMIT 1"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user["password"])) {

            session_regenerate_id(true);

            $_SESSION["id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "admin") {
                header("Location: ../admin/dash.php");
            } elseif ($user["role"] === "staff") {
                header("Location: ../staff/dash.php");
            } else {
                session_unset();
                session_destroy();
                header("Location: login.php");
            }

            exit;

        } else {
            $error = "Invalid username or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | ISMS</title>

<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="login-page">

    <div class="login-box">

        <div class="login-icon">ISMS</div>

        <h2>Welcome Back!</h2>

        <p>Inventory and Supply Management System</p>

        <?php if ($error !== ""): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <label>Username</label>

            <input
                type="text"
                name="username"
                placeholder="Enter username"
                required
                autofocus
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter password"
                required
            >

            <button type="submit" class="btn">
                Login
            </button>

        </form>

        <div class="login-footer">
            ISMS &copy; <?= date("Y") ?>
        </div>

    </div>
  <script src="../assets/js/app.js"></script>
</body>
</html>
