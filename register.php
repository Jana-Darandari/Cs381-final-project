<?php
// ============================================
// register.php
// ============================================
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = $_GET['error'] ?? '';
$errorMessages = [
    'empty'         => '⚠️ Please fill in all fields.',
    'invalid_email' => '⚠️ Please enter a valid email.',
    'short_password'=> '⚠️ Password must be at least 6 characters.',
    'email_exists'  => '❌ This email is already registered.',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | Bubble Market</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

  <?php include 'php/header.php'; ?>

  <main class="container" style="max-width:500px; margin-top:60px;">
    <div class="card" style="padding:40px;">
      <h2 style="color:var(--bubble-blue); text-align:center; margin-top:0;">Create Account</h2>

      <?php if ($error && isset($errorMessages[$error])): ?>
        <div class="alert alert-error"><?= $errorMessages[$error] ?></div>
      <?php endif; ?>

      <form method="POST" action="php/register_process.php" style="display:grid; gap:20px;">
        <div>
          <label style="font-weight:700; margin-bottom:8px; display:block;">Email</label>
          <input type="email" name="email" placeholder="student@campus.edu" required class="bubble-input">
        </div>
        <div>
          <label style="font-weight:700; margin-bottom:8px; display:block;">Password</label>
          <input type="password" name="password" placeholder="••••••••" required class="bubble-input" minlength="6">
        </div>
        <div>
          <label style="font-weight:700; margin-bottom:8px; display:block;">Role</label>
          <select name="role" class="bubble-input">
            <option value="student">Student</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <button type="submit" class="btn" style="width:100%; margin-top:10px;">Sign Up</button>
      </form>

      <p style="text-align:center; margin-top:20px;">
        <a href="login.php" style="color:var(--bubble-blue); font-weight:600;">Already have an account? Login</a>
      </p>
    </div>
  </main>

  <footer class="footer"><p>© 2026 Bubble Campus Marketplace</p></footer>
</body>
</html>
