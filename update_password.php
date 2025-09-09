<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['password'];

    if (strlen($new_password) < 8) {
        die("❌ Password must be at least 8 characters long.");
    }

    // Verify token again
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("❌ Invalid or expired token.");
    }

    $user = $result->fetch_assoc();

    // Update password + clear token
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
    $stmt->bind_param("si", $new_password, $user['id']);
    if ($stmt->execute()) {
        echo "✅ Password updated! <a href='login.tml.php'>Login now</a>.";
    } else {
        echo "❌ Failed to update password.";
    }
}
?>
