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
    $username = $_POST["username"] ?? NULL;
    $title = $_POST["title"] ?? NULL;

    try {
        if ($id && $username) {
            //user
            $stmt = $db->prepare("SELECT profile_picture FROM users WHERE id = :id");
            $stmt->execute([
                'id' => $id
            ]);
            $user = $stmt->fetch();

            if ($user && $user['profile_picture'] !== 'images/default_pfp.avif') {
                $old_file = "../" . $user['profile_picture'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            $stmt = $db->prepare("UPDATE users SET profile_picture = :default_picture WHERE id = :id");
            $stmt->execute([
                'default_picture' => 'images/default_pfp.avif',
                'id' => $id,
            ]);

            $_SESSION['success'] = "reset successful";
            header("Location: users.php");
            exit;
        }

        if ($id && $title) {
            //song
            $stmt = $db->prepare("SELECT picture_path FROM songs WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $song = $stmt->fetch();

            if ($song && $song['picture_path'] !== 'images/default_song.png') {
                $old_file = "../" . $song['picture_path'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }


            $stmt = $db->prepare("UPDATE songs SET picture_path = :default_picture WHERE id = :id");
            $stmt->execute([
                'default_picture' => 'images/default_song.png',
                'id' => $id,
            ]);

            $_SESSION['success'] = "reset successful";
            header("Location: songs.php");
            exit;
        }

        $_SESSION['error'] = "Invalid form data.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

header("Location: users.php");
exit();
