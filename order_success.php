<?php
session_start();
include 'config.php';

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("<script>alert('Please login first to place the order!'); window.location.href='Menu.php';</script>");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['items_data']) || empty($_POST['items_data'])) {
        die("<script>alert('Cart is empty!'); window.location.href='Menu.php';</script>");
    }

    $cart = json_decode($_POST['items_data'], true);
    $payment_method = $_POST['payment_method'] ?? 'Unknown';

    $payment_confirmed = true;

    if ($payment_confirmed && !empty($cart)) {
        foreach ($cart as $item) {
            $item_name = $item['name'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $total_price = $quantity * $price;

            // ✅ Prepare query
            $stmt = $conn->prepare("INSERT INTO orders 
                (user_id, item_name, quantity, price, total_price, payment_method, payment_status) 
                VALUES (?, ?, ?, ?, ?, ?, 'Confirmed')");

            if (!$stmt) {
                die("SQL Error: " . $conn->error); // ✅ Debug line
            }

            $stmt->bind_param("isidds", $user_id, $item_name, $quantity, $price, $total_price, $payment_method);
            $stmt->execute();
        }

        echo "<script>
            alert('Order placed successfully!');
            localStorage.removeItem('cartItems');
            window.location.href='Menu.php';
        </script>";
    } else {
        echo "<script>alert('Payment not confirmed!'); window.location.href='Menu.php';</script>";
    }
} else {
    die("<script>alert('Invalid request!'); window.location.href='Menu.php';</script>");
}
?>
