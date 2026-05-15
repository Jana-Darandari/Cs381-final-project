<?php
// ============================================
// login.php
// ============================================
session_start();

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = $_GET['error'] ?? '';
$errorMessages = [
    'empty'            => '⚠️ Please fill in all fields.',
    'invalid_email'    => '⚠️ Please enter a valid email.',
    'wrong_credentials'=> '❌ Incorrect email or password.',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Bubble Market</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php include 'php/header.php'; ?>

  <main class="container" style="max-width:500px; margin-top:60px;">
    <div class="card" style="padding:40px;">
      <h2 style="color:var(--bubble-blue); text-align:center; margin-top:0; font-size:2rem;">Welcome Back</h2>
      <p style="text-align:center; opacity:0.7; margin-bottom:30px;">Sign in to your account.</p>

      <?php if ($error && isset($errorMessages[$error])): ?>
        <div class="alert alert-error"><?= $errorMessages[$error] ?></div>
      <?php endif; ?>

      <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
        <div class="alert alert-success">✅ Account created! Please log in.</div>
      <?php endif; ?>

      <form method="POST" action="php/login_process.php" style="display:grid; gap:20px;">
        <div>
          <label style="font-weight:700; margin-bottom:8px; display:block;">Email</label>
          <input type="email" name="email" placeholder="example@email.com" required class="bubble-input">
        </div>
        <div>
          <label style="font-weight:700; margin-bottom:8px; display:block;">Password</label>
          <input type="password" name="password" placeholder="••••••••" required class="bubble-input">
        </div>
        <button type="submit" class="btn" style="width:100%; margin-top:10px; font-size:1.1rem;">Sign In</button>
      </form>

      <p style="text-align:center; margin-top:20px;">
        <a href="register.php" style="color:var(--bubble-blue); font-weight:600;">Don't have an account? Register</a>
      </p>
    </div>
  </main>

  <footer class="footer"><p>© 2026 Bubble Campus Marketplace</p></footer>
</body>
</html>
