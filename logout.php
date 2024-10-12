<?php
session_start(); //unset all session variables
session_destroy(); //Destroy the session
$url = 'login.php';
header('Location: ' . $url); 

?>