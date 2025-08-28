<!--About--><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE-edge">
        <meta name="viewport" content="width-device-width, initial-scale-1.0">
        <title>HAK DINE</title>
        <link rel="stylesheet" href="home.css">
          <link rel="stylesheet" href="navbar.css">
        
     
    
    </head>
    <body>
        <section class="home">
            <nav class="fill">
           
                <img src="logo-removebg-preview.png" width="100px" height="100px">
                    
                        <ul class="nav-link">
                            <li><a href="home.php">Home</a></li>
                            <li><a href="table.php">Table</a></li>
                            <li><a href="about.php">About us</a></li>   
                            <li><a href="Menu.php">Menu</a></li>
                            <li><a href="contact.php">Contact</a></li>
                            <li><a href="review.php">Reviews</a></li>    
                            <li><a href="login.tml.php">Login</a></li>         
                         </ul>
                    </nav>
<link rel="stylesheet" href="about.css">

<div class="about" id="About">
    <div class="about_main">

        <div class="image">
            <img src="buger.jpg">
        </div>

        <div class="about_text">
            <h1><span>About</span>Us</h1>
            <h3>Why Choose us?</h3>
            <p>
                Welcome to our Restaurant Management System, a smart and efficient solution designed to make restaurant operations smooth and hassle-free.
                 Our system helps restaurant owners manage orders, reservations, menus, and customer interactions all in one place.
                  With an easy-to-use interface, staff can quickly process orders, track inventory, and provide excellent customer service.
                  Our goal is to improve restaurant efficiency, reduce wait times, and enhance the overall dining experience. Whether you run a small café or a large restaurant, our system is here to simplify your daily tasks and help your business grow.


            </p>
        </div>

    </div>

</div> 
 
     ><footer>
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

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<script src="sc.js"></script>

</html>

</body>