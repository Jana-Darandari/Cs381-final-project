<?php
// ============================================
// header.php — Shared navigation header
// ============================================
// This file is included in every PHP page.
// It uses $_SESSION to show/hide nav links.

$currentRole   = $_SESSION['role']    ?? 'guest';
$currentPage   = basename($_SERVER['PHP_SELF']);
?>
<header class="site-header container">
  <a href="index.php" class="brand">🔵 Marketplace</a>
  <nav class="nav-links">
    <a href="index.php"         class="nav-link <?= $currentPage==='index.php'?'active':'' ?>">Home</a>

    <?php if ($currentRole !== 'guest'): ?>
      <a href="messages.php"    class="nav-link <?= $currentPage==='messages.php'?'active':'' ?>">Inbox</a>
      <a href="add-product.php" class="nav-link <?= $currentPage==='add-product.php'?'active':'' ?>">Sell Item</a>
      <a href="my-listings.php" class="nav-link <?= $currentPage==='my-listings.php'?'active':'' ?>">My Listings</a>
    <?php endif; ?>

    <?php if ($currentRole === 'admin'): ?>
      <a href="admin-panel.php" class="nav-link <?= $currentPage==='admin-panel.php'?'active':'' ?>">Admin</a>
    <?php endif; ?>

    <?php if ($currentRole === 'guest'): ?>
      <a href="login.php"       class="nav-link <?= $currentPage==='login.php'?'active':'' ?>">Login</a>
      <a href="register.php"    class="nav-link <?= $currentPage==='register.php'?'active':'' ?>">Register</a>
    <?php else: ?>
      <a href="php/logout.php"  class="nav-link">Logout</a>
    <?php endif; ?>
  </nav>
</header>
