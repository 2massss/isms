
<?php
require_once "../includes/auth.php";
requireRole("admin");

require_once "../config/db.php";

// Count products
$productResult = $conn->query(
    "SELECT COUNT(*) AS total FROM products"
);
$totalProducts = $productResult->fetch_assoc()["total"];

// Count categories
$categoryResult = $conn->query(
    "SELECT COUNT(*) AS total FROM categories"
);
$totalCategories = $categoryResult->fetch_assoc()["total"];

// Count users
$userResult = $conn->query(
    "SELECT COUNT(*) AS total FROM users"
);
$totalUsers = $userResult->fetch_assoc()["total"];

// Count pending requests
$requestResult = $conn->query(
    "SELECT COUNT(*) AS total
     FROM requests WHERE status = 'pending'"
);
$totalRequests = $requestResult->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | ISMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="dashboard">

    <header class="topbar">
        <h2>ISMS Admin</h2>

        <div>
            Welcome,
            <strong>
                <?= htmlspecialchars($_SESSION["name"]) ?>
            </strong>

            <a href="../auth/logout.php" class="logout">
                Logout
            </a>
        </div>
    </header>

    <main class="content">

        <h1>Admin Dashboard</h1>
        <p>Inventory and Supply Management Overview</p>

        <div class="cards">

            <div class="card">
                <h3>Total Products</h3>
                <h2><?= $totalProducts ?></h2>
            </div>

            <div class="card">
                <h3>Categories</h3>
                <h2><?= $totalCategories ?></h2>
            </div>

            <div class="card">
                <h3>Total Users</h3>
                <h2><?= $totalUsers ?></h2>
            </div>

            <div class="card">
                <h3>Pending Requests</h3>
                <h2><?= $totalRequests ?></h2>
            </div>

        </div>

    </main>

</div>
  <script src="../assets/js/app.js"></script>
</body>
</html>
