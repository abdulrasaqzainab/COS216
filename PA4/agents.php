<?php
session_start(); 
include("header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agents</title>
    <script src="js/agents.js" defer></script>
</head>
<body>
    
    <div class="banner1">
        <img src="img/green_t.png" width="200" height="150" alt="Company Logo">
        <h1>McAlister's Listing</h1>
    </div>

    

    <main>
        <div id="loading_screen">
            <img src="img/loading_gif.gif" alt="Loading">
            <h1>Content is Loading</h1>
        </div>
        <div id="agents-container">
           
        </div>
    </main>

    <?php
include("footer.php");
?>
</body>
</html>

