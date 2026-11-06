
<?php
require_once "../includes/auth.php";
requireRole("admin");

require_once "../config/db.php";

$error = "";

$categories = $conn->query(
    "SELECT id, name FROM categories ORDER BY name ASC"
);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $categoryId = $_POST["category_id"] ?? "";
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

        $categoryId = ($categoryId === "") ? null : filter_var(
            $categoryId,
            FILTER_VALIDATE_INT
        );

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

                $stmt = $conn->prepare(
                    "INSERT INTO products
                    (category_id, name, description, quantity, min_stock, unit)
                    VALUES (?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "issiis",
                    $categoryId,
                    $name,
                    $description,
                    $quantity,
                    $minStock,
                    $unit
                );

                if ($stmt->execute()) {
                    header("Location: products.php?added=1");
                    exit;
                }

                $error = "Unable to add product.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Product | ISMS</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="content">

    <h1>Add Product</h1>
    <p>Register a new inventory product.</p>

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
                required
                value="<?= htmlspecialchars($_POST["name"] ?? "") ?>"
            >

            <br><br>

            <label>Category</label>

            <select name="category_id">

                <option value="">Select Category (Optional)</option>

                <?php while ($cat = $categories->fetch_assoc()): ?>

                    <option
                        value="<?= (int)$cat["id"] ?>"
                        <?= (($_POST["category_id"] ?? "") == $cat["id"]) ? "selected" : "" ?>
                    >
                        <?= htmlspecialchars($cat["name"]) ?>
                    </option>

                <?php endwhile; ?>

            </select>

            <br><br>

            <label>Description</label>

            <textarea
                name="description"
                rows="4"
            ><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>

            <br><br>

            <label>Quantity</label>

            <input
                type="number"
                name="quantity"
                min="0"
                required
                value="<?= htmlspecialchars($_POST["quantity"] ?? "0") ?>"
            >

            <br><br>

            <label>Minimum Stock</label>

            <input
                type="number"
                name="min_stock"
                min="0"
                required
                value="<?= htmlspecialchars($_POST["min_stock"] ?? "5") ?>"
            >

            <br><br>

            <label>Unit</label>

            <input
                type="text"
                name="unit"
                maxlength="30"
                placeholder="e.g. pcs, boxes, packs"
                value="<?= htmlspecialchars($_POST["unit"] ?? "") ?>"
            >

            <br><br>

            <button type="submit" class="btn">
                Save Product
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
