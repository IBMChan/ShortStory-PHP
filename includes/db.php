<?php

define('DB_SERVER', 'localhost:3306');
define('DB_USERNAME', 'root'); 
define('DB_PASSWORD', '12345678');     
define('DB_NAME', 'shortstory'); 


$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Successfull";



?>