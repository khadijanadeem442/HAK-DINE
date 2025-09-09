<?php
session_start();
include 'config.php'; // path jahan apka db config hai

// Delete review
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_query = "DELETE FROM reviews WHERE id = $id";
    mysqli_query($conn, $delete_query);
    header("Location: manager_reviews.php");
    exit;
}


$reviews_query = "SELECT * FROM reviews ORDER BY created_at DESC";
$result = mysqli_query($conn, $reviews_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Reviews</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #333;
      padding: 20px;
    }
    .container {
      max-width: 900px;
      margin: auto;
      background: #CDA45E;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    th, td {
      padding: 12px 15px;
      border: 1px solid #ddd;
      text-align: left;
    }
    th {
      background: #eee;
    }
    .delete-btn {
      background: #333;
      color: white;
      border: none;
      padding: 7px 12px;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
    }
    .delete-btn:hover {
      background:hsla(40, 11.10%, 10.60%, 0.81);
    }
    .back-btn {
      display: inline-block;
      padding: 10px 15px;
      background: #333;
      color: white;
      border-radius: 5px;
      text-decoration: none;
      margin-bottom: 20px;
    }
    .back-btn:hover {
      background:rgba(32, 34, 35, 0.43);
    }
  </style>
</head>
<body>

<div class="container">
  <a href="dashboard_manager.php" class="back-btn">← Back to Dashboard</a>
  <h1>Customer Reviews Management</h1>
  <?php if(mysqli_num_rows($result) > 0): ?>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Review</th>
        <th>Rating</th>
        <th>Created At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?php echo htmlspecialchars($row['id']); ?></td>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo htmlspecialchars($row['review_text']); ?></td>
        <td><?php echo intval($row['rating']); ?>/5</td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        <td>
          <a 
            href="manager_reviews.php?delete=<?php echo $row['id']; ?>" 
            class="delete-btn"
            onclick="return confirm('Are you sure you want to delete this review?');"
          >
            Delete
          </a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php else: ?>
    <p>No reviews found.</p>
  <?php endif; ?>
</div>

</body>
</html>
