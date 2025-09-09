<?php
session_start();
include 'config.php';

if (!isset($_SESSION['manager_id'])) {
    header("Location: manager_login.php");
    exit;
}


if (isset($_POST['update_status'])) {
    $reservation_id = isset($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    // Allowed statuses only
    $allowed = ['Confirmed', 'Cancelled'];
    if ($reservation_id > 0 && in_array($status, $allowed, true)) {
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $status, $reservation_id);
            $stmt->execute();
            $stmt->close();
        }
    }
    header("Location: reservation_manager.php"); // same file par refresh
    exit;
}

// ---- Reservations fetch ----
$sql = "SELECT * FROM reservations ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . htmlspecialchars(mysqli_error($conn)));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reservations Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; background: #CDA45E; margin: 0; padding: 20px; }
    h2 { text-align: center; color: #444; margin-bottom: 20px; }
    .back-btn { display: inline-block; margin-bottom: 15px; padding: 10px 15px; background: #333; color: #CDA45E; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.3s; }
    .back-btn:hover { background: #555; }
    table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: center; }
    th { background: #333; color: #fff; }
    tr:nth-child(even) { background: #f2f2f2; }
    .status { padding: 5px 10px; border-radius: 4px; font-weight: bold; }
    .status.Expired { background: #e74c3c; color: #fff; }
    .status.Cancelled { background: #f39c12; color: #fff; }
    .status.Confirmed { background: #2ecc71; color: #fff; }
    .status.Pending { background: #3498db; color: #fff; }
    .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
    .confirm-btn { background: #2ecc71; color: #fff; }
    .cancel-btn { background: #e74c3c; color: #fff; }
    .muted { color: #444; font-style: italic; }
  </style>
</head>
<body>
  <h2>Reservation Details</h2>

  <a href="dashboard_manager.php" class="back-btn">⬅ Back to Dashboard</a>

  <table>
    <tr>
      <th>ID</th>
      <th>User ID</th>
      <th>Name</th>
      <th>Phone</th>
      <th>Date</th>
      <th>Time</th>
      <th>Guests</th>
      <th>Status</th>
      <th>Created At</th>
      <th>Action</th>
    </tr>

    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= (int)$row['id']; ?></td>
          <td><?= (int)$row['user_id']; ?></td>
          <td><?= htmlspecialchars($row['name']); ?></td>
          <td><?= htmlspecialchars($row['phone']); ?></td>
          <td><?= htmlspecialchars($row['reservation_date']); ?></td>
          <td><?= htmlspecialchars($row['reservation_time']); ?></td>
          <td><?= (int)$row['guests']; ?></td>
          <td><span class="status <?= htmlspecialchars($row['status']); ?>"><?= htmlspecialchars($row['status']); ?></span></td>
          <td><?= htmlspecialchars($row['created_at']); ?></td>
          <td>
            <?php if (strcasecmp($row['status'], 'Pending') === 0): ?>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="reservation_id" value="<?= (int)$row['id']; ?>">
                <input type="hidden" name="status" value="Confirmed">
                <button type="submit" name="update_status" class="btn confirm-btn">Confirm</button>
              </form>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="reservation_id" value="<?= (int)$row['id']; ?>">
                <input type="hidden" name="status" value="Cancelled">
                <button type="submit" name="update_status" class="btn cancel-btn">Cancel</button>
              </form>
            <?php else: ?>
              <span class="muted">No Action</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="10">No reservations found.</td></tr>
    <?php endif; ?>
  </table>
</body>
</html>
