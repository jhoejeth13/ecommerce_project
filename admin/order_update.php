<?php
include("../connection/connect.php"); // Adjust the path as per your project structure
error_reporting(E_ALL); // Enable error reporting to catch potential issues
// Include the file where redirectTohttps() is defined
include 'functions.php';

// Call redirectTohttps() to redirect to HTTPS if not already on HTTPS
redirectTohttps();
session_start();
include 'session_timeout.php';

// Redirect to login page if user is not logged in
if(empty($_SESSION['user_id'])) { 
    header('location: ../login.php');
    exit(); // Ensure script stops executing after redirection
}

// Process form submission
if(isset($_POST['update'])) {
    // Retrieve form data
    $form_id = $_GET['form_id'];
    $status = $_POST['status'];
    $remark = $_POST['remark'];
    
    // Validate form inputs
    if(empty($status) || empty($remark)) {
        echo "<script>alert('Please fill in all fields');</script>";
    } else {
        // Sanitize inputs to prevent SQL injection
        $status = mysqli_real_escape_string($db, $status);
        $remark = mysqli_real_escape_string($db, $remark);
        
        // Update status and remark in users_orders table
        $update_query = "UPDATE users_orders SET status='$status', remark='$remark' WHERE o_id='$form_id'";
        $update_status = mysqli_query($db, $update_query);
        
        if($update_status) {
            echo "<script>alert('Form Details Updated Successfully');</script>";
        } else {
            echo "<script>alert('Failed to update form details');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Update</title>
    <link href="css/lib/bootstrap/bootstrap.min.css" rel="stylesheet"> <!-- Adjust paths as per your project -->
    <link href="css/helper.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style type="text/css">
        /* Your custom styles here */
    </style>
</head>
<body>
    <div style="margin-left:50px;">
        <form name="updateticket" id="updatecomplaint" method="post" onsubmit="return validateForm()">
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td><b>Form Number</b></td>
                    <td><?php echo htmlentities($_GET['form_id']); ?></td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td><b>Status</b></td>
                    <td>
                        <select name="status" required>
                            <option value="">Select Status</option>
                            <option value="in process">On the way</option>
                            <option value="closed">Delivered</option>
                            <option value="rejected">Cancelled</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><b>Message</b></td>
                    <td><textarea name="remark" cols="50" rows="10" required></textarea></td>
                </tr>
                <tr>
                    <td><b>Action</b></td>
                    <td>
                        <input type="submit" name="update" class="btn btn-primary" value="Submit">
                        <input type="button" class="btn btn-danger" value="Close this window" onclick="closeWindow()">
                    </td>
                </tr>
            </table>
        </form>
    </div>
    
    <script>
        function validateForm() {
            var status = document.forms["updateticket"]["status"].value;
            var remark = document.forms["updateticket"]["remark"].value;
            if (status === "" || remark === "") {
                alert("Please fill in all fields");
                return false;
            }
            return true;
        }
        
        function closeWindow() {
            window.close();
        }
    </script>
</body>
</html>
