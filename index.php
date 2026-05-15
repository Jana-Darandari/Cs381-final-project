<?php
// ============================================
// index.php — Homepage (READ products)
// ============================================
session_start();
require_once 'php/db_connect.php';

$currentRole   = $_SESSION['role']    ?? 'guest';
$currentUserId = $_SESSION['user_id'] ?? null;

// --- Read products from DB with optional filters ---
$where   = [];
$params  = [];

$search   = htmlspecialchars(trim($_GET['search']   ?? ''));
$category = htmlspecialchars(trim($_GET['category'] ?? ''));
$status   = htmlspecialchars(trim($_GET['status']   ?? ''));
$price    = $_GET['price'] ?? '';

if ($search)   { $where[] = "title LIKE :search";      $params[':search']   = "%$search%"; }
if ($category) { $where[] = "category = :category";    $params[':category'] = $category; }
if ($status)   { $where[] = "status = :status";        $params[':status']   = $status; }
if ($price === '0-300')    { $where[] = "price <= 300"; }
if ($price === '301-1000') { $where[] = "price > 300 AND price <= 1000"; }
if ($price === '1000+')    { $where[] = "price > 1000"; }

$sql = "SELECT * FROM products";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bubble Market | Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php include 'php/header.php'; ?>

  <section class="hero container">
    <h1>Buy &amp; Sell on Campus</h1>
    <p>The softest, simplest way to trade gear with fellow students.</p>
  </section>

  <!-- SUCCESS / ERROR MESSAGES -->
  <?php if (isset($_GET['success'])): ?>
    <div class="container">
      <div class="alert alert-success">
        <?php
          $msg = [
            'item_added' => '✅ Item posted successfully!',
            'registered' => '✅ Account created! You can now log in.',
          ];
          echo $msg[$_GET['success']] ?? '✅ Done!';
        ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- SEARCH & FILTER -->
  <section class="container">
    <form method="GET" action="index.php" class="search-bar">
      <input  type="text"   name="search"   placeholder="Search products..."  value="<?= htmlspecialchars($search) ?>">
      <select name="category">
        <option value="">All Categories</option>
        <?php foreach (['Books','Tablets','Audio','Furniture'] as $cat): ?>
          <option value="<?= $cat ?>" <?= $category===$cat?'selected':'' ?>><?= $cat ?></option>
        <?php endforeach; ?>
      </select>
      <select name="status">
        <option value="">All Status</option>
        <option value="available" <?= $status==='available'?'selected':'' ?>>Available</option>
        <option value="sold"      <?= $status==='sold'     ?'selected':'' ?>>Sold</option>
      </select>
      <select name="price">
        <option value="">All Prices</option>
        <option value="0-300"    <?= $price==='0-300'    ?'selected':'' ?>>0 - 300 SR</option>
        <option value="301-1000" <?= $price==='301-1000' ?'selected':'' ?>>301 - 1000 SR</option>
        <option value="1000+"    <?= $price==='1000+'    ?'selected':'' ?>>1000+ SR</option>
      </select>
    </form>
  </section>

  <!-- PRODUCTS GRID -->
  <section class="container">
    <?php if (empty($products)): ?>
      <h3 style="text-align:center; color:var(--muted);">No products match your search.</h3>
    <?php else: ?>
      <div class="grid-products">
        <?php foreach ($products as $p): ?>
          <a href="product-details.php?id=<?= $p['product_id'] ?>" class="card">
            <div class="product-image"
              <?= $p['item_image'] ? "style=\"background-image:url('" . htmlspecialchars($p['item_image']) . "'); background-size:cover; background-position:center;\"" : '' ?>>
              <?php if (!$p['item_image']): ?>
                <?= $p['category']==='Books' ? '📚' : ($p['category']==='Tablets' ? '📱' : ($p['category']==='Audio' ? '🎧' : '📦')) ?>
              <?php endif; ?>
            </div>
            <h3 style="margin: 10px 0 5px;"><?= htmlspecialchars($p['title']) ?></h3>
            <div style="color:#6f78a8; font-size:0.9rem;"><?= $p['category'] ?> • <?= $p['status'] ?></div>
            <div class="product-price"><?= number_format($p['price'], 0) ?> SR</div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <footer class="footer"><p>© 2026 Bubble Campus Marketplace</p></footer>
  <script src="script.js"></script>
</body>
</html>
