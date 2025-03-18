<?php
session_start(); // Start session if not already started
include("header.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration</title>
</head>
<body>
  <h2>Register</h2>
  <form method="post" action="register.php">
    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name"><br>
    <label for="surname">Surname:</label><br>
    <input type="text" id="surname" name="surname"><br>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email"><br>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password"><br><br>
    <button type="submit" name="submit">Register</button>
  </form>

  <?php
  if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $data = array(
      "type" => "Register",
      "name" => $name,
      "surname" => $surname,
      "email" => $email,
      "password" => $password
    );

    $api_url = "https://wheatley.cs.up.ac.za/u22566202/api.php";
    $options = array(
      'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-Type: application/json',
        'content' => json_encode($data)
      )
    );
    $context  = stream_context_create($options);
    $response = file_get_contents($api_url, false, $context);
    $result = json_decode($response, true);

    if ($result['status'] === 'success') {
      echo "<div>Registration successful</div>";
    } else {
      echo "<div>Error: " . $result['message'] . "</div>";
    }
  }
  ?>

<?php

include("footer.php");
?>
</body>
</html>

