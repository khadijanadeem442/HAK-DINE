<?php
session_start();
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, password FROM managers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $fullname, $db_password);
        $stmt->fetch();

        
        if ($password === $db_password) {
            $_SESSION['manager_id'] = $id;
            $_SESSION['manager_name'] = $fullname;
            header("Location: dashboard_manager.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Manager Login</title>
<style>
    body {
        background: #CDA45E;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .login-container {
        background: white;
        padding: 40px 30px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        width: 320px;
        text-align: center;
    }
    h2 {
        margin-bottom: 25px;
        color: #333;
    }
    input[type="email"], input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        margin: 10px 0 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }
    input[type="email"]:focus, input[type="password"]:focus {
        outline: none;
        border-color: #333;
    }
    button {
        background: #333;
        color: white;
        border: none;
        padding: 14px;
        width: 100%;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background: transparent;
    }
    .error-message {
        color: #d9534f;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .register-link, .forgot-link {
        margin-top: 20px;
        display: block;
        color: #555;
        font-size: 14px;
        text-decoration: none;
    }
    .register-link:hover, .forgot-link:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="login-container">
    <h2>Manager Login</h2>
    <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="manager_login.php">
        <input type="email" name="email" placeholder="Email" required autofocus />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
    </form>

    <a class="forgot-link" href="forgot_manager.php">Forgot Password?</a>
    
</div>
</body>
</html>
