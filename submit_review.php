<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? null;
    $review_text = $_POST['review_text'] ?? null;
    $rating = $_POST['rating'] ?? null;

    if ($username && $review_text && $rating) {
        $sql = "INSERT INTO reviews (username, review_text, rating) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $username, $review_text, $rating);

        if ($stmt->execute()) {
            header("Location: review.php?success=1");
            exit();
        } else {
            echo "❌ Error: " . $stmt->error;
        }
    } else {
        echo "⚠️ All fields are required.";
    }
}
?>
