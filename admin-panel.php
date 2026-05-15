<?php
// ============================================
// admin-panel.php — Admin only (READ + DELETE all)
// ============================================
session_start();
require_once 'php/db_connect.php';

// Must be admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?error=forbidden');
    exit;
}

// Fetch all products with seller email
$stmt = $pdo->query(
    "SELECT p.*, u.email AS seller_email
     FROM products p
     JOIN users u ON p.seller_id = u.user_id
     ORDER BY p.created_at DESC"
);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin | Bubble Market</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php include 'php/header.php'; ?>

  <main class="container" style="max-width:800px; margin-top:40px;">
    <h2 style="color:var(--bubble-blue); margin-bottom:10px;">Admin Moderation</h2>
    <p style="color:var(--muted); margin-bottom:30px;">Delete inappropriate or old listings from the database.</p>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">✅ Item deleted successfully.</div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
      <p>No products in the marketplace.</p>
    <?php else: ?>
      <?php foreach ($products as $p): ?>
        <div class="card" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; padding:15px 30px; flex-wrap:wrap; gap:10px;">
          <div>
            <h3 style="margin:0;"><?= htmlspecialchars($p['title']) ?> <span style="font-size:0.9rem; color:#6f78a8;">(<?= $p['status'] ?>)</span></h3>
            <small style="color:var(--muted);">Seller: <?= htmlspecialchars($p['seller_email']) ?> | <?= number_format($p['price'],0) ?> SR</small>
          </div>
          <form method="POST" action="php/delete_item.php" onsubmit="return confirm('Permanently delete this listing?');">
            <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
            <button type="submit" class="btn" style="background:#ff85a2; padding:10px 20px;">Delete</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <footer class="footer"><p>© 2026 Bubble Campus Marketplace</p></footer>
</body>
</html>
