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
    $profile_picture = $_POST["profile_picture"] ?? NULL;
    $email = $_POST["email"] ?? NULL;
    $date_of_birth = $_POST["date_of_birth"] ?? NULL;
    $date_joined = $_POST["date_joined"] ?? NULL;
    $login_tries = $_POST["login_tries"] ?? NULL;
    $is_admin = isset($_POST["is_admin"]) ? 1 : 0;

    if ($id && $username && $profile_picture && $email && $date_of_birth && $date_joined && $login_tries) {
        try {
            $stmt = $db->prepare("UPDATE users SET username = :username, profile_picture = :profile_picture, email = :email, date_of_birth = :date_of_birth, date_joined = :date_joined, login_tries = :login_tries, is_admin = :is_admin WHERE id = :id");
            $stmt->execute([
                'id' => $id,
                'username' => $username,
                'profile_picture' => $profile_picture,
                'email' => $email,
                'date_of_birth' => $date_of_birth,
                'date_joined' => $date_joined,
                'login_tries' => $login_tries,
                'is_admin' => $is_admin,
            ]);

            $_SESSION['success'] = "update succesfull";
        } catch (Exception $e) {
            $_SESSION['error'] = "error: " . $e->getMessage();
            header("Location: users.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Invalid form data.";
    }
}

header("Location: users.php");
exit();
