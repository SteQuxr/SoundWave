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
    $text = $_POST["text"] ?? NULL;

    if ($id && $title && $text) {
        try {
            $stmt = $db->prepare("
            UPDATE community_posts 
            SET 
                title = :title, 
                text =:text
            WHERE id = :id
            ");

            $stmt->execute([
                'id' => $id,
                'title' => $title,
                'text' => $text,
            ]);

            $_SESSION['success'] = "update succesfull";
        } catch (Exception $e) {
            $_SESSION['error'] = "error: " . $e->getMessage();
            header("Location: posts.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid form data.";
    }
}

header("Location: posts.php");
exit();
