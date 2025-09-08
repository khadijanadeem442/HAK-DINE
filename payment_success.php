<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.tml.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$items = $_SESSION['cart_items'] ?? [];
$total_price = $_SESSION['cart_total'] ?? 0;
$payment_method = $_SESSION['payment_method'] ?? '';

if (empty($items) || $total_price <= 0 || empty($payment_method)) {
    die("Order data missing.");
}

 $items_json = json_encode($items);

 $sql = "INSERT INTO orders (user_id, items, total_price, payment_method, order_time) VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isds", $user_id, $items_json, $total_price, $payment_method);

if ($stmt->execute()) {
     unset($_SESSION['cart_items'], $_SESSION['cart_total'], $_SESSION['payment_method']);

     header("Location: order_success.php");
    exit;

} else {
    echo "Error placing order: " . $conn->error;
}
?>
