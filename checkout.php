<?php
// Include necessary files and session handling
include("connection/connect.php");
include_once 'product-action.php';
include 'functions.php'; // Include file where redirectTohttps() is defined

// Redirect to HTTPS if not already on HTTPS
redirectTohttps();

error_reporting(0);
session_start();
include 'session_timeout.php';

// Function to alert and redirect after placing an order
function function_alert() {
    echo "<script>alert('Thank you. Your Order has been placed!');</script>";
    echo "<script>window.location.replace('your_orders.php');</script>";
}

// Check if user is logged in
if(empty($_SESSION["user_id"])) {
    header('location:login.php');
    exit(); // Ensure script stops execution after redirect
} else {
    // Initialize variables
    $item_total = 0;

    // Calculate total cost of items in cart
    foreach ($_SESSION["cart_item"] as $item) {
        $item_total += ($item["price"] * $item["quantity"]);
    }

    // Process order placement
    if(isset($_POST['submit'])) {
        // Insert each item from cart into users_orders table
        foreach ($_SESSION["cart_item"] as $item) {
            // Prepare SQL statement (better approach to avoid SQL injection)
            $stmt = $db->prepare("INSERT INTO users_orders(u_id, title, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isidd", $_SESSION["user_id"], $item["title"], $item["quantity"], $item["price"], $item_total);
            $stmt->execute();
        }

        // Clear the cart after successful order placement
        unset($_SESSION["cart_item"]);
        $success = "Thank you. Your order has been placed!";
        function_alert();
    }
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="#">
    <title>Checkout</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
</head>
<body>
    <div class="site-wrapper">
        <header id="header" class="header-scroll top-header headrom">
            <nav class="navbar navbar-dark">
                <div class="container">
                    <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
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
                    </style>

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
                                echo  '<li class="nav-item"><a href="cart.php" class="nav-link active">My Cart</a> </li>';
                                echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a> </li>';
                                echo '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a> </li>';
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div class="page-wrapper">
            <div class="top-links">
                <div class="container">
                    <ul class="row links">
                        <li class="col-xs-12 col-sm-4 link-item"><span>1</span><a href="restaurants.php">Choose Restaurant</a></li>
                        <li class="col-xs-12 col-sm-4 link-item"><span>2</span><a href="#">Pick Your favorite food</a></li>
                        <li class="col-xs-12 col-sm-4 link-item active"><span>3</span><a href="checkout.php">Order and Pay</a></li>
                    </ul>
                </div>
            </div>
            <div class="container">
                <span style="color:green;">
                    <?php echo $success; ?>
                </span>
            </div>
            <div class="container m-t-30">
                <form action="" method="post">
                    <div class="widget clearfix">
                        <div class="widget-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="cart-totals margin-b-20">
                                        <div class="cart-totals-title">
                                            <h4>Cart Summary</h4> 
                                        </div>
                                        <div class="cart-totals-fields">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($_SESSION["cart_item"] as $item): ?>
                                                        <tr>
                                                            <td><?php echo $item["title"]; ?></td>
                                                            <td><?php echo $item["quantity"]; ?></td>
                                                            <td><?php echo "₱".$item["price"]; ?></td>
                                                            <td><?php echo "₱".($item["price"] * $item["quantity"]); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    <tr>
                                                        <td colspan="3" class="text-right"><strong>Total</strong></td>
                                                        <td class="text-color"><strong><?php echo "₱".$item_total; ?></strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="payment-option">
                                        <ul class="list-unstyled">
                                            <h4>Mode of Payment</h4> 
                                            <li>
                                            <label class="custom-control custom-radio m-b-20">
        <input name="mod" id="radioStacked1" checked value="COD" type="radio" class="custom-control-input">
        <span class="custom-control-indicator"></span>
        <span class="custom-control-description">Cash on Delivery</span>
    </label>
</li>
<li>
    <label class="custom-control custom-radio m-b-10">
        <input name="mod" type="radio" value="paypal" disabled class="custom-control-input">
        <span class="custom-control-indicator"></span>
        <span class="custom-control-description">Paypal <img src="images/paypal.jpg" alt="" width="90"></span>
    </label>
</li>
<li>
    <label class="custom-control custom-radio m-b-5">
        <input name="mod" type="radio" value="gcash" disabled class="custom-control-input">
        <span class="custom-control-indicator"></span>
        <span class="custom-control-description"><img src="images/gcash (2).png" alt="" width="28"> Gcash</span>
    </label>
                                            </li>
                                            <!-- Remove disabled options for payment methods -->
                                        </ul>
                                        <p class="text-xs-center">
                                            <input type="submit" onclick="return confirm('Do you want to confirm the order?');" name="submit" class="btn btn-success btn-block" value="Order Now">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <footer class="footer">
                <div class="container">
                    <div class="row bottom-footer">
                        <div class="container">
                            <div class="row">
                                <div class="col-xs-12 col-sm-3 payment-options color-gray">
                                    <h5>Payment Options</h5>
                                    <ul>
                                        <!-- Adjust payment options as needed -->
                                    </ul>
                                </div>
                                <div class="col-xs-12 col-sm-4 address color-gray">
                                    <h5>Address</h5>
                                    <p>CSU - Cabadbaran Campus</p>
                                    <h5>Phone: 09123456789</h5>
                                </div>
                                <div class="col-xs-12 col-sm-5 additional-info color-gray">
                                    <h5>Additional Information</h5>
                                    <p>Join thousands of other restaurants who benefit from having partnered with us.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/tether.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/animsition.min.js"></script>
    <script src="js/bootstrap-slider.min.js"></script>
    <script src="js/jquery.isotope.min.js"></script>
    <script src="js/headroom.js"></script>
    <script src="js/foodpicky.min.js"></script>
</body>
</html>
