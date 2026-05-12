<?php
// ============================================
// register_process.php — Handles registration
// ============================================
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

// --- Input Validation ---
$email    = filter_input(INPUT_POST, 'email',    FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
$role     = filter_input(INPUT_POST, 'role',     FILTER_DEFAULT);

// Validate fields not empty
if (empty($email) || empty($password) || empty($role)) {
    header('Location: ../register.php?error=empty');
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../register.php?error=invalid_email');
    exit;
}

// Validate password length
if (strlen($password) < 6) {
    header('Location: ../register.php?error=short_password');
    exit;
}

// Validate role (whitelist)
if (!in_array($role, ['student', 'admin'])) {
    $role = 'student';
}

// --- Check if email already exists ---
$stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
if ($stmt->fetch()) {
    header('Location: ../register.php?error=email_exists');
    exit;
}

// --- Hash the password securely ---
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// --- Insert new user using prepared statement ---
$stmt = $pdo->prepare(
    "INSERT INTO users (email, password, role) VALUES (:email, :password, :role)"
);
$stmt->execute([
    ':email'    => $email,
    ':password' => $hashedPassword,
    ':role'     => $role,
]);

// --- Redirect to login ---
header('Location: ../login.php?success=registered');
exit;
?>