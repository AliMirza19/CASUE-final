<?php
// Logout functionality
session_start();

// Session variables ko clear karo
session_unset();

// Session ko destroy karo
session_destroy();

// Login page par redirect karo with success message
session_start();
$_SESSION['success'] = "You have been successfully logged out!";
header("Location: ../index.php");
exit();
?>
