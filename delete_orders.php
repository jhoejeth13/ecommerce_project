<?php
include("connection/connect.php"); //connection to db
error_reporting(0);
session_start();
include 'session_timeout.php';


// sending query
mysqli_query($db,"DELETE FROM users_orders WHERE o_id = '".$_GET['order_del']."'"); 
header("location:your_orders.php"); 

?>
