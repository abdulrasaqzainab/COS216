<?php
session_start(); 
include("header.php");
?>

<h1>My Favorite Listings</h1>

<div class="listings-container">
    <?php
    
    echo '<script>';
    echo 'document.addEventListener("DOMContentLoaded", function() {';
    echo 'if (!isLoggedIn()) {';
    echo 'window.location.href = "login.php";'; 
    echo '}';
    echo '});';
    echo 'function isLoggedIn() {';
    echo 'return !!localStorage.getItem("apiKey");'; 
    echo '}';
    echo '</script>';
    ?>

    <?php
   
    if (isset($_SESSION['apiKey'])) {
        
        require_once "config.php";

        
        $apiKey = $_SESSION['apiKey'];

        
        $stmt = $db->prepare("SELECT * FROM favorites WHERE apikey = ?");
        $stmt->bind_param("s", $apiKey);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="listing">';
                echo '<h2>' . $row['listing_title'] . '</h2>';
                echo '<p>Price: $' . $row['listing_price'] . '</p>';
               
                echo '</div>';
            }
        } else {
            echo '<p>No favorite listings found.</p>';
        }
    }
    ?>
</div>

<?php
include("footer.php");
?>
