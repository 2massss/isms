
<?php
require_once "../includes/auth.php";
requireRole("admin");
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    exit("Invalid user ID.");
}

if ($id == $_SESSION["id"]) {
    exit("You cannot delete your own account.");
}

// Check if user exists
$check = $conn->prepare(
    "SELECT id FROM users WHERE id = ?"
);
$check->bind_param("i", $id);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    exit("User not found.");
}

// Delete account
$stmt = $conn->prepare(
    "DELETE FROM users WHERE id = ?"
);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: users.php?deleted=1");
    exit;
}

http_response_code(500);
echo "Unable to delete user. The account may have related records.";
?>
