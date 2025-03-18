<?php
session_start(); 
include("header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listings</title>
     <script src="js/listings.js" defer></script>
</head>
<body>
    <div class="banner1">
        <img src="img/green_t.png" width="200" height="150" alt="company logo">
        <h1>McAlister's Listing</h1>
    </div>

   

    <main>

    <section id="filter-section">
        <h2>Filters</h2>
        <label for="minPrice">Min Price:</label>
        <input type="number" id="minPrice">
        <label for="maxPrice">Max Price:</label>
        <input type="number" id="maxPrice">
        <label for="bedrooms">Bedrooms:</label>
        <select id="bedrooms">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <button onclick="savePreferences()">Save Preferences</button>
    </section>

    <!-- Listings section -->
    <section id="listings-section">
        <h2>Listings</h2>
        <div id="listings-container">
            <!-- Listings will be loaded here dynamically -->
        </div>
    </section>

    <script>
        // Function to save user preferences
        function savePreferences() {
            var preferences = {
                minPrice: document.getElementById('minPrice').value,
                maxPrice: document.getElementById('maxPrice').value,
                bedrooms: document.getElementById('bedrooms').value
            };
            // Store preferences in localStorage
            localStorage.setItem('userPreferences', JSON.stringify(preferences));
            // Apply filters immediately (optional)
            applyFilters(preferences);
        }

        // Function to retrieve saved preferences and apply filters
        function applySavedPreferences() {
            var savedPreferences = localStorage.getItem('userPreferences');
            if (savedPreferences) {
                savedPreferences = JSON.parse(savedPreferences);
                applyFilters(savedPreferences);
            }
        }

        // Function to apply filters to listings
        function applyFilters(filters) {
            // Fetch listings from server based on filters
            // Display filtered listings on the page
            console.log("Applying filters:", filters);
        }

        // Call applySavedPreferences when the page loads
        window.onload = applySavedPreferences;
    </script>

   
        <section>
            <div class="container">
                <h2>Search Bar</h2>
                <input type="text" id="searchbar" placeholder="Search by location">
            </div>
            <select class="buy-rent-selector">
                <option value="buy">Buy</option>
                <option value="rent">Rent</option>
            </select>
            <label for="sort">Sort by:</label>
            <select id="sort">
                <option value="title">Title</option>
                <option value="price">Price</option>
            </select>
        </section>

        <div id="loading_screen">
            <img src="img/loading_gif.gif" alt="Loading">
            <h1>Content is Loading</h1>
        </div>

        <div id="listings-container" class="container">
            <!-- Listings will be loaded here dynamically -->
        </div>
    </main>

    

    <?php
include("footer.php");
?>
</body>
</html>

