<?php
session_start();
include 'config.php'; // adjust path if needed

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql_user = "SELECT fullname, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql_user);
if (!$stmt) die("Prepare failed: " . $conn->error);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

if (!$user) die("User not found. Please login again.");

// Current timestamp
$current_time = new DateTime();

 
 
$sql_orders = "SELECT * FROM orders 
               WHERE user_id = ? 
               AND payment_status = 'Confirmed' 
               ORDER BY order_date DESC";
$stmt = $conn->prepare($sql_orders);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
$orders = [];

while ($row = $orders_result->fetch_assoc()) {
    $order_time = new DateTime($row['order_date']);
    $elapsed = $current_time->getTimestamp() - $order_time->getTimestamp();

    // Auto expire if older than 30 min and not cancelled
    if (
        isset($row['payment_status']) &&
        strtolower($row['payment_status']) !== 'cancelled' &&
        strtolower($row['payment_status']) !== 'expired' &&
        $elapsed > 1800
    ) {
        $update_sql = "UPDATE orders SET payment_status='Expired' WHERE order_id=?";
        $stmt_upd = $conn->prepare($update_sql);
        $stmt_upd->bind_param("i", $row['order_id']);
        $stmt_upd->execute();
        $stmt_upd->close();
        $row['payment_status'] = 'Expired';
    }
    $orders[] = $row;
}
$stmt->close();


/* ==============================
   Reservations + Auto Expire + Cancel
================================= */
$sql_res = "SELECT * FROM reservations WHERE user_id = ? ORDER BY reservation_date DESC";
$stmt_res = $conn->prepare($sql_res);
$stmt_res->bind_param("i", $user_id);
$stmt_res->execute();
$reservations_result = $stmt_res->get_result();
$reservations = [];

while ($row = $reservations_result->fetch_assoc()) {
    $res_dt_string = $row['reservation_date'] . ' ' . $row['reservation_time'];
    $reservation_dt = DateTime::createFromFormat('Y-m-d H:i:s', $res_dt_string) ?: new DateTime($res_dt_string);

    // Auto expire if reservation time passed
    if (
        isset($row['status']) &&
        strtolower($row['status']) !== 'cancelled' &&
        strtolower($row['status']) !== 'expired' &&
        $reservation_dt < $current_time
    ) {
        $update_res = "UPDATE reservations SET status='Expired' WHERE id=?";
        $stmt_upd_res = $conn->prepare($update_res);
        $stmt_upd_res->bind_param("i", $row['id']);
        $stmt_upd_res->execute();
        $stmt_upd_res->close();
        $row['status'] = 'Expired';
    }
    $reservations[] = $row;
}
$stmt_res->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Profile</title>
  <link rel="stylesheet" href="profile.css" />
  <link rel="stylesheet" href="navbar.css" />
  <style>
    table { width:100%; border-collapse: collapse; margin-bottom:20px; }
    th, td { padding:8px 10px; border:1px solid #ddd; text-align:left; }
    th { background:#f2f2f2; }
    .muted { color: gray; }
    .btn { padding:6px 10px; border-radius:4px; border:0; cursor:pointer; }
    .btn-cancel { background:#c62828; color:#fff; }
  </style>
</head>
<body>
<section class="home">
  <?php include('navbar.php'); ?>
  <div class="container">
    <h1>User Profile</h1>

    <!-- User Info -->
    <div class="card">
      <p><strong>Name:</strong> <?= htmlspecialchars($user['fullname']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    </div>

    <!-- Orders -->
   <!-- Orders -->
<h2>Your Orders</h2>
<?php if (!empty($orders)): ?>
<table>
  <thead>
    <tr>
      <th>Item</th>
      <th>Qty</th>
      <th>Total</th>
      <th>Payment</th>
      <th>Status</th>
      <th>Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($orders as $order): 
    $order_ts = strtotime($order['order_date']);
    $elapsed = time() - $order_ts;

    // Can cancel if within 30 minutes AND not cancelled/expired
    $can_cancel_order = isset($order['payment_status']) &&
                        strtolower($order['payment_status']) !== 'cancelled' &&
                        strtolower($order['payment_status']) !== 'expired' &&
                        $elapsed <= 1800;
?>
<tr>
  <td><?= htmlspecialchars($order['item_name']) ?></td>
  <td><?= htmlspecialchars($order['quantity']) ?></td>
  <td>Rs. <?= number_format($order['total_price'], 2) ?></td>
  <td><?= htmlspecialchars($order['payment_method']) ?></td>
  <td><?= htmlspecialchars($order['payment_status'] ?? 'N/A') ?></td>
  <td><?= htmlspecialchars($order['order_date']) ?></td>
  <td>
    <?php if ($can_cancel_order): ?>
      <form method="POST" action="cancel_order.php" onsubmit="return confirm('Cancel this order?');" style="display:inline">
        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
        <button class="btn btn-cancel" type="submit" name="cancel_order">Cancel</button>
      </form>
    <?php else: ?>
      <span class="muted">Time expired</span>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
  <p>No orders found.</p>
<?php endif; ?>


    <!-- Reservations -->
    <h2>Your Reservations</h2>
    <?php if (!empty($reservations)): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Date</th>
          <th>Time</th>
          <th>Guests</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($reservations as $res):
            $res_dt_string = $res['reservation_date'] . ' ' . $res['reservation_time'];
            $reservation_dt = new DateTime($res_dt_string);
            $now = new DateTime();
            $is_future = ($now < $reservation_dt);
      ?>
        <tr>
          <td><?= htmlspecialchars($res['id']) ?></td>
          <td><?= htmlspecialchars($res['name']) ?></td>
          <td><?= htmlspecialchars($res['reservation_date']) ?></td>
          <td><?= htmlspecialchars($res['reservation_time']) ?></td>
          <td><?= htmlspecialchars($res['guests']) ?></td>
          <td><?= htmlspecialchars($res['status'] ?? 'N/A') ?></td>
          <td>
            <?php if (strtolower($res['status']) === 'cancelled'): ?>
                <span class="muted">Cancelled</span>
            <?php elseif (strtolower($res['status']) === 'expired'): ?>
                <span class="muted">Time expired</span>
            <?php elseif ($is_future): ?>
                <form method="POST" action="cancel_reservation.php" onsubmit="return confirm('Cancel this reservation?');" style="display:inline">
                    <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($res['id']) ?>">
                    <button class="btn btn-cancel" type="submit" name="cancel_reservation">Cancel</button>
                </form>
            <?php else: ?>
                <span class="muted">Time expired</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>No reservations found.</p>
    <?php endif; ?>

  </div>
</section>
</body>
</html>

<?php $conn->close(); ?>
