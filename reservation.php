<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('config.php');

// User login check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$confirmation_message = "";

// Reservation form submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reserve_table'])) {
 
    $name = htmlspecialchars($_POST["name"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $date = $_POST["date"];
    $time = $_POST["time"];
    $guests = (int)$_POST["guests"];

    // Check if date+time already reserved
    $query = "SELECT COUNT(*) as total_reservations 
              FROM reservations 
              WHERE reservation_date = ? AND reservation_time = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $date, $time);
    $stmt->execute();
    $stmt->bind_result($total_reservations);
    $stmt->fetch();
    $stmt->close();

    if ($total_reservations > 0) {
        $confirmation_message = " Sorry, this date and time is already reserved.";
    } else {
        // Insert new reservation
        $sql = "INSERT INTO reservations (user_id, name, phone, reservation_date, reservation_time, guests, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'Pending')";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("issssi", $user_id, $name, $phone, $date, $time, $guests);
            if ($stmt->execute()) {
                $confirmation_message = "✅ Reservation successful! See you on $date at $time.";
            } else {
                $confirmation_message = "❌ Error while reserving. Try again.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>HAK DINE - Table Reservation</title>
  <link rel="stylesheet" href="table.css">
  <link rel="stylesheet" href="navbar.css">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
   <div class="bg"></div>
   <h1 class="fade-In"></h1>

   <section class="home">
        <?php include('navbar.php'); ?>

        <div class="container">
            <div class="image-section">
                <img src="RESERVATION.png" alt="Restaurant Image">
            </div>
            <div class="form-section">
                <h1>Reserve Your Table</h1>
                <form method="POST" action="">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>

                    <label for="phone">Phone:</label>
                    <input type="tel" id="phone" name="phone" required>

                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>

                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" required>

                    <label for="guests">Number of Guests:</label>
                    <input type="number" id="guests" name="guests" min="1" required>
                    <br><br>

                    <button type="submit" name="reserve_table">Reserve Table</button>
                </form>
                <br><br>

                <?php if (!empty($confirmation_message)) : ?>
                    <div id="confirmation"><?php echo $confirmation_message; ?></div>
                <?php endif; ?>
            </div>
        </div>

        <?php include 'footer.php'; ?>
        <link rel="stylesheet" href="footer.css">
   </section>

   <script>
    document.addEventListener("DOMContentLoaded", () => {
      document.querySelector(".container").classList.add("show");
    });
   </script>
   <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
   <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>

