
<?php
require_once "../includes/auth.php";
requireRole("admin");

require_once "../config/db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    exit("Invalid product ID.");
}

// Get product
$stmt = $conn->prepare(
    "SELECT * FROM products WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    exit("Product not found.");
}

// Get categories
$categories = $conn->query(
    "SELECT id, name FROM categories ORDER BY name ASC"
);

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $categoryInput = $_POST["category_id"] ?? "";
    $quantity = filter_var($_POST["quantity"] ?? null, FILTER_VALIDATE_INT);
    $minStock = filter_var($_POST["min_stock"] ?? null, FILTER_VALIDATE_INT);
    $unit = trim($_POST["unit"] ?? "");

    if ($name === "") {
        $error = "Product name is required.";

    } elseif (strlen($name) > 150) {
        $error = "Product name must not exceed 150 characters.";

    } elseif (
        $quantity === false || $quantity === null || $quantity < 0 ||
        $minStock === false || $minStock === null || $minStock < 0
    ) {
        $error = "Quantity and minimum stock must be zero or greater.";

    } elseif (strlen($unit) > 30) {
        $error = "Unit must not exceed 30 characters.";

    } else {

        $categoryId = ($categoryInput === "")
            ? null
            : filter_var($categoryInput, FILTER_VALIDATE_INT);

        if ($categoryId !== null && (
            $categoryId === false || $categoryId <= 0
        )) {
            $error = "Invalid category.";

        } else {

            if ($categoryId !== null) {

                $check = $conn->prepare(
                    "SELECT id FROM categories WHERE id = ?"
                );

                $check->bind_param("i", $categoryId);
                $check->execute();

                if ($check->get_result()->num_rows === 0) {
                    $error = "Selected category does not exist.";
                }
            }

            if ($error === "") {

                $unit = ($unit === "") ? null : $unit;

                $update = $conn->prepare(
                    "UPDATE products
                     SET category_id = ?,
                         name = ?,
                         description = ?,
                         quantity = ?,
                         min_stock = ?,
                         unit = ?
                     WHERE id = ?"
                );

                $update->bind_param(
                    "issiisi",
                    $categoryId,
                    $name,
                    $description,
                    $quantity,
                    $minStock,
                    $unit,
                    $id
                );

                if ($update->execute()) {
                    header("Location: products.php?updated=1");
                    exit;
                }

                $error = "Unable to update product.";
            }
        }
    }

    // Keep submitted values on the form
    $product["name"] = $name;
    $product["description"] = $description;
    $product["category_id"] = $categoryInput;
    $product["quantity"] = $_POST["quantity"] ?? "";
    $product["min_stock"] = $_POST["min_stock"] ?? "";
    $product["unit"] = $unit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Product | ISMS</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="content">

    <h1>Edit Product</h1>
    <p>Update inventory product information.</p>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <form method="POST">

            <label>Product Name</label>

            <input
                type="text"
                name="name"
                maxlength="150"
                value="<?= htmlspecialchars($product["name"]) ?>"
                required
            >

            <br><br>

            <label>Category</label>

            <select name="category_id">

                <option value="">Select Category (Optional)</option>

                <?php while ($cat = $categories->fetch_assoc()): ?>

                    <option
                        value="<?= (int)$cat["id"] ?>"
                        <?= ((string)$product["category_id"] === (string)$cat["id"]) ? "selected" : "" ?>
                    >
                        <?= htmlspecialchars($cat["name"]) ?>
                    </option>

                <?php endwhile; ?>

            </select>

            <br><br>

            <label>Description</label>

            <textarea name="description" rows="4"><?= htmlspecialchars($product["description"] ?? "") ?></textarea>

            <br><br>

            <label>Quantity</label>

            <input
                type="number"
                name="quantity"
                min="0"
                value="<?= htmlspecialchars((string)$product["quantity"]) ?>"
                required
            >

            <br><br>

            <label>Minimum Stock</label>

            <input
                type="number"
                name="min_stock"
                min="0"
                value="<?= htmlspecialchars((string)$product["min_stock"]) ?>"
                required
            >

            <br><br>

            <label>Unit</label>

            <input
                type="text"
                name="unit"
                maxlength="30"
                value="<?= htmlspecialchars($product["unit"] ?? "") ?>"
                placeholder="e.g. pcs, boxes, packs"
            >

            <br><br>

            <button type="submit" class="btn">
                Update Product
            </button>

            <a href="products.php" class="btn btn-danger">
                Cancel
            </a>

        </form>

    </div>

</div>

<script src="../assets/js/app.js"></script>

</body>
</html>
