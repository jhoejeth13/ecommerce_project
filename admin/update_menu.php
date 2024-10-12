<!DOCTYPE html>
<html lang="en">
<?php
include("../connection/connect.php"); // Include your database connection file
// Include the file where redirectTohttps() is defined
include 'functions.php';

// Call redirectTohttps() to redirect to HTTPS if not already on HTTPS
redirectTohttps();
error_reporting(0);
session_start();

$error = '';
$success = '';

// Handle form submission
if(isset($_POST['submit'])) {
    // Validate form fields
    if(empty($_POST['d_name']) || empty($_POST['about']) || $_POST['price'] == '' || $_POST['res_name'] == '') {
        $error = '<div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>All fields must be filled!</strong>
                  </div>';
    } else {
        // Process file upload if a new file is selected
        if($_FILES['file']['name'] != '') {
            $fname = $_FILES['file']['name'];
            $temp = $_FILES['file']['tmp_name'];
            $fsize = $_FILES['file']['size'];
            $extension = pathinfo($fname, PATHINFO_EXTENSION);
            $fnew = uniqid().'.'.$extension;
            $store = "../Res_img/dishes/".basename($fnew);
            
            // Check file extension and size
            if($extension == 'jpg' || $extension == 'png' || $extension == 'gif') {
                if($fsize >= 1000000) {
                    $error = '<div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <strong>Max image size is 1024kb!</strong> Try a different image.
                              </div>';
                } else {
                    // Update database record with new image
                    move_uploaded_file($temp, $store);
                    $sql = "UPDATE dishes SET rs_id=?, title=?, slogan=?, price=?, img=? WHERE d_id=?";
                    $stmt = mysqli_prepare($db, $sql);
                    mysqli_stmt_bind_param($stmt, 'issssi', $_POST['res_name'], $_POST['d_name'], $_POST['about'], $_POST['price'], $fnew, $_GET['menu_upd']);
                    
                    if(mysqli_stmt_execute($stmt)) {
                        $success = '<div class="alert alert-success alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>Record updated successfully!</strong>
                                  </div>';
                    } else {
                        $error = '<div class="alert alert-danger alert-dismissible fade show">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>Error updating record: ' . mysqli_error($db) . '</strong>
                                  </div>';
                    }
                    
                    mysqli_stmt_close($stmt);
                }
            } else {
                $error = '<div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>Invalid file type!</strong> Only JPG, PNG, and GIF files are allowed.
                          </div>';
            }
        } else {
            // Update database record without changing the image
            $sql = "UPDATE dishes SET rs_id=?, title=?, slogan=?, price=? WHERE d_id=?";
            $stmt = mysqli_prepare($db, $sql);
            mysqli_stmt_bind_param($stmt, 'isssi', $_POST['res_name'], $_POST['d_name'], $_POST['about'], $_POST['price'], $_GET['menu_upd']);
            
            if(mysqli_stmt_execute($stmt)) {
                $success = '<div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <strong>Record updated successfully!</strong>
                            </div>';
            } else {
                $error = '<div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <strong>Error updating record: ' . mysqli_error($db) . '</strong>
                          </div>';
            }
            
            mysqli_stmt_close($stmt);
        }
    }
}

// Fetch existing menu item details if menu_upd is set
if(isset($_GET['menu_upd'])) {
    $menu_id = $_GET['menu_upd'];
    $query = "SELECT * FROM dishes WHERE d_id=?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, 'i', $menu_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $d_name = $row['title'];
        $about = $row['slogan'];
        $price = $row['price'];
        $res_id = $row['rs_id'];
        $img = $row['img']; // Correctly fetch the image path field from the database
    } else {
        echo "Menu item not found!";
        exit;
    }
    
    mysqli_stmt_close($stmt);
}
?>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Update Menu</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<style>
        .logo-text {
    font-size: 20px; /* Adjust size as needed */
    font-weight: bold;
    color: black; /* Adjust color as needed */
}

