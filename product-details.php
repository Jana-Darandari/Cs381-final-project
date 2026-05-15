<?php
// ============================================
// product-details.php — View product + Message Seller
// ============================================
session_start();
require_once 'php/db_connect.php';

$currentUserId = $_SESSION['user_id'] ?? null;
$currentRole   = $_SESSION['role']    ?? 'guest';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch product
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$p = $stmt->fetch();

if (!$p) {
    header('Location: index.php?error=not_found');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($p['title']) ?> | Bubble Market</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php include 'php/header.php'; ?>

  <main class="container" style="max-width:800px; margin-top:40px;">
    <div class="card" style="padding:40px;">

      <!-- Product Image -->
      <div class="product-image" style="height:250px; font-size:6rem;
        <?= $p['item_image'] ? "background-image:url('" . htmlspecialchars($p['item_image']) . "'); background-size:cover; background-position:center;" : '' ?>">
        <?php if (!$p['item_image']): ?>
          <?= $p['category']==='Books'?'📚':($p['category']==='Tablets'?'📱':($p['category']==='Audio'?'🎧':'📦')) ?>
        <?php endif; ?>
      </div>

      <!-- Product Info -->
      <h1 style="color:var(--bubble-blue); margin-bottom:5px;"><?= htmlspecialchars($p['title']) ?></h1>
      <p style="color:var(--muted); font-size:1.2rem; margin-top:0;">
        Category: <?= $p['category'] ?> | Status: <?= $p['status'] ?>
      </p>
      <p style="margin:20px 0; font-size:1.1rem; background:var(--bubble-light); padding:20px; border-radius:15px;">
        <?= htmlspecialchars($p['description'] ?: 'No description.') ?>
      </p>
      <div class="product-price" style="font-size:1.5rem;"><?= number_format($p['price'],0) ?> SR</div>

      <!-- Message Section -->
      <?php if (isset($_GET['success']) && $_GET['success']==='message_sent'): ?>
        <div class="alert alert-success" style="margin-top:20px;">✅ Message sent to seller!</div>
      <?php endif; ?>

      <?php if ($currentUserId && $currentUserId !== $p['seller_id']): ?>
        <div style="text-align:left; background:#fff; padding:20px; border-radius:15px; margin-top:20px; border:2px solid var(--bubble-light);">
          <h3 style="margin-top:0; color:var(--bubble-blue);">Message Seller</h3>
          <form method="POST" action="php/send_message.php">
            <input type="hidden" name="item_id"     value="<?= $p['product_id'] ?>">
            <input type="hidden" name="receiver_id" value="<?= $p['seller_id'] ?>">
            <textarea name="message_text" class="bubble-input" rows="3" placeholder="I'm interested in this!" required></textarea>
            <button type="submit" class="btn" style="margin-top:10px; width:100%;">Send Message</button>
          </form>
        </div>
      <?php elseif ($currentUserId === $p['seller_id']): ?>
        <p style="color:var(--muted); font-weight:bold; margin-top:20px;">This is your own listing.</p>
      <?php else: ?>
        <p style="margin-top:20px;"><a href="login.php" style="color:var(--bubble-blue); font-weight:bold;">Log in to message seller</a></p>
      <?php endif; ?>

    </div>
  </main>

  <footer class="footer"><p>© 2026 Bubble Campus Marketplace</p></footer>
</body>
</html>
