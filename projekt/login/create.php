<?php
session_start();

require_once('..\config\db.php');

$email = $_POST["email"] ?? NULL;
$username = $_POST["username"] ?? NULL;
$password = $_POST["password"] ?? NULL;
$date_of_birth = $_POST["date_of_birth"] ?? null;


if ($username === NULL || $email === NULL || $password === NULL) {

    header("Location: ..\index.php");
    exit;
}

$password = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $db->prepare("INSERT INTO users (email, username, password, date_of_birth, date_joined) VALUES (:email, :username, :password, :date_of_birth, :date_joined)");
    $stmt->execute([
        ':email' => $email,
        ':username' => $username,
        ':password' => $password,
        ':date_of_birth' => $date_of_birth,
        ':date_joined' => date('Y-m-d')
    ]);

    header("Location: login.php");
    exit;
} catch (PDOException $e) {

    if ($e->getCode() == 23000) {
        $_SESSION['error'] = "Email or username already in use";
        header("Location: register.php");
    } else {
        $_SESSION['error'] = "Error try again";
        header("Location: register.php");
    }
    exit;
}

header("Location: login.php");
