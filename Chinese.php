<?php
session_start();
include 'config.php';   

$isLoggedIn = isset($_SESSION['email']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Desi Food</title>
<link rel="stylesheet" href="menu.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<section class="home">
<nav class="fill">
    <img src="logo-removebg-preview.png" width="100px" height="100px">
    <ul class="nav-link">
        <li><a href="home.php">Home</a></li>
        <li><a href="Menu.php">Menu</a></li>
        <li><a href="FastFood.php">Fast food</a></li>
        <li><a href="DesiFood.php">Desi Food</a></li>   
        <li><a href="Desserts.php">Desserts</a></li>
        <li><a href="Drinks.php">Drinks</a></li>
        <li><a href="Chinese.php">Chinese</a></li>    
        <li><a href="contact.php">Contact</a></li>
        <li>
            <a href="#" id="cart-btn">
                <i class="fa fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </a>
        </li>
    </ul>
</nav>

<!-- Cart Sidebar -->
<div class="cart" id="cart">
    <div class="cart-header">
        <h2>Cart Items</h2>
        <i class="fa fa-close" id="cart-close"></i>
    </div>

    <div class="cart-content"></div>

    <div class="total">
        <span>Total: </span><span class="total-price">Rs.0</span>
    </div>
    <br>

  <?php if($isLoggedIn): ?>
    <button id="placeOrderBtn" class="btn-buy">Place Order</button>
    <div id="paymentDiv" style="display:none;">
        <h3>Payment Method</h3>
        <div class="manual-payment">
            <h4>Send Payment via EasyPaisa</h4>
            <p><strong>Send to EasyPaisa Account:</strong> 0300-7745933</p>
            <p><strong>Account Name:</strong> HAK DINE</p>
            <!-- ✅ Add QR code image here -->
            <img src="QR.jpg" alt="EasyPaisa QR Code" style="width:200px; display:block; margin-bottom:10px;">
            
            <form action="order_success.php" method="POST" onsubmit="return prepareOrder();">
                <input type="hidden" name="total_price" id="total_price_input">
                <input type="hidden" name="payment_method" value="EasyPaisa">
                <input type="hidden" name="items_data" id="items_data">
                <input type="submit" value="Confirm Payment" class="submit-btn">
            </form>
        </div>
    </div>
<?php else: ?>
    <p style="color:red;">You must login to place an order!</p>
<?php endif; ?>

        
</div>

<!-- JS for Cart -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const cartBtn = document.getElementById("cart-btn");
    const cart = document.getElementById("cart");
    const cartClose = document.getElementById("cart-close");
    const cartContent = document.querySelector(".cart-content");
    const totalPriceEl = document.querySelector(".total-price");
    const cartCount = document.querySelector(".cart-count");
    const placeOrderBtn = document.getElementById("placeOrderBtn");
    const paymentDiv = document.getElementById("paymentDiv");

    let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

    cartBtn.onclick = (e) => { e.preventDefault(); cart.classList.add("active"); };
    cartClose.onclick = () => cart.classList.remove("active");

    function attachAddCartListeners() {
        document.querySelectorAll(".add-cart").forEach(btn => {
            btn.onclick = () => {
                const foodBox = btn.parentElement;
                const name = foodBox.querySelector(".food-title").textContent;
                const price = parseInt(foodBox.querySelector(".food-price").textContent.replace("rs",""));
                const img = foodBox.querySelector(".food-img").getAttribute("src");

                const existing = cartItems.find(item=>item.name===name);
                if(existing){ existing.quantity++; } 
                else { cartItems.push({name, price, img, quantity:1}); }

                saveCart();
                alert(name + " added to cart ✅");
            }
        });
    }

    function updateCart() {
        cartContent.innerHTML = "";
        let total = 0, count = 0;

        cartItems.forEach((item, index) => {
            total += item.price * item.quantity;
            count += item.quantity;

            const div = document.createElement("div");
            div.classList.add("cart-box");
            div.innerHTML = `
                <img src="${item.img}" class="cart-img">
                <div class="detail-box">
                    <div class="cart-food-title">${item.name}</div>
                    <div class="price-box">
                        Rs.${item.price} x 
                        <button class="qty-btn decrease" data-index="${index}">-</button>
                        <span class="qty">${item.quantity}</span>
                        <button class="qty-btn increase" data-index="${index}">+</button>
                    </div>
                </div>
                <i class="fa fa-trash cart-remove" data-index="${index}"></i>
            `;
            cartContent.appendChild(div);
        });

        totalPriceEl.textContent = "Rs." + total;
        cartCount.textContent = count;

        if(placeOrderBtn){
            placeOrderBtn.style.display = count>0 ? "block" : "none";
            if(paymentDiv) paymentDiv.style.display = "none";
        }

        document.getElementById("total_price_input")?.setAttribute("value", total);
        document.getElementById("items_data")?.setAttribute("value", JSON.stringify(cartItems));

        attachCartButtons();
    }

    function attachCartButtons() {
        document.querySelectorAll(".qty-btn.increase").forEach(btn => {
            btn.onclick = () => { 
                const i = btn.dataset.index; cartItems[i].quantity++; saveCart();
            };
        });
        document.querySelectorAll(".qty-btn.decrease").forEach(btn => {
            btn.onclick = () => {
                const i = btn.dataset.index;
                if(cartItems[i].quantity>1) cartItems[i].quantity--; 
                else cartItems.splice(i,1);
                saveCart();
            };
        });
        document.querySelectorAll(".cart-remove").forEach(btn => {
            btn.onclick = () => {
                const i = btn.dataset.index;
                cartItems.splice(i,1);
                saveCart();
            };
        });
    }

    function saveCart(){
        localStorage.setItem("cartItems", JSON.stringify(cartItems));
        updateCart();
    }

    <?php if($isLoggedIn): ?>
    placeOrderBtn?.addEventListener("click", ()=> {
        placeOrderBtn.style.display="none";
        paymentDiv.style.display="block";
    });
    <?php endif; ?>

    window.prepareOrder = function(){
        if(cartItems.length===0){ alert("Cart is empty!"); return false; }
        document.getElementById("total_price_input").value = cartItems.reduce((sum,item)=>sum+(item.price*item.quantity),0);
        document.getElementById("items_data").value = JSON.stringify(cartItems);
        return true;
    }

    updateCart();
    attachAddCartListeners();
    <?php if($isLoggedIn): ?>
placeOrderBtn?.addEventListener("click", ()=> {
    placeOrderBtn.style.display="none";
    paymentDiv.style.display="block";
});
<?php endif; ?>

});
</script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
        
         <div class="video-container">
             <video autoplay loop muted playsinline class="background-video">
                 <source src="chinese.mp4" type="video/mp4">
             </video>
         </div>
      
         <section class="menu-header">
             <img src="image/chow mein.jpg" alt="Food Icon" class="food-icon">
             <h1>Chinese</h1>
           <p>Discover over yummy menu</p>
         </section>
     
          <div class="menu-categories">
             <div class="category">Appetizers</div>
             <div class="category">Main Course</div>
             <div class="category">Desserts</div>
             <div class="category">Beverages</div>
         </div>
     
         <script>
              gsap.to("#background-video", { opacity: 1, duration: 1, ease: "power2.out" });
     
              gsap.to(".food-icon", { opacity: 1, scale: 1, duration: 1, ease: "bounce.out", delay: 1 });
             gsap.to(".menu-header h1", { opacity: 1, y: 0, duration: 1, ease: "power2.out", delay: 0.3 });
             gsap.to(".menu-header p", { opacity: 1, y: 0, duration: 1, ease: "power2.out", delay: 1.2 });
             gsap.to(".menu-categories", { opacity: 1, y: 0, duration: 1, ease: "power2.out", delay: 1.5 });
     
             gsap.from(".category", {
                 opacity: 0,
                 y: 30,
                 duration: 0.8,
                 stagger: 0.2,
                 ease: "power2.out",
                 delay: 1.8
             });
         </script>   
<!-- Menu Items from DB -->
<div class="container">
<?php
$sql = "SELECT * FROM menu_items";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)){
    echo '<div class="food-box">
        <img src="image/'.htmlspecialchars($row['image']).'" class="food-img">
        <h2 class="food-title">'.htmlspecialchars($row['name']).'</h2>
        <h3 class="food-price">'.htmlspecialchars($row['price']).'rs</h3>
        <button class="add-cart">Add to Cart</button>
    </div>';
}
?>
</div>

</section>
</body>
</html>
