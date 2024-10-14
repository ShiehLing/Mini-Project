<?php
// Start the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

function logOutUser() {
    sessionStorage.removeItem('isLoggedIn');
    window.location.reload(); // Reload the page to reflect changes
}

// Redirect to homepage
header("Location: index.php");
exit;
?>