
<?php
$role = $_SESSION["role"] ?? "";
?>

<aside class="sidebar" id="sidebar">

    <h3>Navigation</h3>

    <?php if ($role === "admin"): ?>

        <a href="dash.php">Dashboard</a>
        <a href="users.php">User Management</a>
        <a href="products.php">Products</a>
        <a href="category.php">Categories</a>
        <a href="stock.php">Stock Management</a>
        <a href="requests.php">Supply Requests</a>
        <a href="reports.php">Reports</a>
        <a href="logs.php">Activity Logs</a>

    <?php elseif ($role === "staff"): ?>

        <a href="dash.php">Dashboard</a>
        <a href="products.php">View Products</a>
        <a href="request.php">Request Supplies</a>
        <a href="history.php">Request History</a>

    <?php endif; ?>

</aside>
