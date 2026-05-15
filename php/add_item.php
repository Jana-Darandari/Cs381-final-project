<?php
// ============================================
// add_item.php — Add new product (CREATE)
// ============================================
session_start();
require_once 'db_connect.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?error=not_logged_in');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../add-product.php');
    exit;
}

// --- CSRF Token Validation ---
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Security Error: Invalid CSRF token.");
}

// --- Input Validation & Sanitization ---
$title       = htmlspecialchars(trim(filter_input(INPUT_POST, 'title',       FILTER_DEFAULT)));
$category    = htmlspecialchars(trim(filter_input(INPUT_POST, 'category',    FILTER_DEFAULT)));
$price       = filter_input(INPUT_POST, 'price',       FILTER_VALIDATE_FLOAT);
$description = htmlspecialchars(trim(filter_input(INPUT_POST, 'description', FILTER_DEFAULT)));
$image_url   = filter_input(INPUT_POST, 'image_url',   FILTER_VALIDATE_URL) ?: '';

// Whitelist allowed categories
$allowed_categories = ['Books', 'Tablets', 'Audio', 'Furniture'];

if (empty($title) || strlen($title) < 3) {
    header('Location: ../add-product.php?error=short_title');
    exit;
}
if (!in_array($category, $allowed_categories)) {
    header('Location: ../add-product.php?error=invalid_category');
    exit;
}
if ($price === false || $price <= 0) {
    header('Location: ../add-product.php?error=invalid_price');
    exit;
}
if (empty($description) || strlen($description) < 10) {
    header('Location: ../add-product.php?error=short_description');
    exit;
}

// --- Insert using prepared statement (prevents SQL injection) ---
$stmt = $pdo->prepare(
    "INSERT INTO products (seller_id, title, category, price, description, item_image, status)
     VALUES (:seller_id, :title, :category, :price, :description, :item_image, 'available')"
);
$stmt->execute([
    ':seller_id'   => $_SESSION['user_id'],
    ':title'       => $title,
    ':category'    => $category,
    ':price'       => $price,
    ':description' => $description,
    ':item_image'  => $image_url,
]);

header('Location: ../index.php?success=item_added');
exit;
?>