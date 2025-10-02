<?php

session_start();

require_once('../config/db.php');

if ($_SESSION["isLogged"] != true) {
    header("Location: index.php");
    exit;
}

$stmt = $db->prepare("SELECT id FROM songs ORDER BY RAND() LIMIT 1");
$stmt->execute();

$id = $stmt->fetch();

if ($id) {
    $song_id = $id['id'];
    header("Location: ../songPage.php?id=$song_id");
    exit;
} else {
    header("Location: ../index.php");
    exit;
}
