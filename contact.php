<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $message = trim($_POST["message"] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo "error";
        exit();
    } else {
        $conn = new mysqli("localhost", "root", "", "hak_dine");
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

        $stmt = $conn->prepare("INSERT INTO contact_messages (name,email,message) VALUES (?,?,?)");
        $stmt->bind_param("sss",$name,$email,$message);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        $stmt->close();
        $conn->close();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HAK DINE - Contact</title>
<link rel="stylesheet" href="home.css">
<link rel="stylesheet" href="navbar.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" defer></script>

<link rel="stylesheet" href="contact.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="contact-header">
    <h1 class="animated-text">Contact Us</h1>
</div>

<section class="contact-container">
    <div class="contact-info">
        <h2 class="animated-text">Contact Information</h2>
        <div class="info-item"><ion-icon name="call-outline"></ion-icon><div><h3>Phone</h3><span><a href="tel:+923328038505">+92 332 8038505</a></span></div></div>
        <div class="info-item"><ion-icon name="mail-outline"></ion-icon><div><h3>Email</h3><span><a href="mailto:romeattia@gmail.com">romeattia@gmail.com</a></span></div></div>
        <div class="info-item"><ion-icon name="location-outline"></ion-icon><div><h3>Address</h3><span><a href="https://maps.app.goo.gl/JGUeoj53cugvajnGA" target="_blank">Mandi Bahaudin</a></span></div></div>
    </div>

    <div class="contact-form">
        <h3 class="animated-text">Send a Message</h3>
        <form action="contact.php" method="POST">
            <input type="text" name="name" placeholder="Your Name" class="form-input" required>
            <input type="email" name="email" placeholder="Your Email" class="form-input" required>
            <textarea name="message" placeholder="Your Message" class="form-input" required></textarea>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>

</section>
<?php include 'footer.php'; ?>
<link rel="stylesheet" href="footer.css">
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<script src="contact .js">
</script>
</body>
</html>

