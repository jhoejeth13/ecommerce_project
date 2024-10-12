<?php
include("connection/connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username'])) {
        $username = mysqli_real_escape_string($db, $_POST['username']);
        $check_username = mysqli_query($db, "SELECT username FROM users WHERE username = '$username'");
        if (mysqli_num_rows($check_username) > 0) {
            echo "unavailable";
        } else {
            echo "available";
        }
    }

    if (isset($_POST['email'])) {
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $check_email = mysqli_query($db, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            echo "unavailable";
        } else {
            echo "available";
        }
    }
}
?>
