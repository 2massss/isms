<?php if (isset($_GET["updated"])): ?>
    <div class="alert alert-success">
        Product updated successfully!
    </div>
<?php endif; ?>
<?php
require_once "../includes/auth.php";
requireRole("admin");

require_once "../config/db.php";

$pageTitle = "Product Management";
$search = trim($_GET["search"] ?? "");

// CSRF token for delete forms
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Search products
if ($search !== "") {

    $stmt = $conn->prepare(
        "SELECT p.id, p.name, p.description, p.quantity,
                p.min_stock, p.unit, p.created_at,
                c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.name LIKE ? OR c.name LIKE ?
         ORDER BY p.id DESC"
    );

    $term = "%" . $search . "%";
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();

    $products = $stmt->get_result();

} else {

    $products = $conn->query(
        "SELECT p.id, p.name, p.description, p.quantity,
                p.min_stock, p.unit, p.created_at,
                c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         ORDER BY p.id DESC"
    );
}

// Dashboard counts
$total = $conn->query(
    "SELECT COUNT(*) AS total FROM products"
)->fetch_assoc()["total"];

$low = $conn->query(
    "SELECT COUNT(*) AS total
     FROM products
     WHERE quantity <= min_stock"
)->fetch_assoc()["total"];

$out = $conn->query(
    "SELECT COUNT(*) AS total
     FROM products
     WHERE quantity = 0"
)->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Products | ISMS</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="content">

    <h1>Product Management</h1>
    <p>Manage inventory products and stock levels.</p>

    <!-- MESSAGES -->

    <?php if (isset($_GET["added"])): ?>
        <div class="alert alert-success">
            Product added successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["updated"])): ?>
        <div class="alert alert-success">
            Product updated successfully!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["deleted"])): ?>
        <div class="alert alert-success">
            Product deleted successfully!
        </div>
    <?php endif; ?>

    <?php
    $messages = [
        "used" => "This product cannot be deleted because it is linked to other records.",
        "invalid" => "Invalid product request.",
        "failed" => "The operation could not be completed."
    ];

    if (isset($_GET["error"]) && isset($messages[$_GET["error"]])):
    ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($messages[$_GET["error"]]) ?>
        </div>
    <?php endif; ?>

    <!-- SUMMARY CARDS -->

    <div class="cards">

        <div class="card">
            <h3>Total Products</h3>
            <h2><?= (int)$total ?></h2>
        </div>

        <div class="card">
            <h3>Low Stock</h3>
            <h2><?= (int)$low ?></h2>
        </div>

        <div class="card">
            <h3>Out of Stock</h3>
            <h2><?= (int)$out ?></h2>
        </div>

    </div>

    <br>

    <a href="product_add.php" class="btn">
        + Add Product
    </a>

    <br><br>

    <!-- SEARCH -->

    <form method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search product or category..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <br><br>

        <button type="submit" class="btn">Search</button>

        <a href="products.php" class="btn btn-danger">Clear</a>

    </form>

    <br>

    <!-- PRODUCT TABLE -->

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Minimum Stock</th>
                    <th>Unit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($products && $products->num_rows > 0): ?>

                    <?php while ($product = $products->fetch_assoc()): ?>

                        <?php
                        if ((int)$product["quantity"] === 0) {
                            $stockStatus = "Out of Stock";
                        } elseif (
                            (int)$product["quantity"] <=
                            (int)$product["min_stock"]
                        ) {
                            $stockStatus = "Low Stock";
                        } else {
                            $stockStatus = "In Stock";
                        }
                        ?>

                        <tr>

                            <td><?= (int)$product["id"] ?></td>

                            <td>
                                <?= htmlspecialchars($product["name"]) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $product["category_name"] ?? "Uncategorized"
                                ) ?>
                            </td>

                            <td><?= (int)$product["quantity"] ?></td>

                            <td><?= (int)$product["min_stock"] ?></td>

                            <td>
                                <?= htmlspecialchars($product["unit"] ?? "-") ?>
                            </td>

                            <td><?= $stockStatus ?></td>

                            <td>

                                <a
                                    href="product_edit.php?id=<?= (int)$product["id"] ?>"
                                    class="btn"
                                >
                                    Edit
                                </a>

                                <form
                                    action="product_del.php"
                                    method="POST"
                                    style="display:inline"
                                    onsubmit="return confirm('Delete this product?');"
                                >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$product["id"] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="8">No products found.</td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<script src="../assets/js/app.js"></script>

</body>
</html>
