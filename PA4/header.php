<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>McAlister's Listing</title>
    <!-- Link to default stylesheet -->
    <link id="themeStylesheet" rel="stylesheet" href="css/mycss.css">

    <script>
        
        function savePreferences(preferences) {
            var apiKey = localStorage.getItem('apikey'); 
            var data = {
                type: 'save',
                apikey: apiKey,
                preferences: preferences
            };

            // Send AJAX request to save preferences
            fetch('https://wheatley.cs.up.ac.za/u22566202/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                // Handle success or error response
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        var preferences = {
                minPrice: minPrice,
                maxPrice: maxPrice,
                bedrooms: bedrooms
            };
        savePreferences(filters);
    </script>
</head>
<body>
    <header>
        <nav id="navbar">
            
            <a href="index.php">Home</a>
            <a href="listings.php">Listings</a>
            <a href="agents.php">Agents</a>
            <a href="calculators.php">Calculators</a>
            
        </nav>
       
        <script>
            document.addEventListener('DOMContentLoaded', function() {
            // Create a logout button if the user is logged in
        if (isLoggedIn()) {
            createLogoutButton();
        } else {
            createLoginButton();
            createRegisterButton();
        }
    });

    function isLoggedIn() {
        return !!localStorage.getItem('apiKey');
    }


    function createLogoutButton() {
        var logoutButton = document.createElement('a');
        logoutButton.textContent = 'Logout';
        logoutButton.href = '#'; 
        logoutButton.addEventListener('click', function(event) {
            event.preventDefault();
            // Implement logout functionality here
            localStorage.removeItem('apiKey');
            localStorage.removeItem('userPreferences');
            localStorage.removeItem('theme');
            window.location.href = 'index.php'; // Redirect to logout page
        });
        document.getElementById('navbar').appendChild(logoutButton);
    }

    // Function to create a login button
    function createLoginButton() {
        var loginButton = document.createElement('a');
        loginButton.textContent = 'Login';
        loginButton.href = 'login.php'; // Add the login URL here
        document.getElementById('navbar').appendChild(loginButton);
    }

    // Function to create a register button
    function createRegisterButton() {
        var registerButton = document.createElement('a');
        registerButton.textContent = 'Register';
        registerButton.href = 'register.php'; // Add the register URL here
        document.getElementById('navbar').appendChild(registerButton);
    }

    function showWelcomeMessage() {
        var welcomeMessage = document.createElement('span');
        var userName = localStorage.getItem('userName'); 
        welcomeMessage.textContent = 'Welcome, ' + userName;
        document.getElementById('navbar').appendChild(welcomeMessage);
    }

            </script>
       
    </header>
   
  
</body>
</html>
