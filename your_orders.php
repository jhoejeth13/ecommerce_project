<?php
// Include necessary files and functions
include("connection/connect.php"); // Include database connection
include 'functions.php'; // Include custom functions
include 'session_timeout.php'; // Include session timeout handling

// Enforce HTTPS
redirectTohttps(); // Redirect to HTTPS if not already using HTTPS

// Start secure session
session_start([
    'cookie_secure' => true,   // Ensures cookies are only sent over HTTPS
    'cookie_httponly' => true, // Prevents cookies from being accessed via JavaScript
    'use_strict_mode' => true  // Adds entropy to session IDs
]);

// Redirect to login if user is not logged in
if(empty($_SESSION['user_id'])) {
    header('location:login.php');
    exit(); // Stop further execution if not logged in
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
    <title>My Orders</title>
    <!-- Bootstrap CSS and other stylesheets -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/animsition.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style type="text/css">
        /* Your custom CSS styles */
    </style>
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

    </style>
<body>
    <!-- Header section -->
    <header id="header" class="header-scroll top-header headrom">
        <nav class="navbar navbar-dark">
            <div class="container">
                <!-- Navbar toggle button for mobile -->
                <button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mainNavbarCollapse">&#9776;</button>
                <span class="logo-text">FOOD RESTO</span>
                <span class="tagline">Your Cravings, We Deliver</span>
                <!-- Navbar links -->
                <div class="collapse navbar-toggleable-md  float-lg-right" id="mainNavbarCollapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item"> <a class="nav-link active" href="index.php">Home <span class="sr-only">(current)</span></a> </li>
                        <li class="nav-item"> <a class="nav-link active" href="restaurants.php">Restaurants</a> </li>
                        <?php
                        // Conditional navbar links based on user session
                        if(empty($_SESSION["user_id"])) {
                            echo '<li class="nav-item"><a href="login.php" class="nav-link active">Login</a></li>
                                  <li class="nav-item"><a href="registration.php" class="nav-link active">Register</a></li>';
                        } else {
                            echo '<li class="nav-item"><a href="cart.php" class="nav-link active">My Cart</a></li>';
                            echo '<li class="nav-item"><a href="your_orders.php" class="nav-link active">My Orders</a></li>';
                            echo '<li class="nav-item"><a href="logout.php" class="nav-link active">Logout</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page content -->
    <div class="page-wrapper">
        <!-- Hero section -->
        <div class="inner-page-hero bg-image" data-image-src="images/img/pimg.jpg">
            <div class="container"></div>
        </div>
        
        <!-- Main section -->
        <section class="restaurants-page">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="bg-gray">
                            <div class="row">
                                <!-- Table for displaying orders -->
                                <table class="table table-bordered table-hover">
                                    <thead style="background: #404040; color:white;">
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                            <th>Message</th> <!-- New column for Remarks -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    // Fetch user's orders securely
                                    $query_res = mysqli_query($db, "SELECT * FROM users_orders WHERE u_id='{$_SESSION['user_id']}' ORDER BY date DESC");
                                    $prev_date = null; // Initialize variable to track previous date/time

                                    if(mysqli_num_rows($query_res) > 0) {
                                        while($row = mysqli_fetch_array($query_res)) {
                                            $current_date = $row['date']; // Get current order's date/time

                                            // Display date/time only in the first row of each group
                                            if ($current_date !== $prev_date) {
                                                echo '<tr>';
                                                echo '<td data-column="Date" rowspan="1">' . $current_date . '</td>'; // Display date/time in the first row
                                            } else {
                                                echo '<tr>';
                                                echo '<td data-column="Date" rowspan="1"></td>'; // For subsequent rows in the same group, leave this cell empty
                                            }

                                            // Display other columns for each order securely
                                            echo '<td data-column="Item">' . htmlspecialchars($row['title']) . '</td>';
                                            echo '<td data-column="Quantity">' . htmlspecialchars($row['quantity']) . '</td>';
                                            echo '<td data-column="price">₱' . htmlspecialchars($row['price']) . '</td>';
                                            echo '<td data-column="status">';
                                            
                                            // Example status buttons based on your previous code
                                            $status = $row['status'];
                                            if(empty($status) || $status == "NULL") {
                                                echo '<button type="button" class="btn btn-info"><span class="fa fa-bars"  aria-hidden="true"></span> Dispatch</button>';
                                            } elseif($status == "in process") {
                                                echo '<button type="button" class="btn btn-warning"><span class="fa fa-cog fa-spin"  aria-hidden="true"></span> On The Way!</button>';
                                            } elseif($status == "closed") {
                                                echo '<button type="button" class="btn btn-success"><span  class="fa fa-check-circle" aria-hidden="true"></span> Delivered</button>';
                                            } elseif($status == "rejected") {
                                                echo '<button type="button" class="btn btn-danger"><i class="fa fa-close"></i> Cancelled</button>';
                                            }
                                            
                                            echo '</td>';
                                            
                                            // Hide action button if status is "closed" (Delivered)
                                            if ($status != "closed") {
                                                echo '<td data-column="Action"><a href="delete_orders.php?order_del=' . htmlspecialchars($row['o_id']) . '" onclick="return confirm(\'Are you sure you want to cancel your order?\');" class="btn btn-danger btn-flat btn-addon btn-xs m-b-10"><i class="fa fa- cancel-o" style="font-size:16px"></i> Cancel</a></td>';
                                            } else {
                                                echo '<td data-column="Action">-</td>'; // Placeholder or empty cell for delivered orders
                                            }

                                            // Display Remarks column securely
                                            echo '<td data-column="Remarks">';
                                            echo htmlspecialchars($row['remark']); // Assuming 'remarks' is a field in your database for each order
                                            echo '</td>';

                                            echo '</tr>';

                                            $prev_date = $current_date; // Update previous date/time for the next iteration
                                        }
                                    } else {
                                        echo '<tr><td colspan="7"><center>You have No orders Placed yet.</center></td></tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer section -->
        <footer class="footer">
            <div class="container">
                <div class="row bottom-footer">
                    <div class="container">
                        <div class="row">
                            <!-- Payment options -->
                            <div class="col-xs-12 col-sm-3 payment-options color-gray">
                                <h5>Payment Options</h5>
                                <ul>
                                    <li><a href="#"><img src="images/paypal.png" alt="Paypal"></a></li>
                                    <li><a href="#"><img src="images/gcash (2).png" alt="Gcash"></a></li>
                                    <li><a href="#"><img src="images/mastercard.png" alt="Mastercard"></a></li>
                                    <li><a href="#"><img src="images/maestro.png" alt="Maestro"></a></li>
                                    <li><a href="#"><img src="images/stripe.png" alt="Stripe"></a></li>
                                    <li><a href="#"><img src="images/bitcoin.png" alt="Bitcoin"></a></li>
                                </ul>
                            </div>
                            <!-- Address and contact information -->
                            <div class="col-xs-12 col-sm-4 address color-gray">
                                <h5>Address</h5>
                                <p>CSU - Cabadbaran Campus</p>
                                <h5>Phone: 09123456789</h5>
                            </div>
                            <!-- Additional information -->
                            <div class="col-xs-12 col-sm-5 additional-info color-gray">
                                <h5>Addition informations</h5>
                                <p>Join thousands of other restaurants who benefit from having partnered with us.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

    </div>

    <!-- JavaScript libraries -->
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
