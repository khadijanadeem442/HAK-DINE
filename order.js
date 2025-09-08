// Load cart from localStorage or initialize as an empty array
let cart = JSON.parse(localStorage.getItem('cartItems')) || [];

// Update the cart item count on the UI
function updateCartCount() {
    const countElement = document.querySelector('.cart-count'); // Selects cart count element
    if (countElement) {
        countElement.textContent = cart.reduce((sum, item) => sum + item.qty, 0); // Updates text content with total quantity
    }
}
updateCartCount(); // Initial update of cart count

// Add event listeners to all "Add to Cart" buttons
document.querySelectorAll('.add-cart').forEach((button) => { // Iterates over all elements with class 'add-cart'
    button.addEventListener('click', () => { // Adds click listener to each button
        const foodBox = button.closest('.food-box'); // Finds the closest parent with class 'food-box'
        if (!foodBox) return alert("Food item container not found."); // Alerts if container not found

        const title = foodBox.querySelector('.food-title')?.textContent.trim(); // Gets food title
        const priceText = foodBox.querySelector('.food-price')?.textContent.trim(); // Gets food price text

        if (!title || !priceText) return alert("Item data is missing."); // Alerts if item data is missing

        const price = parseFloat(priceText.replace(/[^\d.]/g, '')); // Parses price from text
        if (isNaN(price)) return alert("Invalid price format."); // Alerts if price format is invalid

        const existingItem = cart.find(item => item.title === title); // Checks if item already exists in cart
        if (existingItem) {
            existingItem.qty += 1; // Increments quantity if item exists
        } else {
            cart.push({ title, price, qty: 1 }); // Adds new item to cart
        }

        localStorage.setItem('cartItems', JSON.stringify(cart)); // Saves updated cart to localStorage
        updateCartCount(); // Updates cart count on UI
        alert(`${title} added to cart!`); // Alerts that item has been added
    });
});

// Calculate the total price of all items in the cart (function remains, but not directly used by removed place order logic)
function calculateTotal() {
    return cart.reduce((total, item) => total + item.price * item.qty, 0); // Calculates total price
}

// REMOVED: Redundant "Place Order" button click handler from order.js
// The placeorder.js file handles this, which is loaded after order.js in Menu.php.
// const placeOrderBtn = document.getElementById('placeOrderBtn');
// if (placeOrderBtn) {
//     placeOrderBtn.addEventListener('click', () => {
//         // ... (removed logic)
//     });
// }