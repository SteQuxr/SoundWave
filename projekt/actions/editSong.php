<?php
session_start();
require_once('../config/db.php');

if ($_SESSION["isLogged"] != true) {
    header("Location: ../login/login.php");
    exit;
}

$last_page = isset($_POST['last_page']) ? urldecode($_POST['last_page']) : 'index.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['song_id'] ?? null;
    $title = $_POST["title"] ?? null;
    $genre_id = $_POST["genre_id"] ?? null;
    $sub_genre_id = $_POST["sub_genre_id"] ?? null;
    $is_explicit = isset($_POST["is_explicit"]) ? 1 : 0;

    if (!($id && $title && $genre_id && $sub_genre_id)) {
        $_SESSION['error'] = "Invalid form data.";
        header("Location: $last_page");
        exit;
    }

    $image_uploaded = isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK;

    try {
        if ($image_uploaded) {
            $target_dir = "../images/";
            $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $picture_path = "images/" . $filename;
            $picture_file = $target_dir . $filename;

            $fileType = strtolower(pathinfo($picture_file, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
            }

            list($width, $height) = getimagesize($_FILES["image"]["tmp_name"]);
            if ($width > 400 || $height > 400) {
                throw new Exception("Image size must be 400x400px or less.");
            }

            // delete old
            $stmt = $db->prepare("SELECT picture_path FROM songs WHERE id = :id");
            $stmt->execute([
                ':user_id' => $id
            ]);
            $song = $stmt->fetch();

            if ($song && $song['picture_path'] !== "images/default_song.png") {
                $old_file = "../" . $song['picture_path'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $picture_file)) {
                throw new Exception("Failed to upload image.");
            }

            // Update with image
            $stmt = $db->prepare("
                UPDATE songs 
                SET title = :title, 
                    genre_id = :genre_id, 
                    sub_genre_id = :sub_genre_id, 
                    is_explicit = :is_explicit, 
                    picture_path = :picture_path 
                WHERE id = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':title' => $title,
                ':genre_id' => $genre_id,
                ':sub_genre_id' => $sub_genre_id,
                ':is_explicit' => $is_explicit,
                ':picture_path' => $picture_path
            ]);
        } else {
            // Update without image
            $stmt = $db->prepare("
                UPDATE songs 
                SET title = :title, 
                    genre_id = :genre_id, 
                    sub_genre_id = :sub_genre_id, 
                    is_explicit = :is_explicit 
                WHERE id = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':title' => $title,
                ':genre_id' => $genre_id,
                ':sub_genre_id' => $sub_genre_id,
                ':is_explicit' => $is_explicit
            ]);
        }

        $_SESSION['success'] = "Song updated successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Update failed: " . $e->getMessage();
    }

    header("Location: $last_page");
    exit;
}


/*
session_start();

require_once('../config\db.php');

$_SESSION["isLogged"] ?? false;

if ($_SESSION["isLogged"] != true) {
header("Location: ../login/login.php");
exit;
}

$last_page = isset($_POST['last_page']) ? urldecode($_POST['last_page']) : 'index.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$id = $_POST['song_id'];
$title = $_POST["title"] ?? NULL;
$genre_id = $_POST["genre_id"] ?? NULL;
$sub_genre_id = $_POST["sub_genre_id"] ?? NULL;
$is_explicit = isset($_POST["is_explicit"]) ? 1 : 0;

if ($id && $title && $genre_id && $sub_genre_id) {
$stmt = $db->prepare("
UPDATE songs
SET
title = :title,
genre_id = :genre_id,
sub_genre_id = :sub_genre_id,
is_explicit = :is_explicit
WHERE id = :id
");

$stmt->execute([
':id' => $id,
':title' => $title,
':genre_id' => $genre_id,
':sub_genre_id' => $sub_genre_id,
':is_explicit' => $is_explicit,
]);
} else {
$_SESSION['error'] = "Invalid form data.";
}
}

header("Location: $last_page");
exit();
*/