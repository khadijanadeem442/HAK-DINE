<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.tml.php");
    exit;
}

if (isset($_POST['cancel_reservation'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $user_id = intval($_SESSION['user_id']);

    $sql = "UPDATE reservations SET status='Cancelled' WHERE id=? AND user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: profile_user.php");
exit;
?>
