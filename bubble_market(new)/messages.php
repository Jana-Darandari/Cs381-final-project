<?php
// ============================================
// messages.php — User Inbox (READ messages)
// ============================================
session_start();
require_once 'php/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=not_logged_in');
    exit;
}

// Fetch messages received by current user, with sender email and product title
$stmt = $pdo->prepare(
    "SELECT m.*, u.email AS sender_email, p.title AS product_title
     FROM messages m
     JOIN users u    ON m.sender_id   = u.user_id
     JOIN products p ON m.item_id     = p.product_id
     WHERE m.receiver_id = :uid
     ORDER BY m.created_at DESC"
);
$stmt->execute([':uid' => $_SESSION['user_id']]);
$myMessages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inbox | Bubble Market</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php include 'php/header.php'; ?>

  <main class="container" style="max-width:800px; margin-top:40px;">
    <h2 style="color:var(--bubble-blue); margin-bottom:30px;">My Inbox</h2>

    <?php if (empty($myMessages)): ?>
      <p style="color:var(--muted);">Your inbox is empty.</p>
    <?php else: ?>
      <?php foreach ($myMessages as $m): ?>
        <div class="card" style="text-align:left; margin-bottom:15px; border:2px solid var(--bubble-light);">
          <h4 style="margin:0 0 10px; color:var(--bubble-blue);">Regarding: <?= htmlspecialchars($m['product_title']) ?></h4>
          <p style="margin:0 0 10px; font-size:0.9rem;"><strong>From:</strong> <?= htmlspecialchars($m['sender_email']) ?></p>
          <p style="background:var(--bubble-light); padding:15px; border-radius:10px; margin:0;"><?= htmlspecialchars($m['message_text']) ?></p>
          <small style="color:var(--muted); display:block; margin-top:10px;">Sent: <?= $m['created_at'] ?></small>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </main>

  <footer class="footer"><p>© 2026 Bubble Campus Marketplace</p></footer>
</body>
</html>
