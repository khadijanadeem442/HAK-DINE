<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$conn = new mysqli("localhost", "root", "", "hak_dine");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$isLoggedIn = isset($_SESSION['user_id']);

$result = $conn->query("SELECT text FROM discount_banner WHERE id=1");
$discount = "Enjoy our top dishes!";
if ($result && $row = $result->fetch_assoc()) {
    $discount = $row['text'];
}

$items = $conn->query("SELECT * FROM slider_items");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Food Slider</title>
  <style>
     
    .discount-banner {
      margin-top: 20px;
      font-size: 24px;
      background-color: #CDA45E;
      color: white;
      padding: 12px 25px;
      border-radius: 8px;
      margin: 0 auto 30px;
      display: block;
      width: max-content;
      box-shadow: 0 0 10px rgba(205, 164, 94, 0.7);
    }
    .slider-container {
      position: relative;
      margin: 0 auto;
      padding: 20px 0;
      max-width: 90%;
      overflow: hidden;
    }
    .slider {
      display: flex;
      transition: transform 0.5s ease-in-out;
    }
    .slide {
      min-width: 500px;
      height: 300px;
      margin: 0 10px;
      border-radius: 20px;
      overflow: hidden;
      opacity: 0.4;
      transition: 0.3s ease;
      background: #fff6e5;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }
    .slide img {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }
    .slide.active {
      transform: scale(1.1);
      box-shadow: 0 0 20px #CDA45E;
      opacity: 1;
    }
    .food-info {
      text-align: center;
      padding: 10px;
      background-color: #fff;
      border-bottom-left-radius: 20px;
      border-bottom-right-radius: 20px;
    }
    .food-info h3 {
      margin: 5px 0;
      font-size: 18px;
      color: #2c1706;
    }
    .food-info p {
      margin: 0;
      font-weight: bold;
      color: #CDA45E;
      font-size: 16px;
    }
    .nav-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      font-size: 40px;
      background-color: #CDA45E;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 10px;
      cursor: pointer;
      z-index: 2;
    }
    .left-btn { left: 10px; }
    .right-btn { right: 10px; }

    .order-btn {
      display: block;
      background-color: #CDA45E;
      color: white;
      border: none;
      padding: 12px 25px;
      font-size: 16px;
      border-radius: 8px;
      margin: 25px auto;
      cursor: pointer;
      box-shadow: 0 0 10px rgba(205, 164, 94, 0.7);
    }

    #orderModal, #modalOverlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height:300px;
    }
#modalOverlay {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%;
  height: 100%;   /* ✅ full screen */
  background: rgba(0,0,0,0.5);
  z-index: 9;
}

#orderModal {
  display: none;
  position: fixed;
  z-index: 10;
  background: white;
  width: 350px;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 20px;
  border-radius: 10px;
  font-family: Arial;
}

    #orderModal h3 {
      margin-top: 0;
    }

    #orderModal button {
      background-color: #CDA45E;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 6px;
      width: 100%;
      font-size: 16px;
      margin-top: 10px;
    }

    #orderModal .cancel-btn {
      background: none;
      color: #CDA45E;
      font-size: 14px;
      cursor: pointer;
    }

    #orderModal select {
      width: 100%;
      padding: 8px;
      margin-top: 10px;
    }

    #easypaisaQR {
      display: none;
      text-align: center;
      margin-top: 15px;
    }

    #easypaisaQR img {
      width: 200px;
      height: 200px;
      object-fit: contain;
    }
  </style>
</head>
<body>
<br>
<br>
<div class="discount-banner"><?= htmlspecialchars($discount) ?></div>

