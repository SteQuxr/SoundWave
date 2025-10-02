<?php
session_start();
require_once('../../config/db.php');

$username = $_POST["username"] ?? NULL;
$username2 = $_POST["username2"] ?? NULL;

if ($username === NULL || $username2 === NULL) {

    header("Location: nickname.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    $_SESSION['error'] = "Invalid request.";
    header("Location: nickname.php");
    exit;
}

if ($username !== $username2) {
    $_SESSION['error'] = "Nicknames do not match!";
    header("Location: nickname.php");
    exit;
}

try {
    $stmt = $db->prepare("UPDATE users SET username = :username WHERE id = :user_id");
    $stmt->execute([
        ':username' => $username,
        ':user_id' => $_SESSION['user_id'],
    ]);
    $_SESSION['success'] = "Nickname updated successfully!";
    $_SESSION['username'] = $username;


    header("Location: nickname.php");
    exit;
} catch (PDOException $e) {

    if ($e->getCode() == 23000) {
        $_SESSION['error'] = "username already in use";
        header("Location: nickname.php");
    } else {
        $_SESSION['error'] = "Error try again";
        header("Location: nickname.php");
    }
    exit;
}

header("Location: nickname.php");
