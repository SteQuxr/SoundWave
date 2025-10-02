<?php
session_start();
require_once('../config/db.php');
require_once('../config/getid3/getid3.php');

if ($_SESSION["isLogged"] != true) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = $_POST["title"] ?? NULL;
    $is_explicit = isset($_POST["is_explicit"]) ? 'Y' : 'N';
    $genre_id = $_POST["genre_id"] ?? NULL;
    $sub_genre_id = $_POST["sub_genre_id"] ?? NULL;

    if (!($title && $genre_id && $sub_genre_id)) {
        $_SESSION['error'] = "Invalid form data.";
        header("Location: post.php");
        exit;
    }

    $target_dir = "../images/";
    $picture_name = uniqid() . "_" . basename($_FILES["picture"]["name"]);
    $song_name = uniqid() . "_" . basename($_FILES["song"]["name"]);
    $picture_file = $target_dir . $picture_name;
    $song_file = $target_dir . $song_name;

    try {

        if ($_FILES["picture"]["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Picture upload error: " . $_FILES["picture"]["error"]);
        }

        $fileType = strtolower(pathinfo($picture_file, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed for the picture.");
        }

        list($width, $height) = getimagesize($_FILES["picture"]["tmp_name"]);
        if ($width > 400 || $height > 400) {
            throw new Exception("max size 400 x 400");
        }

        //move file 
        if (!move_uploaded_file($_FILES["picture"]["tmp_name"], $picture_file)) {
            throw new Exception("Failed to move uploaded file");
        }


        if ($_FILES["song"]["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Song file upload error: " . $_FILES["song"]["error"]);
        }

        $songType = strtolower(pathinfo($song_file, PATHINFO_EXTENSION));
        $allowedSongTypes = ['mp3'];
        if (!in_array($songType, $allowedSongTypes)) {
            throw new Exception("Invalid song file type.");
        }

        if (!move_uploaded_file($_FILES["song"]["tmp_name"], $song_file)) {
            throw new Exception("Failed to move uploaded song.");
        }

        // getID3
        $getID3 = new getID3;
        $fileInfo = $getID3->analyze($song_file);

        if (!isset($fileInfo['playtime_seconds'])) {
            throw new Exception("Could not determine song duration.");
        }

        $duration = round($fileInfo['playtime_seconds']);

        $stmt = $db->prepare("INSERT INTO songs (title, duration, artist_id, picture_path, file_path, date_added, is_explicit, genre_id, sub_genre_id)
            VALUES (:title, :duration, :artist_id, :picture_path, :file_path, :date_added, :is_explicit, :genre_id, :sub_genre_id)");
        $stmt->execute([
            ':title' => $title,
            ':duration' => $duration,
            ':artist_id' => $_SESSION['user_id'],
            ':picture_path' => "images/" . $picture_name,
            ':file_path' => "images/" . $song_name,
            ':date_added' => date('Y-m-d H:i:s'),
            ':is_explicit' => $is_explicit,
            ':genre_id' => $genre_id,
            ':sub_genre_id' => $sub_genre_id
        ]);

        $_SESSION['success'] = "Song uploaded successfully!";
        header("Location: post.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Upload failed: " . $e->getMessage();
        header("Location: post.php");
        exit;
    }
}

header("Location: post.php");
exit;
