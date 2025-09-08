<?php
session_start();
include 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // token check
    $sql = "SELECT * FROM users WHERE email_verification_token = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Update user -> verified
        $update = "UPDATE users SET is_verified = 1, email_verification_token = NULL WHERE id = ?";
        $stmt_update = $conn->prepare($update);
        $stmt_update->bind_param("i", $user['id']);
        $stmt_update->execute();

        echo "<h2>✅ Your email has been verified successfully! You can now <a href='login.tml.php'>login</a>.</h2>";
    } else {
        echo "<h2>❌ Invalid or expired verification link.</h2>";
    }
} else {
    echo "<h2>❌ No token provided.</h2>";
}
