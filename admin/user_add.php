
<?php
require_once "../includes/auth.php";
requireRole("admin");
require_once "../config/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "staff";

    if ($name === "" || $username === "" || $password === "") {
        $error = "Please complete all fields.";
    } elseif (!in_array($role, ["admin", "staff"], true)) {
        $error = "Invalid user role.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE username = ?"
        );
        $check->bind_param("s", $username);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $error = "Username already exists.";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (name, username, password, role, status)
                 VALUES (?, ?, ?, ?, 'active')"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $username,
                $hashed,
                $role
            );

            if ($stmt->execute()) {
                header("Location: users.php?added=1");
                exit;
            } else {
                $error = "Unable to create user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User | ISMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="content">

    <h1>Add New User</h1>
    <p>Create an administrator or staff account.</p>

    <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <form method="POST">

            <label>Full Name</label>
            <input type="text" name="name" required>

            <br><br>

            <label>Username</label>
            <input type="text" name="username" required>

            <br><br>

            <label>Password</label>
            <input type="password" name="password" minlength="8" required>

            <br><br>

            <label>User Role</label>
            <select name="role" required>
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>

            <br><br>

            <button type="submit" class="btn">
                Create User
            </button>

            <a href="users.php" class="btn btn-danger">
                Cancel
            </a>

        </form>

    </div>

</div>

<script src="../assets/js/app.js"></script>
</body>
</html>
