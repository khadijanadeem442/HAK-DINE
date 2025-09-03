<?php
session_start();
include 'config.php';

if (!isset($_SESSION['manager_id'])) {
    header("Location: manager_login.php");
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delQuery = "DELETE FROM contact_messages WHERE id = $id";
    mysqli_query($conn, $delQuery);
    header("Location: contact_messages.php"); // Redirect to refresh the page
    exit;
}

// Fetch messages
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$results = mysqli_query($conn, $query);

if (!$results) {
    die("Database query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Messages</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #333;
            padding: 20px;
        }

        .container {
            max-width: 950px;
            margin: auto;
            background: #CDA45E;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .back-btn {
            display: block;
            margin: 0 auto 20px auto;
            padding: 10px 15px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            width: 150px;
        }

        .back-btn:hover {
            background: rgba(7, 7, 7, 0.53);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #444;
            color: white;
        }

        .delete-btn {
            background: #c00;
            color: white;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .delete-btn:hover {
            background: #a00;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Contact Messages</h2>

    <a href="dashboard_manager.php" class="back-btn">← Back</a>

    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($results)) { ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['message']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
            <td>
                <a href="contact_messages.php?delete=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
