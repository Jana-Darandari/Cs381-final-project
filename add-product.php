<?php
// ============================================
// add-product.php
// ============================================
session_start();

// Generate a CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'php/db_connect.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=not_logged_in');
    exit;
}

$error = $_GET['error'] ?? '';
$errorMessages = [
    'short_title'        => '⚠️ Title must be at least 3 characters.',
    'invalid_category'   => '⚠️ Please select a valid category.',
    'invalid_price'      => '⚠️ Price must be a positive number.',
    'short_description'  => '⚠️ Description must be at least 10 characters.',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sell Item | Bubble Market</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php include 'php/header.php'; ?>

  <main class="container" style="max-width:600px; margin-top:40px;">
    <div class="card" style="padding:40px;">
      <h2 style="color:var(--bubble-blue); text-align:center; margin-top:0;">Post a Bubble</h2>

      <?php if ($error && isset($errorMessages[$error])): ?>
        <div class="alert alert-error"><?= $errorMessages[$error] ?></div>
      <?php endif; ?>

      <form method="POST" action="php/add_item.php" style="display:grid; gap:15px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div>
          <label style="font-weight:700; margin-bottom:5px; display:block;">What are you selling?</label>
          <input type="text" name="title" class="bubble-input" placeholder="Example: Calculus Textbook" required>
        </div>
        <div>
          <label style="font-weight:700; margin-bottom:5px; display:block;">Category</label>
          <select name="category" class="bubble-input" required>
            <option value="">Select Category...</option>
            <?php foreach (['Books','Tablets','Audio','Furniture'] as $cat): ?>
              <option value="<?= $cat ?>"><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label style="font-weight:700; margin-bottom:5px; display:block;">Price (SR)</label>
          <input type="number" name="price" class="bubble-input" placeholder="150" required min="1">
        </div>
        <div>
          <label style="font-weight:700; margin-bottom:5px; display:block;">Description</label>
          <textarea name="description" class="bubble-input" rows="3" placeholder="Condition, details, etc..." required></textarea>
        </div>
        <div>
          <label style="font-weight:700; margin-bottom:5px; display:block;">Image URL (optional)</label>
          <input type="url" name="image_url" class="bubble-input" placeholder="https://example.com/image.jpg">
        </div>
        <button type="submit" class="btn" style="margin-top:10px; width:100%;">Post Item</button>
      </form>
    </div>
  </main>

  <footer class="footer"><p>© 2026 Bubble Campus Marketplace</p></footer>
  <script src="script.js"></script>
</body>
</html>