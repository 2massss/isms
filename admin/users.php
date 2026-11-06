
<?php
require_once "../includes/auth.php";
requireRole("admin");

require_once "../config/db.php";

$pageTitle = "User Management";

// Search
$search = trim($_GET["search"] ?? "");

// Fetch users
if ($search !== "") {

    $stmt = $conn->prepare(
        "SELECT id, name, username, role, status
         FROM users
         WHERE name LIKE ? OR username LIKE ?
         ORDER BY id DESC"
    );

    $term = "%" . $search . "%";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle) ?> | ISMS</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="content">

    <h1>User Management</h1>

    <p>Manage system administrators and staff accounts.</p>

    <!-- SUCCESS MESSAGES -->

    <?php if (isset($_GET["added"])): ?>
        <div class="alert alert-success">
            User created successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["updated"])): ?>
        <div class="alert alert-success">
            User updated successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["deleted"])): ?>
        <div class="alert alert-success">
            User deleted successfully!
        </div>
    <?php endif; ?>

    <!-- ADD USER -->

    <a href="user_add.php" class="btn">
        + Add User
    </a>

    <br><br>

    <!-- SEARCH -->

    <form method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search name or username"
            value="<?= htmlspecialchars($search) ?>"
        >

        <br><br>

        <button type="submit" class="btn">
            Search
        </button>

        <a href="users.php" class="btn btn-danger">
            Clear
        </a>

    </form>

    <br>

    <!-- USER TABLE -->

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

                            <td>
                                <?= (int)$user["id"] ?>
                            </td>

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

                                <a
                                    href="user_edit.php?id=<?= (int)$user["id"] ?>"
                                    class="btn"
                                >
                                    Edit
                                </a>

                                <?php if ((int)$user["id"] !== (int)$_SESSION["id"]): ?>

                                    <form
                                        action="user_del.php"
                                        method="POST"
                                        style="display:inline"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$user["id"] ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-danger"
                                        >
                                            Delete
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6">
                            No users found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<script src="../assets/js/app.js"></script>

</body>
</html>
