<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
        <a href="index.php" class="text-xl font-bold mb-2 md:mb-0">Lost and Found</a>
        <div class="space-x-2 md:space-x-4 flex flex-wrap justify-center">
            <a href="index.php" class="hover:underline">Home</a>
            <a href="items_list.php" class="hover:underline">Browse Items</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="report_lost_item.php" class="hover:underline">Report Lost</a>
                <a href="report_found_item.php" class="hover:underline">Report Found</a>
                <a href="dashboard.php" class="hover:underline">My Dashboard</a>
                <span class="px-2">|</span>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="hover:underline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="hover:underline">Login</a>
                <a href="register.php" class="hover:underline">Register</a>
            <?php endif; ?>
            
            <a href="contact.php" class="hover:underline">Contact</a>
        </div>
    </div>
</nav>