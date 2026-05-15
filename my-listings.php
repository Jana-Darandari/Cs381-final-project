<?php
// ============================================
// my-listings.php — User's own products (READ + UPDATE + DELETE)
// ============================================
session_start();
require_once 'php/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=not_logged_in');
    exit;
}

// Fetch only this user's products
$stmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = :id ORDER BY created_at DESC");
$stmt->execute([':id' => $_SESSION['user_id']]);
$myProducts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Listings | Bubble Market</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php include 'php/header.php'; ?>

  <main class="container" style="max-width:800px; margin-top:40px;">
    <h2 style="color:var(--bubble-blue); margin-bottom:10px;">My Inventory</h2>
    <p style="color:var(--muted); margin-bottom:30px;">Manage your items. Mark them as sold when someone buys them.</p>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">✅ <?= $_GET['success']==='deleted'?'Item deleted.':'Status updated.' ?></div>
    <?php endif; ?>

    <?php if (empty($myProducts)): ?>
      <p>You haven't posted any items yet. <a href="add-product.php" style="color:var(--bubble-blue);">Post one now!</a></p>
    <?php else: ?>
      <?php foreach ($myProducts as $p): ?>
        <div class="card" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; padding:15px 30px; flex-wrap:wrap; gap:10px;">
          <h3 style="margin:0;">
            <?= htmlspecialchars($p['title']) ?>
            <span style="font-size:0.9rem; color:#6f78a8;">(<?= $p['status'] ?>)</span>
          </h3>
          <div style="display:flex; gap:10px;">
            <!-- Toggle Status -->
            <form method="POST" action="php/toggle_status.php">
              <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
              <button type="submit" class="btn btn-ghost" style="padding:10px 20px;">
                Mark as <?= $p['status']==='available' ? 'Sold' : 'Available' ?>
              </button>
            </form>
            <!-- Delete -->
            <form method="POST" action="php/delete_item.php" onsubmit="return confirm('Delete this item?');">
              <input type="hidden" name="product_id" value="<?= $p['product_id'] ?>">
              <button type="submit" class="btn" style="background:#ff85a2; padding:10px 20px;">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <footer class="footer"><p>© 2026 Bubble Campus Marketplace</p></footer>
</body>
</html>
