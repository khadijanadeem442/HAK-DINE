<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="fill">
    <img src="logo-removebg-preview.png" width="120px" height="120px">
    <ul class="nav-link">
        <li><a href="home.php">Home</a></li>
        <li><a href="reservation.php">Table</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="Menu.php">Menu</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="review.php">Reviews</a></li>

        <?php
        if (isset($_SESSION['user_id'])) {
            $name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'User';
            echo '<li><a href="profile_user.php">Profile (' . htmlspecialchars($name) . ')</a></li>';
            echo '<li><a href="logout.php">Logout</a></li>';
        } else {
            echo '<li><a href="login.tml.php">Login</a></li>';
        }
        ?>
    </ul>
</nav>