.tagline {
    font-size: 15px; /* Adjust size as needed */
    color: black; /* Adjust color as needed */
    margin-top: 5px; /* Add spacing between logo text and tagline */
    font-style: italic; /* Add italic font style */
}

    </style>

<body class="fix-header">
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>

    <div id="main-wrapper">
        <div class="header">
            <nav class="navbar top-navbar navbar-expand-md navbar-light">
                <div class="navbar-header">
                    <a class="navbar-brand" href="dashboard.php">
                    <span class="logo-text">FOOD RESTO</span>
                    <span class="tagline">Your Cravings, We Deliver</span>  
                    </a>
                </div>
                <div class="navbar-collapse">
                    <ul class="navbar-nav mr-auto mt-md-0"></ul>
                    <ul class="navbar-nav my-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="images/bookingSystem/user-icn.png" alt="user" class="profile-pic" />
                            </a>
                            <div class="dropdown-menu dropdown-menu-right animated zoomIn">
                                <ul class="dropdown-user">
                                    <li><a href="logout.php"><i class="fa fa-power-off"></i> Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="left-sidebar">
            <div class="scroll-sidebar">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-devider"></li>
                        <li class="nav-label">Home</li>
                        <li><a href="dashboard.php"><i class="fa fa-tachometer"></i><span>Dashboard</span></a></li>
                        <li class="nav-label">Log</li>
                        <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-archive f-s-20 color-warning"></i><span class="hide-menu">Restaurant</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_restaurant.php">All Restaurants</a></li>
                                <li><a href="add_category.php">Add Category</a></li>
                                <li><a href="add_restaurant.php">Add Restaurant</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="has-arrow" href="#" aria-expanded="false"><i class="fa fa-cutlery" aria-hidden="true"></i><span class="hide-menu">Menu</span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li><a href="all_menu.php">All Menus</a></li>
                                <li><a href="add_menu.php">Add Menu</a></li>
                            </ul>
                        </li>
                        <li><a href="all_orders.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i><span>Orders</span></a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="container-fluid">
                <?php echo $error; ?>
                <?php echo $success; ?>
                <div class="col-lg-12">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-body">
                            <div class="row p-t-20">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Dish Name</label>
                                        <input type="text" name="d_name" value="<?php echo isset($d_name) ? $d_name : ''; ?>" class="form-control" placeholder="Dish Name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">About</label>
                                        <input type="text" name="about" value="<?php echo isset($about) ? $about : ''; ?>" class="form-control" placeholder="About">
                                    </div>
                                </div>
                            </div>
                            <div class="row p-t-20">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Price</label>
                                        <input type="text" name="price" value="<?php echo isset($price) ? $price : ''; ?>" class="form-control" placeholder="Price">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Image</label>
                                        <input type="file" name="file" class="form-control" placeholder="Upload Image">
                                        <img src="<?php echo isset($img) ? 'Res_img/dishes/'.$img : ''; ?>" height="100">
                                        </div>
                                </div>
                            </div>
                            <div class="row p-t-20">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Select Restaurant</label>
                                        <select name="res_name" class="form-control custom-select">
                                            <option value="">-- Select Restaurant --</option>
                                            <?php
                                            $query = "SELECT * FROM restaurant";
                                            $restaurants = mysqli_query($db, $query);
                                            while($row = mysqli_fetch_assoc($restaurants)) {
                                                $selected = ($row['rs_id'] == $res_id) ? 'selected' : '';
                                                echo '<option value="'.$row['rs_id'].'" '.$selected.'>'.$row['title'].'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <input type="submit" name="submit" class="btn btn-primary" value="Save">
                            <a href="all_menu.php" class="btn btn-inverse">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script src="js/lib/jquery/jquery.min.js"></script>
        <script src="js/lib/bootstrap/js/popper.min.js"></script>
        <script src="js/lib/bootstrap/js/bootstrap.min.js"></script>
        <script src="js/jquery.slimscroll.js"></script>
        <script src="js/sidebarmenu.js"></script>
        <script src="js/lib/sticky-kit-master/dist/sticky-kit.min.js"></script>
        <script src="js/custom.min.js"></script>
    </div>
</body>
</html>
