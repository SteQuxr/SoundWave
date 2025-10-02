<?php
session_start();
require_once('../../config/db.php');

$email = $_POST["email"] ?? NULL;
$email2 = $_POST["email2"] ?? NULL;

if ($email === NULL || $email2 === NULL) {

    header("Location: email.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    $_SESSION['error'] = "Invalid request.";
    header("Location: email.php");
    exit;
}

if ($email !== $email2) {
    $_SESSION['error'] = "emails do not match!";
    header("Location: email.php");
    exit;
}

try {
    $stmt = $db->prepare("UPDATE users SET email = :email WHERE id = :user_id");
    $stmt->execute([
        ':email' => $email,
        ':user_id' => $_SESSION['user_id'],
    ]);
    $_SESSION['success'] = "email updated successfully!";
    $_SESSION['email'] = $email;


    header("Location: email.php");
    exit;
} catch (PDOException $e) {

    if ($e->getCode() == 23000) {
        $_SESSION['error'] = "email already in use";
        header("Location: email.php");
    } else {
        $_SESSION['error'] = "Error try again";
        header("Location: email.php");
    }
    exit;
}

header("Location: email.php");
