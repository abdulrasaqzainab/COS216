<?php
session_start(); // Start session to access session variables

// Check if user is logged in
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Website</title>
    <link rel="stylesheet" href="css/mycss.css">
  
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="index.php">Your Logo</a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php
                // If user is logged in, display their name and logout button
                if(isset($username)) {
                    echo '<li>Welcome, ' . $username . '!</li>';
                    echo '<li><a href="logout.php">Logout</a></li>';
                } else { // If user is not logged in, display login and register links
                    echo '<li><a href="login.php">Login</a></li>';
                    echo '<li><a href="register.php">Register</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>
