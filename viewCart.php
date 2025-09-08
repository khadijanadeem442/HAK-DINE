<?php
session_start();
include('config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode([]);
    exit;
}

$cart = $_SESSION['cart'];
$cart_items = [];

foreach ($cart as $item) {
    $item_id = $item['item_id'];
    $quantity = $item['quantity'];

    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$item_id]);
    $item_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item_data) {
        $cart_items[] = [
            'name' => $item_data['name'],
            'price' => $item_data['price'],
            'quantity' => $quantity,
            'total_price' => $item_data['price'] * $quantity
        ];
    }
}

echo json_encode($cart_items);
?>
