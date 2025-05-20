<?php
// Define root path
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Include helper functions
require_once ROOT_PATH . '/includes/helpers/functions.php';

// Start session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Set flash message for the next page
session_start();
set_flash_message('success', 'You have been logged out successfully');

// Redirect to login page
redirect('index.php');
?>