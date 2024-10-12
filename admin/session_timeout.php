<?php


// Set session timeout duration to 1 minute (60 seconds)
$timeout_duration = 60;// Changed to 60 seconds for 1 minute

// Check if the last activity timestamp is set in the session
if (isset($_SESSION['LAST_ACTIVITY'])) {
    // Calculate the session's lifetime
    $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];
    
    // If the elapsed time exceeds the timeout duration, destroy the session
    if ($elapsed_time >= $timeout_duration) {
        echo "<script>
                alert('Your session has timed out. You will be logged out.');
                window.location.href = 'logout.php';
              </script>";
        session_unset();     // Unset $_SESSION variable for the runtime
        session_destroy();   // Destroy session data in storage
        exit();
    }
}

// Update the last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time();
?>

