<?php
include("../connection/connect.php");
error_reporting(0);
session_start();
include 'session_timeout.php';

mysqli_query($db,"DELETE FROM users WHERE u_id = '".$_GET['user_del']."'");
header("location:all_users.php");  

?>