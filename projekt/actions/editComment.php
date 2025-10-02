<?php

session_start();

require_once('..\config\db.php');

$user_id = $_SESSION['user_id'];
$comment_id = $_POST['comment_id'];
$comment_text = $_POST['comment_text'];
$last_page = isset($_POST['last_page']) ? urldecode($_POST['last_page']) : 'index.php';

if ($_SESSION["isLogged"] != true) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $db->prepare("UPDATE songs_comments SET comment_text = :comment_text, edited = 'Y' WHERE id = :comment_id AND user_id = :user_id");
$stmt->execute([
    'comment_text' => $comment_text,
    'comment_id' => $comment_id,
    'user_id' => $user_id
]);


header("Location: $last_page");
exit;
