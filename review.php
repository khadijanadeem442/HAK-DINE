<?php
include('config.php');

// review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? null;
    $review_text = $_POST['review_text'] ?? null;
    $rating = $_POST['rating'] ?? null;

    if ($username && $review_text && $rating) {
        $sql = "INSERT INTO reviews (username, review_text, rating) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $username, $review_text, $rating);
        $stmt->execute();
        header("Location: review.php?success=1");
        exit();
    }
}

$reviews = [];
$sql = "SELECT username, review_text, rating FROM reviews ORDER BY id DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

// rating
$avgRating = 0;
if (!empty($reviews)) {
    $sum = array_sum(array_column($reviews, 'rating'));
    $avgRating = round($sum / count($reviews), 1);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HAK DINE</title>
    <link rel="stylesheet" href="review.css" />
</head>
<body>
    <div class="bg"></div>
   <h1 class="fade-In"></h1>
    <section class="home">
        <?php include('navbar.php'); ?>


        <!-- Success message -->
        <?php if (isset($_GET['success'])): ?>
            <script>alert("✅ Review submitted successfully!");</script>
        <?php endif; ?>

        <!-- Review Section -->
        <section class="review-section animate">
            <h1 class="title">Customer Reviews</h1>

            <!-- Review Form -->
            <div class="review-form">
                <form action="review.php" method="POST">
                    <input type="text" name="username" placeholder="Your name" required />
                    <textarea name="review_text" placeholder="Write your review here..." required></textarea>
                    
                    <!-- Rating system -->
                    <div class="rating">
                        <label for="stars">Rate this restaurant: </label>
                        <input type="radio" id="star5" name="rating" value="5" />
                        <label for="star5" class="star">&#9733;</label>
                        <input type="radio" id="star4" name="rating" value="4" />
                        <label for="star4" class="star">&#9733;</label>
                        <input type="radio" id="star3" name="rating" value="3" />
                        <label for="star3" class="star">&#9733;</label>
                        <input type="radio" id="star2" name="rating" value="2" />
                        <label for="star2" class="star">&#9733;</label>
                        <input type="radio" id="star1" name="rating" value="1" />
                        <label for="star1" class="star">&#9733;</label>
                    </div>

                    <button class="submit-btn">Submit Review</button>
                </form>
            </div>

            <!-- Display Reviews -->
            <div class="reviews">
               <?php foreach ($reviews as $review): ?>
    <div class="review-card animate">
        <h3><?= htmlspecialchars($review['username']) ?></h3>
        <p><?= htmlspecialchars($review['review_text']) ?></p>
        <div class="review-rating">
            <strong>Rating: </strong>
            <?php
            $rating = $review['rating'];
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $rating) {
                    echo '<span class="star">&#9733;</span>'; // Filled star
                } else {
                    echo '<span class="star">&#9734;</span>'; // Empty star
                }
            }
            ?>
        </div>
    </div>
<?php endforeach; ?>

            </div>
        </section>

       <footer>
    <div class="footer_main" id="contact">

        <div class="footer_tag">
            <h2>Location</h2>
            <p>
        <a href="https://maps.app.goo.gl/JGUeoj53cugvajnGA" target="_blank">
            Lalazar Colony
        </a>
    </p>
    
            <p> 
                <a href="https://maps.app.goo.gl/GdqTGGXMLC66GvYR6" target="_blank">
            Model Town
        </a>
            </p>
           <p>   
                <a href="https://maps.app.goo.gl/rvChaFZBgK2E8GdSA" target="_blank">
            Mong
        </a>
            </p>
        </div>

        <div class="footer_tag">
            <h2>Contact</h2>
            <p><a href="tel:+923007745933">+92 300 7745933</a></p>
            <p><a href="tel:+923328038505">+92 332 8038505</a></p>
             <p><a href="tel:+923417650557">+923417650557</a></p>
         
        </div>

        <div class="footer_tag">
            <h2>Email</h2>
            <p><a href="mailto:khadijanadeem442@gmail.com">khadijanadeem442@gmail.com</a></p>
            <p><a href="mailto:romeattia@gmail.com">romeattia@gmail.com</a></p>
            <p><a href="mailto:hifsaashfaqdar@gmail.com">hifsaashfaqdar@gmail.com</a></p>
        </div>

        <div class="footer_tag">
            <h2>Follows</h2>
            
            <!-- Facebook -->
            <a href="https://www.facebook.com/share/19rqy41a67/" target="_blank">
                <ion-icon name="logo-facebook"></ion-icon>
            </a>

            <!-- Instagram -->
            <a href="https://www.instagram.com/hak_dine12?igsh=ZnR3eGpjZjl2ZnE4&utm_source=qr" target="_blank">
                <ion-icon name="logo-instagram"></ion-icon>
            </a>

            <!-- Email -->
            <a href="mailto:khadijanadeem442@gmail.com">
                <ion-icon name="mail-outline"></ion-icon>
            </a>
        </div>

    </div>
</footer>
    </section>

    <script>
         const cards = document.querySelectorAll('.review-card');
        window.addEventListener('scroll', () => {
            cards.forEach(card => {
                const cardTop = card.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                if (cardTop < windowHeight - 100) {
                    card.classList.add('show');
                }
            });
        });
    </script>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
