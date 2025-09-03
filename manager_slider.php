<?php
$conn = new mysqli("localhost", "root", "", "hak_dine");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);


$uploadDir = "../Userinterface/uploads/";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (isset($_POST['update_banner'])) {
        $text = $conn->real_escape_string($_POST['banner_text']);
        $conn->query("UPDATE discount_banner SET text = '$text' WHERE id = 1");
    }

    // Add Slide
    if (isset($_POST['add_slide'])) {
        if (!empty($_FILES['image_file']['name']) && $_FILES['image_file']['error'] == 0) {
            $tmpName = $_FILES['image_file']['tmp_name'];
            $fileName = basename($_FILES['image_file']['name']);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($ext, $allowed)) {
                $newFileName = uniqid('food_') . '.' . $ext;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($tmpName, $destPath)) {
                    $name = $conn->real_escape_string($_POST['food_name']);
                    $price = $conn->real_escape_string($_POST['price']);
                    // Save only the filename
                    $conn->query("INSERT INTO slider_items (image_url, food_name, price) 
                                  VALUES ('$newFileName', '$name', '$price')");
                } else {
                    $errors[] = "❌ Failed to move uploaded file.";
                }
            } else {
                $errors[] = "❌ Invalid image type. Allowed: jpg, jpeg, png, gif.";
            }
        } else {
            $errors[] = "❌ Image file is required.";
        }
    }

    // Delete Slide
    if (isset($_POST['delete_slide'])) {
        $id = intval($_POST['delete_id']);
        $res = $conn->query("SELECT image_url FROM slider_items WHERE id = $id");
        if ($res && $row = $res->fetch_assoc()) {
            $filePath = $uploadDir . $row['image_url'];
            if (file_exists($filePath)) unlink($filePath);
        }
        $conn->query("DELETE FROM slider_items WHERE id = $id");
    }
}


$result = $conn->query("SELECT text FROM discount_banner WHERE id = 1");
$banner = $result && $result->num_rows > 0 ? $result->fetch_assoc()['text'] : '';
$slides = $conn->query("SELECT * FROM slider_items");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager - Slider Control Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #222;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: #333;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(205,164,94,0.3);
            text-align: center;
        }
        h2 {
            color: #CDA45E;
        }
        .form-section {
            margin-bottom: 30px;
        }
        input[type="text"], input[type="number"], input[type="file"] {
            padding: 10px;
            margin: 8px 0;
            width: 80%;
            font-size: 16px;
            border: 1px solid #aaa;
            border-radius: 5px;
        }
        button {
            background-color: #CDA45E;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }
        button:hover {
            background-color: #b18a42;
        }
        .slide-card {
            background: #444;
            margin: 15px auto;
            padding: 15px;
            width: 80%;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(205,164,94,0.4);
        }
        .slide-card img {
            width: 200px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .error {
            color: #ff5555;
            margin-bottom: 10px;
        }
        .back-btn {
            text-decoration: none;
            color: #CDA45E;
            display: inline-block;
            margin-bottom: 25px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard_manager.php" class="back-btn">← Back to Dashboard</a>

    <h2>Update Discount Banner</h2>
    <?php foreach ($errors as $error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <form method="post" class="form-section">
        <input type="text" name="banner_text" value="<?= htmlspecialchars($banner) ?>" required>
        <br>
        <button type="submit" name="update_banner">Update Banner</button>
    </form>

    <h2>Add New Slide</h2>
    <form method="post" enctype="multipart/form-data" class="form-section">
        <input type="file" name="image_file" accept="image/*" required><br>
        <input type="text" name="food_name" placeholder="Food Name" required><br>
        <input type="number" step="0.01" name="price" placeholder="Price" required><br>
        <button type="submit" name="add_slide">Add Slide</button>
    </form>

    <h2>Current Slides</h2>
    <?php if ($slides && $slides->num_rows > 0): ?>
        <?php while ($row = $slides->fetch_assoc()): ?>
            <div class="slide-card">
                <img src="../Userinterface/uploads/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['food_name']) ?>">
                <p><strong><?= htmlspecialchars($row['food_name']) ?></strong></p>
                <p>Price: Rs. <?= number_format($row['price'], 2) ?></p>
                <form method="post" onsubmit="return confirm('Delete this slide?')">
                    <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete_slide">Delete</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No slides found.</p>
    <?php endif; ?>
</div>

</body>
</html>
