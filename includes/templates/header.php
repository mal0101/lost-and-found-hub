<?php 
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include helper functions if they exist
if (file_exists(__DIR__ . '/../helpers/functions.php')) {
    require_once __DIR__ . '/../helpers/functions.php';
}

// Helper function if not using the functions.php file
if (!function_exists('url')) {
    function url($path) {
        return '/' . ltrim($path, '/');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lost and Found System - Find your lost items or report found ones">
    <title><?php echo isset($page_title) ? $page_title . ' - Lost and Found' : 'Lost and Found'; ?></title>
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo url('assets/css/style.css'); ?>" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo url('assets/favicon.ico'); ?>" type="image/x-icon">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Include the navigation bar -->
    <?php require_once __DIR__ . '/navbar.php'; ?>
    
    <!-- Main content container -->
    <div class="container mx-auto p-4 flex-grow">
        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 alert-message" role="alert">
                <p><?php echo $success_message; ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 alert-message" role="alert">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>