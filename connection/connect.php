<?php

$servername = "localhost";
$username = "root";
$password = ""; // consider using environment variables or a secure configuration file
$dbname = "onlinefoodphp";

// Create connection
$db = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
