
<?php
require_once "../includes/auth.php";
requireRole("staff");

require_once "../config/db.php";

$userId = $_SESSION["id"];

// Count available products
$productResult = $conn->query(
    "SELECT COUNT(*) AS total
     FROM products
     WHERE quantity > 0"
);
$totalProducts = $productResult->fetch_assoc()["total"];

// Count my pending requests
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM requests
     WHERE user_id = ? AND status = 'pending'"
);

$stmt->bind_param("i", $userId);
$stmt->execute();

$pendingRequests = $stmt->get_result()
    ->fetch_assoc()["total"];

// Count my approved requests
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM requests
     WHERE user_id = ? AND status = 'approved'"
);

$stmt->bind_param("i", $userId);
$stmt->execute();

$approvedRequests = $stmt->get_result()
    ->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard | ISMS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="dashboard">

    <header class="topbar">
        <h2>ISMS Staff</h2>

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

        <h1>Staff Dashboard</h1>
        <p>Welcome to the Inventory and Supply System.</p>

        <div class="cards">

            <div class="card">
                <h3>Available Products</h3>
                <h2><?= $totalProducts ?></h2>
            </div>

            <div class="card">
                <h3>Pending Requests</h3>
                <h2><?= $pendingRequests ?></h2>
            </div>

            <div class="card">
                <h3>Approved Requests</h3>
                <h2><?= $approvedRequests ?></h2>
            </div>

        </div>

    </main>

</div>
    <script src="../assets/js/app.js"></script>
</body>
</html>
