let ratings = [];

function submitReview() {
    let rating = document.querySelector('input[name="rating"]:checked');
    let reviewText = document.getElementById("reviewText").value;
    let reviewsDiv = document.getElementById("reviews");
    
    if (!rating || reviewText.trim() === "") {
        alert("Please provide a rating and a review.");
        return;
    }
    
    let review = document.createElement("div");
    review.classList.add("review");
review.innerHTML = `<strong>${rating.value} ★</strong><p>${reviewText}</p>`;
    
    reviewsDiv.prepend(review);
    ratings.push(parseInt(rating.value));
<h2 id="averageRating">Average Rating: ☆☆☆☆☆ (0.0)</h2>

    
    document.getElementById("reviewText").value = "";
    rating.checked = false;
}

function updateAverageRating() {
    if (ratings.length === 0) return;
    let sum = ratings.reduce((a, b) => a + b, 0);
    let avg = (sum / ratings.length).toFixed(1);
    
    let stars = "★".repeat(Math.round(avg)) + "☆".repeat(5 - Math.round(avg));
document.getElementById("averageRating").innerHTML = `Average Rating: ${stars} (${avg})`;
}