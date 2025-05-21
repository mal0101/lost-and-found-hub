<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If ROOT_PATH is not defined (direct access to this file)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
    require_once ROOT_PATH . '/includes/helpers/functions.php';
}

// Check for flash messages
$flash = get_flash_message();
if ($flash) {
    if ($flash['type'] === 'success') {
        $success_message = $flash['message'];
    } else {
        $error_message = $flash['message'];
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
    <link href="<?php echo url('assets/css/output.css'); ?>" rel="stylesheet">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🔍</text></svg>">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <?php require_once ROOT_PATH . '/includes/templates/navbar.php'; ?>
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