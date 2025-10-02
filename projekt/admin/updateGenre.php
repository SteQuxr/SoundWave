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
    $name = $_POST["name"] ?? NULL;

    if ($id && $name) {
        try {
            $stmt = $db->prepare("UPDATE genres SET name = :name WHERE id = :id");
            $stmt->execute([
                'id' => $id,
                'name' => $name,
            ]);

            $_SESSION['success'] = "update succesfull";
        } catch (Exception $e) {
            $_SESSION['error'] = "error: " . $e->getMessage();
            header("Location: genres.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid form data.";
    }
}

header("Location: genres.php");
exit();
