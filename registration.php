<?php
session_start();
error_reporting(0);
include("connection/connect.php");
// Include the file where redirectTohttps() is defined
include 'functions.php';

// Call redirectTohttps() to redirect to HTTPS if not already on HTTPS
redirectTohttps();

$message = '';

if(isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $firstname = mysqli_real_escape_string($db, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($db, $_POST['lastname']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $phone = mysqli_real_escape_string($db, $_POST['phone']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $cpassword = mysqli_real_escape_string($db, $_POST['cpassword']);
    $address = mysqli_real_escape_string($db, $_POST['address']);

    // Check if all fields are filled
    if(empty($username) || empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($password) || empty($cpassword) || empty($address)) {
        $message = "All fields must be required!";
    } else {
        // Check password length and content
        if(strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
            $message = "Password must be at least 8 characters long and include a capital letter, a number, and a symbol.";
        } elseif($password !== $cpassword) {
            $message = "Passwords do not match.";
        } elseif(strlen($phone) != 11) {
            $message = "Phone number must be exactly 11 digits long.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email address. Please enter a valid email!";
        } else {
            // Check if username already exists
            $check_username = mysqli_query($db, "SELECT username FROM users WHERE username = '$username'");
            if(mysqli_num_rows($check_username) > 0) {
                $message = "Username already exists!";
            } else {
                // Check if email already exists
                $check_email = mysqli_query($db, "SELECT email FROM users WHERE email = '$email'");
                if(mysqli_num_rows($check_email) > 0) {
                    $message = "Email already exists!";
                } else {
                    // Hash password using bcrypt
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                    // Insert user data into database
                    $query = "INSERT INTO users (username, f_name, l_name, email, phone, password, address) 
                              VALUES ('$username', '$firstname', '$lastname', '$email', '$phone', '$hashed_password', '$address')";
                    if(mysqli_query($db, $query)) {
                        echo "<script>alert('Registration successful. Redirecting to login page.'); window.location='login.php';</script>";
                        exit; // Optional: Stop further execution
                    } else {
                        $message = "Registration failed. Please try again later.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Registration</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script>
    // Function to validate form fields
    function validateForm(event) {
        event.preventDefault(); // Prevent the form from submitting
        var username = document.getElementById("example-text-input").value;
        var firstname = document.getElementById("example-text-input-1").value;
        var lastname = document.getElementById("example-text-input-2").value;
        var email = document.getElementById("exampleInputEmail1").value;
        var phone = document.getElementById("example-tel-input-3").value;
        var password = document.getElementById("exampleInputPassword1").value;
        var password2 = document.getElementById("exampleInputPassword2").value;

        // Perform other validations
        if (username === '') {
            document.getElementById('usernameError').textContent = "Username is required";
            return false;
        }

        if (firstname === '' || !/^[a-zA-Z.\-]+$/.test(firstname) || /([a-zA-Z])\1{2,}/.test(firstname)) {
            document.getElementById('firstnameError').textContent = "First name must contain letters only and cannot contain repetitive letters.";
            return false;
        }

        if (lastname === '' || !/^[a-zA-Z.\-]+$/.test(lastname) || /([a-zA-Z])\1{2,}/.test(lastname)) {
            document.getElementById('lastnameError').textContent = "Last name must contain letters only and cannot contain repetitive letters.";
            return false;
        }

        if (!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email)) {
            document.getElementById('emailError').textContent = "Invalid email address. Please enter a valid email!";
            return false;
        }

        if (!/^\d{11}$/.test(phone)) {
            document.getElementById('phoneError').textContent = "Phone number must be exactly 11 digits long.";
            return false;
        }

        if (password.length < 8 || !/[A-Z]/.test(password) || !/[0-9]/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            document.getElementById('passwordError').textContent = "Password must be at least 8 characters long and include a capital letter, a number, and a symbol.";
            return false;
        }

        if (password !== password2) {
            document.getElementById('cpasswordError').textContent = "Passwords do not match.";
            return false;
        }

        // If all validations pass, submit the form
        return true;
    }
    </script>
</head>
<style>
    .logo-text {
        font-size: 20px; /* Adjust size as needed */
        font-weight: bold;
        color: white; /* Adjust color as needed */
    }

    .tagline {
        font-size: 15px; /* Adjust size as needed */
        color: white; /* Adjust color as needed */
        margin-top: 5px; /* Add spacing between logo text and tagline */
        font-style: italic; /* Add italic font style */
    }

    .error {
        color: red;
    }

</style>
<body>
<div style="background-image: url('images/img/pimg.jpg');">
    <header id="header" class="header-scroll top-header headrom">
        <nav class="navbar navbar-dark">
            <div class="container">
                <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                <span class="logo-text">FOOD RESTO</span>
                <span class="tagline">Your Cravings, We Deliver</span>
                 <div class="collapse navbar-toggleable-md float-lg-right" id="mainNavbarCollapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
                        <li class="nav-item"> <a class="nav-link active" href="restaurants.php">Restaurants <span class="sr-only"></span></a> </li>

                        <?php
                        if(empty($_SESSION["user_id"])) {
                            echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a> </li>
                                  <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a> </li>';
                        } else {
                            echo  '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
                            echo  '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
                        }
                        ?>

                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="page-wrapper">
        <section class="contact-page inner-page">
            <div class="container ">
                <div class="row ">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-body">
                                <form id="registrationForm" action="" method="post" onsubmit="return validateForm()">
                                    <div class="row">
                                        <div class="form-group col-sm-12
                                        <div class="form-group col-sm-12">
                                            <label for="exampleInputEmail1">Username</label>
                                            <input class="form-control" type="text" name="username" id="example-text-input" required>
                                            <small id="usernameError" class="error"></small>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="exampleInputEmail1">First Name</label>
                                            <input class="form-control" type="text" name="firstname" id="example-text-input-1" required>
                                            <small id="firstnameError" class="error"></small>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="exampleInputEmail1">Last Name</label>
                                            <input class="form-control" type="text" name="lastname" id="example-text-input-2" required>
                                            <small id="lastnameError" class="error"></small>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="exampleInputEmail1">Email Address</label>
                                            <input type="email" class="form-control" name="email" id="exampleInputEmail1" aria-describedby="emailHelp" required>
                                            <small id="emailError" class="error"></small>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="exampleInputEmail1">Phone number</label>
                                            <input class="form-control" type="tel" name="phone" id="example-tel-input-3" required>
                                            <small id="phoneError" class="error"></small>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="exampleInputPassword1">Password</label>
                                            <input type="password" class="form-control" name="password" id="exampleInputPassword1" required>
                                            <small id="passwordError" class="error"></small>
                                        </div>
                                        <div class="form-group col-sm-6">
                                            <label for="exampleInputPassword1">Confirm password</label>
                                            <input type="password" class="form-control" name="cpassword" id="exampleInputPassword2" required>
                                            <small id="cpasswordError" class="error"></small>
                                        </div>
                                        <div class="form-group col-sm-12">
                                            <label for="exampleTextarea">Delivery Address</label>
                                            <textarea class="form-control" id="exampleTextarea" name="address" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <p> <input type="submit" value="Register" name="submit" class="btn theme-btn"> </p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="container">
                <div class="row bottom-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 payment-options color-gray">
                                <h5>Payment Options</h5>
                                <ul>
                                    <li><a href="#"> <img src="images/paypal.png" alt="Paypal"> </a></li>
                                    <li><a href="#"> <img src="images/gcash (2).png" alt="Gcash"> </a></li>
                                    <li><a href="#"> <img src="images/mastercard.png" alt="Mastercard"> </a></li>
                                    <li><a href="#"> <img src="images/maestro.png" alt="Maestro"> </a></li>
                                    <li><a href="#"> <img src="images/stripe.png" alt="Stripe"> </a></li>
                                    <li><a href="#"> <img src="images/bitcoin.png" alt="Bitcoin"> </a></li>
                                </ul>
                            </div>
                            <div class="col-xs-12 col-sm-4 address color-gray">
                                <h5>Address</h5>
                                <p>CSU - Cabadbaran Campus</p>
                                <h5>Phone: 09123456789</h5>
                            </div>
                            <div class="col-xs-12 col-sm-5 additional-info color-gray">
                                <h5>Additional information</h5>
                                <p>Join thousands of other restaurants who benefit from having partnered with us.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>

    <script>
    // Function to validate form fields
    function validateForm(event) {
        var isValid = true;
        var username = document.getElementById("example-text-input").value;
        var firstname = document.getElementById("example-text-input-1").value;
        var lastname = document.getElementById("example-text-input-2").value;
        var email = document.getElementById("exampleInputEmail1").value;
        var phone = document.getElementById("example-tel-input-3").value;
        var password = document.getElementById("exampleInputPassword1").value;
        var password2 = document.getElementById("exampleInputPassword2").value;

        if (username === '') {
            document.getElementById('usernameError').textContent = "Username is required";
            isValid = false;
        } else {
            document.getElementById('usernameError').textContent = "";
        }

        if (firstname === '' || !/^[a-zA-Z.\-]+$/.test(firstname) || /([a-zA-Z])\1{2,}/.test(firstname)) {
            document.getElementById('firstnameError').textContent = "First name must contain letters only and cannot contain repetitive letters.";
            isValid = false;
        } else {
            document.getElementById('firstnameError').textContent = "";
        }

        if (lastname === '' || !/^[a-zA-Z.\-]+$/.test(lastname) || /([a-zA-Z])\1{2,}/.test(lastname)) {
            document.getElementById('lastnameError').textContent = "Last name must contain letters only and cannot contain repetitive letters.";
            isValid = false;
        } else {
            document.getElementById('lastnameError').textContent = "";
        }

        if (!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(email)) {
            document.getElementById('emailError').textContent = "Invalid email address. Please enter a valid email!";
            isValid = false;
        } else {
            document.getElementById('emailError').textContent = "";
        }

        if (!/^\d{11}$/.test(phone)) {
            document.getElementById('phoneError').textContent = "Phone number must be exactly 11 digits long.";
            isValid = false;
        } else {
            document.getElementById('phoneError').textContent = "";
        }

        if (password.length < 8 || !/[A-Z]/.test(password) || !/[0-9]/.test(password) || !/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            document.getElementById('passwordError').textContent = "Password must be at least 8 characters long and include a capital letter, a number, and a symbol.";
            isValid = false;
        } else {
            document.getElementById('passwordError').textContent = "";
        }

        if (password !== password2) {
            document.getElementById('cpasswordError').textContent = "Passwords do not match.";
            isValid = false;
        } else {
            document.getElementById('cpasswordError').textContent = "";
        }

        return isValid;
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("registrationForm").addEventListener("submit", function(event) {
            if (!validateForm(event)) {
                event.preventDefault();
            }
        });

        document.getElementById("example-text-input").addEventListener("blur", function() {
            checkAvailability("username", this.value, function(available) {
                if (!available) {
                    document.getElementById('usernameError').textContent = "Username already exists!";
                } else {
                    document.getElementById('usernameError').textContent = "";
                }
            });
        });

        document.getElementById("exampleInputEmail1").addEventListener("blur", function() {
            checkAvailability("email", this.value, function(available) {
                if (!available) {
                    document.getElementById('emailError').textContent = "Email already exists!";
                } else {
                    document.getElementById('emailError').textContent = "";
                }
            });
        });
    });

    // Function to check if username or email already exists using AJAX
    function checkAvailability(field, value, callback) {
        if (value.length === 0) {
            callback(true); // If no value is provided, assume it's available
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "check_availability.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = xhr.responseText;
                callback(response === "available");
            }
        };
        xhr.send(field + "=" + value);
    }
    </script>
</body>
</html>
