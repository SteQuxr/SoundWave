<?php
session_start();
require_once('../../config/db.php');

$date_of_birth = $_POST["date_of_birth"] ?? NULL;
$date_of_birth2 = $_POST["date_of_birth2"] ?? NULL;

if ($date_of_birth === NULL || $date_of_birth2 === NULL) {

    header("Location: date_of_birth.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    $_SESSION['error'] = "Invalid request.";
    header("Location: date_of_birth.php");
    exit;
}

if ($date_of_birth !== $date_of_birth2) {
    $_SESSION['error'] = "dates do not match!";
    header("Location: date_of_birth.php");
    exit;
}

try {
    $stmt = $db->prepare("UPDATE users SET date_of_birth = :date_of_birth WHERE id = :user_id");
    $stmt->execute([
        ':date_of_birth' => $date_of_birth,
        ':user_id' => $_SESSION['user_id'],
    ]);
    $_SESSION['success'] = "date of birth updated successfully!";
    $_SESSION['date_of_birth'] = $date_of_birth;


    header("Location: date_of_birth.php");
    exit;
} catch (PDOException $e) {

    $_SESSION['error'] = "Error try again";
    header("Location: date_of_birth.php");
    exit;
}

header("Location: date_of_birth.php");
