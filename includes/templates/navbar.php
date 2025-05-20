<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Helper function if not using the functions.php file
if (!function_exists('url')) {
    function url($path) {
        return '/' . ltrim($path, '/');
    }
}

// Helper function for active navigation links
function isActive($path) {
    $current_page = $_SERVER['REQUEST_URI'];
    if (strpos($current_page, $path) !== false) {
        return 'text-white bg-blue-700 px-3 py-1 rounded';
    }
    return 'hover:underline';
}
?>

<nav class="bg-blue-600 text-white p-4 shadow-md">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
        <!-- Logo/Brand -->
        <a href="<?php echo url('index.php'); ?>" class="text-xl font-bold mb-2 md:mb-0 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Lost and Found
        </a>
        
        <!-- Navigation Links -->
        <div class="space-x-1 md:space-x-4 flex flex-wrap justify-center">
            <a href="<?php echo url('index.php'); ?>" class="<?php echo isActive('index.php'); ?>">Home</a>
            <a href="<?php echo url('pages/items/item_list.php'); ?>" class="<?php echo isActive('item_list'); ?>">Browse Items</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Logged in user navigation -->
                <a href="<?php echo url('pages/items/report_lost_item.php'); ?>" class="<?php echo isActive('report_lost_item'); ?>">
                    Report Lost
                </a>
                <a href="<?php echo url('pages/items/report_found_item.php'); ?>" class="<?php echo isActive('report_found_item'); ?>">
                    Report Found
                </a>
                <a href="<?php echo url('pages/user/dashboard.php'); ?>" class="<?php echo isActive('dashboard'); ?>">
                    My Dashboard
                </a>
                <span class="hidden md:inline px-2">|</span>
                <span class="text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="<?php echo url('pages/auth/logout.php'); ?>" class="hover:underline text-yellow-200">
                    Logout
                </a>
            <?php else: ?>
                <!-- Guest navigation -->
                <a href="<?php echo url('pages/auth/login.php'); ?>" class="<?php echo isActive('login'); ?>">Login</a>
                <a href="<?php echo url('pages/auth/register.php'); ?>" class="<?php echo isActive('register'); ?>">Register</a>
            <?php endif; ?>
            
            <a href="<?php echo url('contact.php'); ?>" class="<?php echo isActive('contact'); ?>">Contact</a>
        </div>
    </div>
</nav>