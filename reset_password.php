<?php
session_start();
include 'config.php';

$msg = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Token check
    $stmt = $conn->prepare("SELECT id, reset_expiry FROM users WHERE reset_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check expiry
        if (strtotime($user['reset_expiry']) < time()) {
            $msg = "❌ Reset link expired.";
        } else {
            // If form submitted
          if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_password = $_POST['password'];

    if (strlen($new_password) < 8) {
        $msg = "❌ Password must be at least 8 characters.";
    } else {
        // Yahan hashing hata di
        $update = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expiry=NULL WHERE id=?");
        $update->bind_param("si", $new_password, $user['id']);
        if ($update->execute()) {
            $msg = "✅ Password reset successfully! <a href='login.tml.php'>Login now</a>";
        } else {
            $msg = "❌ Failed to reset password.";
        }
    }
}

        
            
        }
    } else {
        $msg = "❌ Invalid reset link.";
    }
} else {
    $msg = "❌ No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f4f4f4; display:flex; justify-content:center; align-items:center; height:100vh; }
    .container { background:white; padding:25px; border-radius:8px; box-shadow:0 0 10px rgba(0,0,0,0.1); width:350px; }
    h2 { text-align:center; margin-bottom:20px; }
    input { width:100%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:5px; }
    button { width:100%; padding:10px; background:#CDA45E; color:white; border:none; border-radius:5px; cursor:pointer; }
    button:hover { background:#333; }
    p { text-align:center; color:red; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Reset Password</h2>
    <p><?= $msg ?></p>

    <?php if (strpos($msg, "❌") === false && strpos($msg, "✅") === false): ?>
      <form method="POST">
        <input type="password" name="password" placeholder="Enter new password" required />
        <button type="submit">Reset Password</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
