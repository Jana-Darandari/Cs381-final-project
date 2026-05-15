<?php
// ============================================
// send_message.php — Send a message (CREATE)
// ============================================
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?error=not_logged_in');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$item_id      = filter_input(INPUT_POST, 'item_id',      FILTER_VALIDATE_INT);
$receiver_id  = filter_input(INPUT_POST, 'receiver_id',  FILTER_VALIDATE_INT);
$message_text = htmlspecialchars(trim(filter_input(INPUT_POST, 'message_text', FILTER_DEFAULT)));

if (!$item_id || !$receiver_id || empty($message_text)) {
    header('Location: ../product-details.php?id=' . $item_id . '&error=empty_message');
    exit;
}

// Can't message yourself
if ($receiver_id === $_SESSION['user_id']) {
    header('Location: ../product-details.php?id=' . $item_id . '&error=self_message');
    exit;
}

// Insert message
$stmt = $pdo->prepare(
    "INSERT INTO messages (sender_id, receiver_id, item_id, message_text)
     VALUES (:sender_id, :receiver_id, :item_id, :message_text)"
);
$stmt->execute([
    ':sender_id'    => $_SESSION['user_id'],
    ':receiver_id'  => $receiver_id,
    ':item_id'      => $item_id,
    ':message_text' => $message_text,
]);

header('Location: ../product-details.php?id=' . $item_id . '&success=message_sent');
exit;
?>