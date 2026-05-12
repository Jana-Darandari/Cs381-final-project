<?php
// ============================================
// toggle_status.php — Toggle available/sold (UPDATE)
// ============================================
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?error=not_logged_in');
    exit;
}

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if (!$product_id) {
    header('Location: ../my-listings.php?error=invalid_id');
    exit;
}

// Check ownership
$stmt = $pdo->prepare("SELECT seller_id, status FROM products WHERE product_id = :id LIMIT 1");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch();

if (!$product || $product['seller_id'] !== $_SESSION['user_id']) {
    header('Location: ../my-listings.php?error=forbidden');
    exit;
}

// Toggle status
$newStatus = ($product['status'] === 'available') ? 'sold' : 'available';

$stmt = $pdo->prepare("UPDATE products SET status = :status WHERE product_id = :id");
$stmt->execute([':status' => $newStatus, ':id' => $product_id]);

header('Location: ../my-listings.php?success=updated');
exit;
?>