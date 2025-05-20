</div><!-- End of main content container -->
    
    <footer class="bg-gray-800 text-white p-4 mt-8">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p>&copy; <?php echo date('Y'); ?> Lost and Found System</p>
                    <p class="text-sm text-gray-400">Helping reunite people with their belongings</p>
                </div>
                
                <div class="flex space-x-4">
                    <a href="<?php echo url('contact.php'); ?>" class="hover:underline text-sm">Contact Us</a>
                    <a href="#" class="hover:underline text-sm">Privacy Policy</a>
                    <a href="#" class="hover:underline text-sm">Terms of Service</a>
                </div>
            </div>
            
            <div class="mt-4 text-center text-xs text-gray-500">
                <p>Built with PHP, MySQL, and Tailwind CSS</p>
            </div>
        </div>
    </footer>
    
    <!-- Custom JavaScript -->
    <script src="<?php echo url('assets/js/scripts.js'); ?>"></script>
    
    <!-- Optional: Add conditional scripts based on page needs -->
    <?php if (isset($page_scripts) && is_array($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo url($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>