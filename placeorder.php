<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "hak_dine");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login to place an order.");
}

$user_id = $_SESSION['user_id'];
$item_name = isset($_POST['item_name']) ? trim($_POST['item_name']) : '';
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$total_price = $quantity * $price;
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$order_date = date("Y-m-d H:i:s");
$payment_status = "Pending"; // default

// Validate input
if (empty($item_name) || $price <= 0 || $quantity <= 0 || empty($payment_method)) {
    die("Invalid order data.");
}

// Insert into orders table
$sql = "INSERT INTO orders 
        (user_id, item_name, quantity, price, total_price, payment_method, order_date, payment_status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed (orders): " . $conn->error);
}

$stmt->bind_param("isiddsss", $user_id, $item_name, $quantity, $price, $total_price, $payment_method, $order_date, $payment_status);

if (!$stmt->execute()) {
    die("Error placing order: " . $stmt->error);
}

$order_id = $stmt->insert_id;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <style>
        body {
            font-family: Arial;
            background: #fff8ef;
            text-align: center;
            padding-top: 100px;
        }
        .success-box {
            display: inline-block;
            padding: 30px;
            background: #CDA45E;
            color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
        }
        .success-box h2 {
            margin: 0 0 10px;
        }
        .success-box a {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="success-box">
        <h2>🎉 Order Confirmed!</h2>
        <p>Your order for <strong><?= htmlspecialchars($item_name) ?></strong> has been placed.</p>
        <p>Quantity: <?= $quantity ?> × Rs. <?= number_format($price, 2) ?></p>
        <p>Total: Rs. <?= number_format($total_price, 2) ?></p>
        <p>Payment Method: <?= htmlspecialchars($payment_method) ?></p>
        <br>
        <a href="home.php">← Back to home</a>
    </div>
</body>
</html>
