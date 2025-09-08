<?php
include('config.php');

$sql = "SELECT username, review_text, rating FROM reviews ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="review-card animate">';
        echo '<h3>' . htmlspecialchars($row['username']) . '</h3>';
        echo '<p>' . htmlspecialchars($row['review_text']) . '</p>';
        echo '<div class="review-rating">';
        
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $row['rating']) {
                echo '<span class="star filled">&#9733;</span>'; // filled star
            } else {
                echo '<span class="star">&#9733;</span>'; // empty star
            }
        }

        echo '</div>';
        echo '</div>';
    }
} else {
    echo "<p>No reviews yet.</p>";
}
?>
