
<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, name, username, password, role
         FROM users
         WHERE username = ? AND status = 'active'"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        if ($user["role"] === "admin") {
            header("Location: ../admin/dash.php");
        } else {
            header("Location: ../staff/dash.php");
        }

        exit;

    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ISMS Login</title>
    <link rel="stylesheet"
    href="../assets/css/style.css">
</head>
<body>

<div class="login-box">

    <h2>ISMS</h2>
    <p>Inventory and Supply Management System</p>

    <?php if ($error): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <input type="text"
        name="username"
        placeholder="Username"
        required>

        <input type="password"
        name="password"
        placeholder="Password"
        required>

        <button type="submit">Login</button>

    </form>

</div>

</body>
</html>
