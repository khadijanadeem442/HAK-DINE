<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
include 'config.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password']; // simple, not hashed

    // Check duplicate email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "❌ Email already registered.";
        exit;
    }

    // Password length check
    if (strlen($password) < 8) {
        echo "❌ Password must be at least 8 characters long.";
        exit;
    }

    // Generate email verification token
    $token = bin2hex(random_bytes(16));

    // Insert user into DB with is_verified = 0 (without phone number)
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, is_verified, email_verification_token) VALUES (?, ?, ?, 0, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $password, $token);

    if ($stmt->execute()) {
        // Send verification email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'khadija442221@gmail.com'; // replace
            $mail->Password   = 'jjby lnit yrlr xlrq ';    // replace
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('khadija442221@gmail.com', 'HAK DINE');
            $mail->addAddress($email, $fullname);

            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $verifyLink = "http://localhost/project%20HAK%20DINE/verify.php?token=" . $token;
            $mail->Body    = "Hi $fullname,<br><br>Click the link to verify your email:<br><a href='$verifyLink'>$verifyLink</a><br><br>Thank you!";

            $mail->send();
            echo "✅ Registration successful! Check your email to verify your account.";
        } catch (Exception $e) {
            echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ Registration failed. Please try again.";
    }
}
?>
