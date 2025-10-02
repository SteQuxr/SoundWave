<?php
session_start();
require_once('../../config/db.php');

$password = $_POST["password"] ?? NULL;
$password2 = $_POST["password2"] ?? NULL;

if ($password === NULL || $password2 === NULL) {

    header("Location: password.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    $_SESSION['error'] = "Invalid request.";
    header("Location: password.php");
    exit;
}

if ($password !== $password2) {
    $_SESSION['error'] = "passwords do not match!";
    header("Location: password.php");
    exit;
}

$password = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :user_id");
    $stmt->execute([
        ':password' => $password,
        ':user_id' => $_SESSION['user_id'],
    ]);
    $_SESSION['success'] = "password updated successfully!";

    header("Location: password.php");
    exit;
} catch (PDOException $e) {
    $_SESSION['error'] = "Error try again";
    header("Location: password.php");
    exit;
}

header("Location: password.php");
