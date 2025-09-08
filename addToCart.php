<?php
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    // Add item to cart session
    $_SESSION['cart'][] = ['item_id' => $item_id, 'quantity' => $quantity];
    echo "Item added to cart!";
}
?>
