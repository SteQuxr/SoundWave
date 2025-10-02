<?php

session_start();

require_once('..\config\db.php');

$user_id = $_SESSION['user_id'];
$song_id = $_POST['song_id'];
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

$stmt = $db->prepare("INSERT INTO songs_comments (user_id, song_id, comment_text) VALUES (:user_id, :song_id, :comment_text)");
$stmt->execute([
    'user_id' => $user_id,
    'song_id' => $song_id,
    ':comment_text' => $comment_text
]);

header("Location: $last_page");
exit();
