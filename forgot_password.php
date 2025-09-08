<?php
session_start();
include "config.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check user exists
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Save token
        $update = "UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?";
        $stmt2 = $conn->prepare($update);
        $stmt2->bind_param("sss", $token, $expiry, $email);
        $stmt2->execute();

        $reset_link = "http://localhost/project%20HAK%20DINE/reset_password.php?token=" . $token;

        // Send Email
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
               $mail->Username   = 'khadija442221@gmail.com'; // replace
            $mail->Password   = 'jjby lnit yrlr xlrq ';    // replace
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('khadija442221@gmail.com', 'HAK DINE');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - HAK DINE';
            $mail->Body    = "Click here to reset your password: <a href='$reset_link'>$reset_link</a>";

            $mail->send();
            echo "<p>✅ Password reset link has been sent to your email.</p>";
        } catch (Exception $e) {
            echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "<p>❌ No account found with this email.</p>";
    }
}
?>
