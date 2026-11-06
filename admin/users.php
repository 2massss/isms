
<?php
require_once "../includes/auth.php";
requireRole("admin");

require_once "../config/db.php";

$pageTitle = "User Management";

$search = trim($_GET["search"] ?? "");

if ($search !== "") {

    $stmt = $conn->prepare(
        "SELECT id, name, username, role, status
         FROM users
         WHERE name LIKE ? OR username LIKE ?
         ORDER BY id DESC"
    );

    $term = "%$search%";
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();

    $users = $stmt->get_result();

} else {

    $users = $conn->query(
        "SELECT id, name, username, role, status
         FROM users
         ORDER BY id DESC"
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management | ISMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="content">

    <h1>User Management</h1>
    <p>Manage system administrators and staff accounts.</p>

    <a href="user_add.php" class="btn">
        + Add User
    </a>

    <br><br>

    <form method="GET">
        <input
            type="text"
            name="search"
            placeholder="Search name or username"
            value="<?= htmlspecialchars($search) ?>"
        >
        <button class="btn" type="submit">Search</button>
    </form>

    <br>

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($users && $users->num_rows > 0): ?>

                    <?php while ($user = $users->fetch_assoc()): ?>

                        <tr>
                            <td><?= (int)$user["id"] ?></td>

                            <td>
                                <?= htmlspecialchars($user["name"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($user["username"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(ucfirst($user["role"])) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(ucfirst($user["status"])) ?>
                            </td>

                            <td>
                                <a href="user_edit.php?id=<?= (int)$user["id"] ?>">
                                    Edit
                                </a>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6">No users found.</td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<script src="../assets/js/app.js"></script>

</body>
</html>
