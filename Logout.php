<?php
// Initialize the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Destroy the session
session_destroy();

// Make sure no output has been sent
if (ob_get_level()) {
    ob_end_clean();
}

// Redirect to login page using JavaScript
echo "<script>window.location.href = 'Login.php';</script>";
exit();
?> 