<?php
// Define root path constant
define('ROOT_PATH', __DIR__);

// Set page title
$page_title = "Home";

// Include essential files
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/includes/helpers/functions.php';
require_once ROOT_PATH . '/includes/templates/header.php';

// Main content
require_once ROOT_PATH . '/pages/items/item_list.php';

// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>