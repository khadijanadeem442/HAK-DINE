<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.tml.php");
    exit;
}

if (isset($_POST['cancel_order'])) {
    $order_id = intval($_POST['order_id']);
    $user_id = intval($_SESSION['user_id']);

    $sql = "UPDATE orders SET payment_status='Cancelled' WHERE order_id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: profile_user.php");
exit;
?>