<div class="slider-container">
  <button class="nav-btn left-btn" onclick="moveSlide(-1)">&#10094;</button>
  <div class="slider" id="slider">
    <?php if($items && $items->num_rows > 0): ?>
      <?php while($row = $items->fetch_assoc()): ?>
        <?php
          $imageUrl = $row['image_url'];
          $fullPath = __DIR__ . "/../uploads/" . $imageUrl;  // Server side check
          $webPath  = "../uploads/" . $imageUrl;             // For <img src>
          $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
          $ext = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));
        ?>
        <?php if (in_array($ext, $allowedExtensions) && file_exists($fullPath)): ?>
          <div class="slide">
            <img src="<?= $webPath ?>" alt="<?= htmlspecialchars($row['food_name']) ?>" />
            <div class="food-info">
              <h3><?= htmlspecialchars($row['food_name']) ?></h3>
              <p>Rs. <?= number_format($row['price'], 2) ?></p>
            </div>
          </div>
        <?php else: ?>
          <p>⚠️ Image not found: <?= htmlspecialchars($imageUrl) ?></p>
        <?php endif; ?>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No food items found.</p>
    <?php endif; ?>
  </div>
  <button class="nav-btn right-btn" onclick="moveSlide(1)">&#10095;</button>
</div>

 
<button class="order-btn" onclick="orderFood()">Order This Food</button>

<div id="modalOverlay" onclick="closeModal()"></div>

<div id="orderModal">
  <h3>Order Confirmation</h3>
  <p id="foodName"></p>
  <p id="foodPrice"></p>
  <form method="post" action="placeorder.php">
  <input type="hidden" name="item_name" id="formItems" />
  <input type="hidden" name="price" id="formPrice" />
  <input type="hidden" name="quantity" value="1" /> <!-- Default 1 -->
  
  <label>Payment Method:</label>
  <select id="paymentMethod" name="payment_method" required>
    <option value="">Select</option>
    <option value="easypaisa_qr">EasyPaisa QR</option>
  </select>
  <div id="easypaisaQR">
    <p>Scan QR to pay:</p>
    <img src="easypaisa-qr.png" alt="QR Code">
  </div>
  <button type="submit">Confirm & Pay</button>
  <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
</form>

</div>

<script>
 
  const slider = document.getElementById('slider');
  const slides = document.querySelectorAll('.slide');
  let current = 0;

  function updateSlider() {
    slides.forEach((slide, i) => {
      slide.classList.remove('active');
      if (i === current) {
        slide.classList.add('active');
      }
    });
    const slideWidth = slides[0].offsetWidth + 20;
    const centerOffset = (slider.offsetWidth / 2) - (slideWidth / 2);
    const translateX = -(current * slideWidth - centerOffset);
    slider.style.transform = `translateX(${translateX}px)`;
  }

  function moveSlide(dir) {
    current += dir;
    if (current < 0) current = slides.length - 1;
    if (current >= slides.length) current = 0;
    updateSlider();
  }

  window.onload = () => {
    updateSlider();
  };

  setInterval(() => moveSlide(1), 4000);

  // PHP se login status aayega
  const isLoggedIn = <?= json_encode($isLoggedIn) ?>;

  function orderFood() {
    if (!isLoggedIn) {
      alert("Please login to order.");
      window.location.href = "login.tml.php"; // ✅ file ka naam sahi karein
      return;
    }
    const slide = slides[current];
    const food = slide.querySelector('h3').textContent;
    const price = slide.querySelector('p').textContent.replace('Rs.', '').trim();

    document.getElementById('foodName').textContent = "Dish: " + food;
    document.getElementById('foodPrice').textContent = "Price: Rs. " + price;
    document.getElementById('formItems').value = food;
    document.getElementById('formPrice').value = price;

    document.getElementById('modalOverlay').style.display = "block";
    document.getElementById('orderModal').style.display = "block";
  }

  function closeModal() {
    document.getElementById('modalOverlay').style.display = "none";
    document.getElementById('orderModal').style.display = "none";
    document.getElementById('paymentMethod').value = "";
    document.getElementById('easypaisaQR').style.display = "none";

    // ✅ optional: hidden fields clear kar do
    document.getElementById('formItems').value = "";
    document.getElementById('formPrice').value = "";
  }

  document.getElementById('paymentMethod').addEventListener('change', function() {
    if (this.value === "easypaisa_qr") {
      document.getElementById('easypaisaQR').style.display = "block";
    } else {
      document.getElementById('easypaisaQR').style.display = "none";
    }
  });
</script>

</body>
</html>
