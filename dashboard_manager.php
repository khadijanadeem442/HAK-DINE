<?php
session_start();

// Database connection
$conn = new mysqli("localhost","root","","hak_dine");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


// Function to safely get single stat
function get_stat($conn, $query, $column) {
    $res = $conn->query($query);
    if ($res) {
        $row = $res->fetch_assoc();
        return $row[$column] ?? 0;
    } else {
        error_log("SQL Error (" . $column . "): " . $conn->error);
        return 0;
    }
}

// 1. Update pending orders older than 30 min to Confirmed/Completed
$conn->query("
    UPDATE orders
    SET payment_status='Confirmed'
    WHERE payment_status='Pending' 
      AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) >= 30
");

// 2. Total Revenue (only confirmed orders older than 30 min)
$res = $conn->query("
    SELECT SUM(total_price) AS total_revenue
    FROM orders
    WHERE payment_status='Confirmed'
      AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) >= 30
");
$total_revenue = 0;
if ($res) {
    $row = $res->fetch_assoc();
    $total_revenue = $row['total_revenue'] ?? 0;
}

// 3. Other stats
$total_orders   = get_stat($conn, "SELECT COUNT(*) AS total_orders FROM orders", 'total_orders');
$active_staff   = get_stat($conn, "SELECT COUNT(*) AS active_staff FROM staff", 'active_staff');
$pending_orders = get_stat($conn, "SELECT COUNT(*) AS pending_orders FROM orders WHERE payment_status='Pending'", 'pending_orders');
$total_reviews  = get_stat($conn, "SELECT COUNT(*) AS total_reviews FROM reviews", 'total_reviews');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manager Dashboard - HAK DINE</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body { font-family:'Segoe UI', sans-serif; margin:0; background:#f4f4f4; }
.sidebar { width:220px; height:100vh; background:#333; position:fixed; top:0; left:0; padding-top:20px; }
.sidebar h2 { color:#CDA45E; text-align:center; margin-bottom:30px; }
.sidebar ul { list-style:none; padding:0; }
.sidebar ul li { border-bottom:1px solid #CDA45E; }
.sidebar ul li a { display:block; padding:12px 20px; color:#CDA45E; text-decoration:none; }
.sidebar ul li a:hover { background:#CDA45E; color:#333; font-weight:bold; }
.main-content { margin-left:220px; padding:30px; }
header h1 { color:#333; }
.cards { display:flex; gap:20px; flex-wrap:wrap; }
.card { flex:1 1 200px; background:#fff; padding:25px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.1); text-align:center; transition:transform 0.3s; }
.card:hover { transform:translateY(-5px); }
.card h3 { color:#CDA45E; margin-bottom:10px; }
.card p { font-size:1.8rem; font-weight:bold; color:#333; }
</style>
</head>
<body>

<div class="sidebar">
<h2>HAK DINE</h2>
<ul>
<li><a href="dashboard_manager.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
<li><a href="order.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
<li><a href="staff.php"><i class="fas fa-users"></i> Staff</a></li>
<li><a href="manager_menu.php"><i class="fas fa-utensils"></i> Menu</a></li>
<li><a href="manager_reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
<li><a href="manager_slider.php"><i class="fas fa-image"></i> Slider</a></li>
<li><a href="manager_profile.php"><i class="fas fa-user"></i> Profile</a></li>
<li><a href="reservation_manager.php"><i class="fas fa-user"></i> Reservation</a></li>
<li><a href="manager._contact.php"><i class="fas fa-user"></i> Contact</a></li>


<li><a href="manager_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
</ul>
</div>

<div class="main-content">
<header><h1>Welcome, Manager</h1></header>

<section class="cards">
<div class="card"><h3>Total Revenue</h3><p>Rs. <?= number_format($total_revenue,2) ?></p></div>
<div class="card"><h3>Total Orders</h3><p><?= $total_orders ?></p></div>
<div class="card"><h3>Active Staff</h3><p><?= $active_staff ?></p></div>
<div class="card"><h3>Pending Orders</h3><p><?= $pending_orders ?></p></div>
<div class="card"><h3>Total Reviews</h3><p><?= $total_reviews ?></p></div>
</section>
</div>

</body>
</html>
