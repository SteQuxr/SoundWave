<?php
session_start();
require_once('../../config/db.php');

$target_dir = "../../images/";
$filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
$target_file = $target_dir . $filename;

try {
    if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
        throw new Exception("File upload error: " . $_FILES["image"]["error"]);
    }

    // File type
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed");
    }

    // File size
    list($width, $height) = getimagesize($_FILES["image"]["tmp_name"]);
    if ($width > 400 || $height > 400) {
        throw new Exception("Max size 400 x 400");
    }

    // delete old
    $stmt = $db->prepare("SELECT profile_picture FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    if ($user && $user['profile_picture'] !== "images/default_pfp.avif") {
        $old_file = "../../" . $user['profile_picture'];
        if (file_exists($old_file)) {
            unlink($old_file);
        }
    }


    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        throw new Exception("Failed to move uploaded file");
    }


    $stmt = $db->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :user_id");
    $stmt->execute([
        ':profile_picture' => "images/" . $filename,
        ':user_id' => $_SESSION['user_id'],
    ]);

    $_SESSION['success'] = "Profile picture updated successfully!";


    $_SESSION['profile_picture'] = "images/" . $filename;

    header("Location: pfp.php");
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Upload failed: " . $e->getMessage();
    header("Location: pfp.php");
    exit;
}
