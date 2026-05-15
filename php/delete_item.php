<?php
// ============================================
// delete_item.php — Delete a product (DELETE)
// ============================================
session_start();
require_once 'db_connect.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?error=not_logged_in');
    exit;
}

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if (!$product_id) {
    header('Location: ../index.php?error=invalid_id');
    exit;
}

// --- Check ownership or admin role ---
$stmt = $pdo->prepare("SELECT seller_id FROM products WHERE product_id = :id LIMIT 1");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: ../index.php?error=not_found');
    exit;
}

// Only the seller OR an admin can delete
if ($product['seller_id'] !== $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?error=forbidden');
    exit;
}

// --- Delete using prepared statement ---
$stmt = $pdo->prepare("DELETE FROM products WHERE product_id = :id");
$stmt->execute([':id' => $product_id]);

// Redirect based on role
if ($_SESSION['role'] === 'admin') {
    header('Location: ../admin-panel.php?success=deleted');
} else {
    header('Location: ../my-listings.php?success=deleted');
}
exit;
?>