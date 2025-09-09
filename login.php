<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Debug: check prepare
    $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
   if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}


    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $fullname, $db_password);
        $stmt->fetch();

     if ($password === $db_password) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['fullname'] = $fullname;
    header("Location: profile_user.php");
    exit;
} else {
    echo "❌ Invalid password.";
}

    } else {
        echo "❌ No account found with that email.";
    }

    $stmt->close();
}
?>
