<?php
session_start();
include 'config.php';

// Cancel order
if (isset($_GET['cancel'])) {
    $order_id = intval($_GET['cancel']);
    mysqli_query($conn, "UPDATE orders SET payment_status='Cancelled' WHERE order_id=$order_id");
    header("Location: order.php");
    exit;
}

// fetch orders
$query = "SELECT o.*, u.fullname 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.order_date DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #333;
            padding: 30px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: #CDA45E;
            padding: 25px;
            border-radius: 10px;
        }
        h2 { text-align: center; margin-bottom: 25px; }
        .back-btn { display:inline-block; margin-bottom:20px; padding:10px 18px; background:#333; color:#fff; border-radius:5px; text-decoration:none; }
        .back-btn:hover { background:#444; }
        table { width:100%; border-collapse: collapse; text-align:center; }
        th, td { padding:12px 15px; border-bottom:1px solid #ddd; }
        th { background:#222; color:#fff; }
        .cancel-btn { background:#333; border:none; padding:7px 14px; border-radius:5px; color:#fff; cursor:pointer; font-weight:bold; }
        .cancel-btn:hover { background:#444; }
    </style>
</head>
<body>
<div class="container">
    <a href="dashboard_manager.php" class="back-btn">← Back to Dashboard</a>
    <h2>Order History</h2>
    <table>
        <thead>
            <tr>
                <th>User Name</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total Price</th>
                <th>Payment Method</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= intval($row['quantity']) ?></td>
                    <td>Rs. <?= number_format($row['price'], 2) ?></td>
                    <td>Rs. <?= number_format($row['total_price'], 2) ?></td>
                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['order_date'])) ?></td>
                    <td><?= htmlspecialchars($row['payment_status']) ?></td>
                    <td>
                        <?php if ($row['payment_status'] !== 'Cancelled'): ?>
                        <form method="GET" onsubmit="return confirmCancel();">
                            <input type="hidden" name="cancel" value="<?= $row['order_id'] ?>">
                            <button type="submit" class="cancel-btn">Cancel</button>
                        </form>
                        <?php else: ?>
                            <span style="color: gray;">Cancelled</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9">No orders found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function confirmCancel() {
    return confirm('Are you sure you want to cancel this order?');
}
</script>
</body>
</html>
