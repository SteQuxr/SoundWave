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

    if ($id) {
        try {
            $stmt = $db->prepare("DELETE FROM songs_comments WHERE id = :id");
            $stmt->execute([
                'id' => $id,
            ]);

            $_SESSION['success'] = "delete of comment id = $id succesfull";
        } catch (Exception $e) {
            $_SESSION['error'] = "error: " . $e->getMessage();
            header("Location: posts.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid form data.";
    }
}

header("Location: songs_comments.php");
exit();
