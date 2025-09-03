<?php
session_start();
include 'config.php';

// Delete Item
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM menu_items WHERE id=$id"));
    if ($get && file_exists("image/" . $get['image'])) {
        unlink("image/" . $get['image']); // Delete image file
    }
    mysqli_query($conn, "DELETE FROM menu_items WHERE id=$id");
    header("Location: manager_menu.php");
    exit;
}

// Add Item
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // Handle image upload
    $imgName = basename($_FILES['image']['name']);
    $tmp = $_FILES['image']['tmp_name'];
    $target = "image/" . $imgName;
    move_uploaded_file($tmp, $target);

    // Insert into DB
    $query = "INSERT INTO menu_items (name, price, image, category) VALUES ('$name', '$price', '$imgName', '$category')";
    mysqli_query($conn, $query);
    header("Location: manager_menu.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Menu</title>
    <link rel="stylesheet" href="manager.menu.css">
   
</head>
<body>

<div class="container">
    <a href="dashboard_manager.php" class="back-button">← Back to Dashboard</a>

    <h1>Manage Menu</h1>

    <form method="POST" enctype="multipart/form-data" class="add-form">
        <input type="text" name="name" placeholder="Food Name" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <select name="category" required>
            <option value="">-- Select Category --</option>
            <option value="Menu">Menu</option>
            <option value="Fast Food">Fast Food</option>
            <option value="Desi Food">Desi Food</option>
            <option value="Chinese">Chinese</option>
            <option value="Desserts">Desserts</option>
            <option value="Drinks">Drinks</option>
        </select>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="add">Add Item</button>
    </form>

    <h2>Existing Items</h2>
    <table>
        <tr><th>Image</th><th>Name</th><th>Price</th><th>Category</th><th>Action</th></tr>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM menu_items ORDER BY id DESC");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td><img src='image/{$row['image']}' alt='{$row['name']}'></td>
                <td>{$row['name']}</td>
                <td>{$row['price']} rs</td>
                <td>{$row['category']}</td>
                <td><a class='delete-link' href='?delete={$row['id']}' onclick='return confirmDelete();'>Delete</a></td>
            </tr>";
        }
        ?>
    </table>
</div>

<script>
function confirmDelete() {
    return confirm("Are you sure you want to delete this item?");
}
</script>

</body>
</html>
