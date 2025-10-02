<?php

session_start();

require_once('../config\db.php');

$_SESSION["isLogged"] ?? false;

if ($_SESSION["isLogged"] != true) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SESSION["is_admin"] != true) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? NULL;
    $title = $_POST["title"] ?? NULL;
    $duration = $_POST["duration"] ?? NULL;
    $artist_id = $_POST["artist_id"] ?? NULL;
    $file_path = $_POST["file_path"] ?? NULL;
    $date_added = $_POST["date_added"] ?? NULL;
    $genre_id = $_POST["genre_id"] ?? NULL;
    $sub_genre_id = $_POST["sub_genre_id"] ?? NULL;
    $is_explicit = isset($_POST["is_explicit"]) ? 1 : 0;

    if ($id && $title && $duration && $artist_id && $file_path && $date_added && $genre_id && $sub_genre_id) {
        try {
            $stmt = $db->prepare("
            UPDATE songs 
            SET 
                title = :title, 
                duration = :duration, 
                artist_id = :artist_id, 
                file_path = :file_path, 
                date_added = :date_added, 
                genre_id = :genre_id, 
                sub_genre_id = :sub_genre_id,
                is_explicit = :is_explicit 
            WHERE id = :id
            ");

            $stmt->execute([
                'id' => $id,
                'title' => $title,
                'duration' => $duration,
                'artist_id' => $artist_id,
                'file_path' => $file_path,
                'date_added' => $date_added,
                'genre_id' => $genre_id,
                'sub_genre_id' => $sub_genre_id,
                'is_explicit' => $is_explicit,
            ]);

            $_SESSION['success'] = "update succesfull";
        } catch (Exception $e) {
            $_SESSION['error'] = "error: " . $e->getMessage();
            header("Location: songs.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid form data.";
    }
}

header("Location: songs.php");
exit();
