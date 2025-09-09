<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.tml.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_SESSION['user_id'];
    $name = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $number = trim($_POST['number']);
    $password = trim($_POST['password']);

    if (!empty($password)) {
        // Store plain text password (Not recommended)
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, number = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $number, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, number = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $number, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Something went wrong!";
    }

    $stmt->close();
}

$conn->close();
header("Location: profile_user.php");
exit;
?>
