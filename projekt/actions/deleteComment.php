<?php
session_start();
require_once('../config/db.php');

$comment_id = $_POST['comment_id'] ?? null;
$last_page = $_POST['last_page'] ?? '/projekt/index.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$stmt = $db->prepare("SELECT user_id FROM songs_comments WHERE id = :id");
$stmt->execute([
    'id' => $comment_id
]);

$comment = $stmt->fetch();

if ($comment && $comment['user_id'] == $_SESSION['user_id']) {
    $stmt = $db->prepare("DELETE FROM songs_comments WHERE id = :id");
    $stmt->execute([
        'id' => $comment_id
    ]);
}

header("Location: $last_page");
exit;
