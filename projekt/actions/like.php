<?php

session_start();

require_once('..\config\db.php');

$song_id = $_GET['id'];
$last_page = isset($_GET['last_page']) ? urldecode($_GET['last_page']) : '/projekt/index.php';

if ($_SESSION["isLogged"] != true) {
    header("Location: index.php");
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM songs_likes WHERE user_id = :user_id AND song_id = :song_id");
$stmt->execute([
    'user_id' => $_SESSION['user_id'],
    'song_id' => $song_id
]);

if ($stmt->rowCount() === 0) {

    $stmt = $db->prepare("INSERT INTO songs_likes (user_id, song_id) VALUES (:user_id, :song_id)");
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'song_id' => $song_id
    ]);
} else {
    $stmt = $db->prepare("DELETE FROM songs_likes WHERE user_id = :user_id AND song_id = :song_id");
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'song_id' => $song_id
    ]);
}

header("Location: $last_page");
exit;
