<?php
// ============================================
// logout.php — Destroys session and redirects
// ============================================
session_start();
session_destroy();
header('Location: ../login.php');
exit;
?>