<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// If ROOT_PATH is not defined (direct access to this file)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
    require_once ROOT_PATH . '/includes/helpers/functions.php';
}

// Helper function for active navigation links
function isActive($path)
{
    $current_page = $_SERVER['REQUEST_URI'];
    if (strpos($current_page, $path) !== false) {
        return ' border border-white p-2 rounded';
    }
    return 'hover:underline';
}
?>
<nav class="bg-blue-600 text-white py-4 px-4 sm:px-6 shadow-lg sticky top-0 z-50 transition-all duration-300">
    <div class="container mx-auto">
        <div class="flex justify-between items-center">
            <!-- Logo/Brand -->
            <a href="<?php echo url('index.php'); ?>"
                class="text-lg font-bold flex items-center hover:text-yellow-200 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Lost&Found
            </a>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-white focus:outline-none">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex md:items-center md:space-x-4">

                <a href="<?php echo url('pages/items/item_list.php'); ?>"
                    class="p-2 rounded-md font-medium transition-all duration-200 hover:bg-blue-700 hover:text-yellow-200 <?php echo isActive('item_list'); ?>">
                    Browse Items
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- <a href="<?php echo url('pages/items/report_lost_item.php'); ?>"
                        class="p-2 rounded-md font-medium transition-all duration-200 hover:bg-blue-700 hover:text-yellow-200 <?php echo isActive('report_lost_item'); ?>">
                        Report Lost
                    </a> -->
                    <!-- <a href="<?php echo url('pages/items/report_found_item.php'); ?>"
                        class="p-2 rounded-md font-medium transition-all duration-200 hover:bg-blue-700 hover:text-yellow-200 <?php echo isActive('report_found_item'); ?>">
                        Report Found
                    </a> -->
                    <a href="<?php echo url('pages/items/report_item.php'); ?>"
                        class="p-2 rounded-md font-medium transition-all duration-200 hover:bg-blue-700 hover:text-yellow-200 <?php echo isActive('report_lost_item'); ?>">
                        Report Item
                    </a>
                    <a href="<?php echo url('pages/user/dashboard.php'); ?>"
                        class="p-2 rounded-md font-medium transition-all duration-200 hover:bg-blue-700 hover:text-yellow-200 <?php echo isActive('dashboard'); ?>">
                        My Dashboard
                    </a>

                    <div class="w-px h-6 bg-blue-400 mx-2"></div>

                    <a href="<?php echo url('pages/auth/logout.php'); ?>"
                        class="p-2 rounded-md font-medium bg-blue-700 text-yellow-200 hover:bg-blue-800 transition-all duration-200">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo url('pages/auth/login.php'); ?>"
                        class="p-2 rounded-md font-medium transition-all duration-200 hover:bg-blue-700 hover:text-yellow-200 <?php echo isActive('login'); ?>">
                        Login
                    </a>
                    <a href="<?php echo url('pages/auth/register.php'); ?>"
                        class="p-2 rounded-md font-medium transition-all duration-200 hover:bg-blue-700 hover:text-yellow-200 <?php echo isActive('register'); ?>">
                        Register
                    </a>
                <?php endif; ?>

                <a href="<?php echo url('contact.php'); ?>"
                    class="p-2 rounded-md font-medium transition-all duration-200 hover:bg-blue-700 hover:text-yellow-200 <?php echo isActive('contact'); ?>">
                    Contact
                </a>

                <?php if (isset($_SESSION['username'])): ?>
                    <div class="flex items-center px-3 py-2 ml-2 bg-blue-700 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-200 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium text-gray-100"><?php echo h($_SESSION['username']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile Navigation (Hidden by default) -->
        <div id="mobile-menu" class="hidden md:hidden mt-4 pb-3 border-t border-blue-500">
            <div class="flex flex-col space-y-2 mt-3">
                <a href="<?php echo url('pages/items/item_list.php'); ?>"
                    class="px-4 py-3 rounded-md font-medium block hover:bg-blue-700 <?php echo isActive('item_list'); ?>">
                    Browse Items
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo url('pages/items/report_item.php'); ?>"
                        class="px-4 py-3 rounded-md font-medium block hover:bg-blue-700 <?php echo isActive('report_lost_item'); ?>">
                        Report Item
                    </a>
                    <a href="<?php echo url('pages/user/dashboard.php'); ?>"
                        class="px-4 py-3 rounded-md font-medium block hover:bg-blue-700 <?php echo isActive('dashboard'); ?>">
                        My Dashboard
                    </a>

                    <div class="my-2 border-t border-blue-500"></div>

                    <a href="<?php echo url('pages/auth/logout.php'); ?>"
                        class="px-4 py-3 rounded-md font-medium block bg-blue-700 text-yellow-200 hover:bg-blue-800">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo url('pages/auth/login.php'); ?>"
                        class="px-4 py-3 rounded-md font-medium block hover:bg-blue-700 <?php echo isActive('login'); ?>">
                        Login
                    </a>
                    <a href="<?php echo url('pages/auth/register.php'); ?>"
                        class="px-4 py-3 rounded-md font-medium block hover:bg-blue-700 <?php echo isActive('register'); ?>">
                        Register
                    </a>
                <?php endif; ?>

                <a href="<?php echo url('contact.php'); ?>"
                    class="px-4 py-3 rounded-md font-medium block hover:bg-blue-700 <?php echo isActive('contact'); ?>">
                    Contact
                </a>

                <?php if (isset($_SESSION['username'])): ?>
                    <div class="flex items-center px-4 py-3 mt-2 bg-blue-700 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-200 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="font-medium text-gray-100"><?php echo h($_SESSION['username']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>