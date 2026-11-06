
<?php
require_once "../includes/auth.php";
requireRole("admin");
require_once "../config/db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    exit("Invalid user ID.");
}

$stmt = $conn->prepare(
    "SELECT id, name, username, role, status
     FROM users WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    exit("User not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $role = $_POST["role"] ?? "";
    $status = $_POST["status"] ?? "";

    if ($name === "" || $username === "") {
        $error = "Please complete all fields.";
    } elseif (!in_array($role, ["admin", "staff"], true)) {
        $error = "Invalid role.";
    } elseif (!in_array($status, ["active", "inactive"], true)) {
        $error = "Invalid status.";
    } else {

        // Prevent duplicate usernames
        $check = $conn->prepare(
            "SELECT id FROM users
             WHERE username = ? AND id != ?"
        );
        $check->bind_param("si", $username, $id);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $error = "Username already exists.";
        } else {

            // Prevent administrator from removing own admin access
            if (
                $id == $_SESSION["id"] &&
                ($role !== "admin" || $status !== "active")
            ) {
                $error = "You cannot remove your own active admin access.";
            } else {

                $update = $conn->prepare(
                    "UPDATE users
                     SET name = ?, username = ?, role = ?, status = ?
                     WHERE id = ?"
                );

                $update->bind_param(
                    "ssssi",
                    $name,
                    $username,
                    $role,
                    $status,
                    $id
                );

                if ($update->execute()) {
                    header("Location: users.php?updated=1");
                    exit;
                } else {
                    $error = "Unable to update user.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User | ISMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="content">

    <h1>Edit User</h1>

    <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <form method="POST">

            <label>Full Name</label>
            <input
                type="text"
                name="name"
                value="<?= htmlspecialchars($user["name"]) ?>"
                required
            >

            <br><br>

            <label>Username</label>
            <input
                type="text"
                name="username"
                value="<?= htmlspecialchars($user["username"]) ?>"
                required
            >

            <br><br>

            <label>Role</label>
            <select name="role">
                <option value="admin" <?= $user["role"] === "admin" ? "selected" : "" ?>>Admin</option>
                <option value="staff" <?= $user["role"] === "staff" ? "selected" : "" ?>>Staff</option>
            </select>

            <br><br>

            <label>Status</label>
            <select name="status">
                <option value="active" <?= $user["status"] === "active" ? "selected" : "" ?>>Active</option>
                <option value="inactive" <?= $user["status"] === "inactive" ? "selected" : "" ?>>Inactive</option>
            </select>

            <br><br>

            <button type="submit" class="btn">
                Save Changes
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
