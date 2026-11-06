
<?php
$pageTitle = $pageTitle ?? "ISMS";
$role = $_SESSION["role"] ?? "";
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

<div class="dashboard">

    <header class="topbar">

        <h2>ISMS</h2>

        <div class="topbar-user">
            <span>
                <?= htmlspecialchars($_SESSION["name"] ?? "User") ?>
            </span>

            <span class="role-label">
                <?= htmlspecialchars(ucfirst($role)) ?>
            </span>

            <a href="../auth/logout.php" class="logout">
                Logout
            </a>
        </div>

    </header>
</div>
</body>
</html>
