<?php
session_start();
require_once('../config/db.php');

if (!($_SESSION["isLogged"] ?? false) || !($_SESSION["is_admin"] ?? false)) {
    header("Location: ../login/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"] ?? NULL;
    $text = $_POST["text"] ?? NULL;
    $edited = isset($_POST["edited"]) ? 'Y' : 'N';

    if ($id && $text) {
        try {
            $stmt = $db->prepare("
                UPDATE community_comments
                SET 
                    text = :text,
                    edited = :edited
                WHERE id = :id
            ");

            $stmt->execute([
                'id' => $id,
                'text' => $text,
                'edited' => $edited,
            ]);

            $_SESSION['success'] = "Update successful.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid form data.";
    }
}

header("Location: community_comments.php");
exit();
