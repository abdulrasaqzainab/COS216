<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', '');
define('DB_PASSWORD', '');
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


