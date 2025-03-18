<?php
session_start(); 
include("header.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
</head>
<body>
  <h2>Login</h2>
  <form id="loginUser">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br><br>
    <button type="submit">Login</button>
  </form>

  <div id="message"></div>

  <script>
    

const apiUrl = "https://wheatley.cs.up.ac.za/u22566202/api.php";

document.addEventListener("DOMContentLoaded", function() {
    const loginUser = document.getElementById("loginUser");

    loginUser.addEventListener("submit", function(event) {
        event.preventDefault();

        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;

        let requestData = {
            "type":"Login",
            "email":email,
            "password": password
        }

        const body = JSON.stringify(requestData);

        const xhr = new XMLHttpRequest();

        xhr.open("POST", apiUrl, true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 )
            if(xhr.status === 200)
            { {
                
                const data = JSON.parse(xhr.responseText);
    
                if (data.status === "success") {
                  
                    localStorage.setItem("apiKey", data.data.apikey);
                    localStorage.setItem("userName", data.data.userName);
                     
                    window.location.href = "listings.php"; 

                } else {
                    console.log("ERROR:Login failed");
                    alert("Incorrect information given.Please check your email and password.");
                }
            }}
        };
    
        console.log(JSON.stringify(requestData));
        xhr.send(body);
    });
});
  </script>

  

  <?php include("footer.php"); ?>
</body>
</html>
