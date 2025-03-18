<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'u22566202');
define('DB_PASSWORD', 'IN7XW2WVN4YW4PT7ESMU5AYCEJYDNKW2');
define('DB_NAME', 'u22566202_properties'); 
//make it static--singleton function
//search with price
//trying to connect to my database
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

//checking connection
if($mysqli === false){
    die("Failed to connect" . $mysqli->connect_error);
}
?>


