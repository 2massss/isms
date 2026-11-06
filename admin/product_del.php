
<?php
require_once "../includes/auth.php";
requireRole("admin");

require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: products.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$token = $_POST["csrf_token"] ?? "";

if (
    !$id ||
    $id <= 0 ||
    empty($_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $token)
) {
    header("Location: products.php?error=invalid");
    exit;
}

// Check product
$check = $conn->prepare(
    "SELECT id FROM products WHERE id = ?"
);

$check->bind_param("i", $id);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    header("Location: products.php?error=invalid");
    exit;
}

// Delete product
$stmt = $conn->prepare(
    "DELETE FROM products WHERE id = ?"
);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: products.php?deleted=1");
    exit;

} else {

    // Related request or stock records may prevent deletion
    header("Location: products.php?error=used");
    exit;
}
