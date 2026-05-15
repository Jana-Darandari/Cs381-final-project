<?php
// ============================================
// login_process.php — Handles login form
// ============================================
session_start();
require_once 'db_connect.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

// --- Input Validation & Sanitization ---
$email    = filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);

if (empty($email) || empty($password)) {
    header('Location: ../login.php?error=empty');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../login.php?error=invalid_email');
    exit;
}

// --- Query the database using prepared statement (prevents SQL injection) ---
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

// --- Verify password with password_verify() ---
if (!$user || !password_verify($password, $user['password'])) {
    header('Location: ../login.php?error=wrong_credentials');
    exit;
}

// --- Create session variables ---
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['email']   = $user['email'];
$_SESSION['role']    = $user['role'];

// --- Redirect based on role ---
if ($user['role'] === 'admin') {
    header('Location: ../admin-panel.php');
} else {
    header('Location: ../index.php');
}
exit;
?>