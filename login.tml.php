<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HAK DINE</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="home.css">
  <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="navbar.css">

</head>
<body>
<h1 class="fade-In"></h1>
    <section class="home">
        <?php include('navbar.php'); ?>


    <div class="bg"></div>

    <!-- Form Container -->
    <div class="form-container">

      <!-- Login Form -->
     <!-- Login Form -->
<form class="form-box active" id="loginBox"  method="POST" action="login.php">
  <h2>Login</h2>
  <div class="input-group">
    <input type="email" name="email" placeholder="Email" required />
    <i class="icon">👤</i>
  </div>
  <div class="input-group">
    <input type="password" name="password" placeholder="Password" required />
    <i class="icon">🔒</i>
  </div>
  
  <!-- ye part hata diya -->
  <!--
  <div class="options">
    <label><input type="checkbox" /> Remember me</label>
    <a href="#" onclick="showForgot()">Forgot password?</a>
  </div>
  -->

  <!-- sirf forgot password ka link rakh liya -->
  <div class="options">
    <a href="#" onclick="showForgot()">Forgot password?</a>
  </div>

  <button type="submit" class="btn">Login</button>
  <p>Don't have an account? <span onclick="showRegister()">Register</span></p>
</form>


      <!-- Register Form -->
      <!-- Register Form -->
<form class="form-box" id="registerBox" method="POST" action="register.php">
  <h2>Register</h2>
  <div class="input-group">
    <input type="text" name="fullname" placeholder="Full Name" required />
    <i class="icon">👤</i>
  </div>
  <div class="input-group">
    <input type="email" name="email" placeholder="Email" required />
    <i class="icon">📧</i>
  </div>
  <div class="input-group">
    <input type="password" name="password" placeholder="Password" required />
    <i class="icon">🔒</i>
  </div>

  <button type="submit" class="btn">Register</button>
  <p>Already have an account? <span onclick="showLogin()">Login</span></p>
</form>


      <!-- Forgot Password Form -->
      <form class="form-box" id="forgotBox" action="forgot_password.php" method="POST">
        <h2>Forgot Password</h2>
        <div class="input-group">
          <input type="email" name="email" placeholder="Enter your email" required />
          <i class="icon">📧</i>
        </div>
        <button type="submit" class="btn">Send Reset Link</button>
        <p><span onclick="showLogin()">Back to Login</span></p>
      </form>

    </div>


  <footer>
    <div class="footer_main" id="contact">
      <div class="footer_tag">
        <h2>Location</h2>
        <p>Model Town</p>
        <p>Lalazar colony</p>
        <p>Mong</p>
        <p>Sufi City</p>
      </div>
      <div class="footer_tag">
        <h2>Contact</h2>
        <p>+923007745933</p>
        <p>+923328038505</p>
        <p>khadijanadeem442@gmail.com</p>
        <p>romeattia@gmail.com</p>
      </div>
      <div class="footer_tag">
        <h2>Our Service</h2>
        <p>Fast Delivery</p>
        <p>Easy Payments</p>
        <p>24 x 7 Service</p>
      </div>
      <div class="footer_tag">
        <h2>Follows</h2>
        <ion-icon name="logo-facebook"></ion-icon>
        <ion-icon name="logo-instagram"></ion-icon>
        <ion-icon name="mail-outline"></ion-icon>
      </div>
    </div>
  </footer>

  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script>
    function showLogin() {
      document.getElementById('loginBox').classList.add('active');
      document.getElementById('registerBox').classList.remove('active');
      document.getElementById('forgotBox').classList.remove('active');
    }

    function showRegister() {
      document.getElementById('registerBox').classList.add('active');
      document.getElementById('loginBox').classList.remove('active');
      document.getElementById('forgotBox').classList.remove('active');
    }

    function showForgot() {
      document.getElementById('forgotBox').classList.add('active');
      document.getElementById('loginBox').classList.remove('active');
      document.getElementById('registerBox').classList.remove('active');
    }
  </script>
  <script>
  // Only run this after login
  const isLoggedIn = localStorage.getItem("isLoggedIn");

  if (!isLoggedIn || isLoggedIn !== "true") {
    // Hide order button
    const orderButtons = document.querySelectorAll(".order-button");
    orderButtons.forEach(btn => btn.style.display = "none");

    // Optionally hide profile nav link
    const profileLink = document.querySelector('a[href="profile_user.php"]');
    if (profileLink) profileLink.style.display = "none";
  }
</script>

</body>
</html>