<?php
session_start();
require_once('../config/db.php');

$song_id = $_POST['song_id'] ?? null;

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$stmt = $db->prepare("SELECT artist_id FROM songs WHERE id = :id");
$stmt->execute([
    'id' => $song_id
]);

$song = $stmt->fetch();

if ($song && $song['artist_id'] == $_SESSION['user_id']) {
    $stmt = $db->prepare("DELETE FROM songs WHERE id = :id");
    $stmt->execute([
        'id' => $song_id
    ]);
}

header("Location: ../user/yourSongs.php");
exit;
