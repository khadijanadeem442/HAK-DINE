<?php
session_start();
include 'config.php';

 if (!isset($_SESSION['user_id'])) {
    header("Location: login.tml.php"); 
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['cart_items']) || !isset($_SESSION['cart_total'])) {
    die("❌ No cart data found. Please add items to cart first.");
}

$items = json_encode($_SESSION['cart_items']);
$total_price = $_SESSION['cart_total'];
$payment_method = "EasyPaisa";     
$payment_status = "Pending";       
$order_date = date('Y-m-d H:i:s');


$sql = "INSERT INTO orders (user_id, items, total_price, payment_method, payment_status, order_time)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isdsss", $user_id, $items, $total_price, $payment_method, $payment_status, $order_date);

if ($stmt->execute()) {

    unset($_SESSION['cart_items'], $_SESSION['cart_total']);

    
    header("Location: order_success.php");
    exit;
} else {
    echo "❌ Error placing order: " . htmlspecialchars($conn->error);
}

$stmt->close();
$conn->close();
?>
