<?php
// Define root path
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Set page title
$page_title = "Item Details";

// Include essential files
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/includes/helpers/functions.php';

// Start session
session_start();

// Include header
require_once ROOT_PATH . '/includes/templates/header.php';

// Check if item ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $error_message = "No item specified";
    echo '<div class="max-w-2xl mx-auto my-8 bg-white p-8 rounded-lg shadow-lg border border-red-200">';
    echo '<p class="text-red-500 font-medium text-lg">Error: ' . $error_message . '</p>';
    echo '<p class="mt-4"><a href="' . url('index.php') . '" class="text-blue-600 hover:text-blue-800 hover:underline transition duration-300">Return to homepage</a></p>';
    echo '</div>';
    require_once ROOT_PATH . '/includes/templates/footer.php';
    exit;
}

// Fetch item details
$stmt = $pdo->prepare("
    SELECT i.*, u.username, u.email 
    FROM items i 
    JOIN users u ON i.user_id = u.id 
    WHERE i.id = ?
");
$stmt->execute([$_GET['id']]);
$item = $stmt->fetch();

// If item doesn't exist, show error
if (!$item) {
    $error_message = "Item not found";
    echo '<div class="max-w-2xl mx-auto my-8 bg-white p-8 rounded-lg shadow-lg border border-red-200">';
    echo '<p class="text-red-500 font-medium text-lg">Error: ' . $error_message . '</p>';
    echo '<p class="mt-4"><a href="' . url('index.php') . '" class="text-blue-600 hover:text-blue-800 hover:underline transition duration-300">Return to homepage</a></p>';
    echo '</div>';
    require_once ROOT_PATH . '/includes/templates/footer.php';
    exit;
}

$is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $item['user_id'];
?>

<div class="max-w-6xl mx-auto my-8">
    <div class="mb-6">
        <a href="javascript:history.back()"
            class="flex items-center text-blue-600 hover:text-blue-800 transition duration-300 group">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1 group-hover:-translate-x-1 transition-transform duration-300" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-medium">Back</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 md:p-8">
            <div class="flex flex-wrap">
                <?php if (!empty($item['image_path']) && file_exists(ROOT_PATH . '/' . $item['image_path'])): ?>
                    <div class="w-full md:w-2/5 lg:w-1/3 mb-6 md:mb-0">
                        <div class="rounded-lg overflow-hidden shadow-md bg-gray-100">
                            <img src="<?php echo url($item['image_path']); ?>" alt="<?php echo h($item['title']); ?>"
                                class="w-full h-auto object-cover">
                        </div>
                    </div>
                    <div class="w-full md:w-3/5 lg:w-2/3 md:pl-8">
                    <?php else: ?>
                        <div class="w-full">
                        <?php endif; ?>

                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full <?php echo $item['status'] === 'lost' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200'; ?> text-sm font-medium">
                                <?php if ($item['status'] === 'lost'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                <?php endif; ?>
                                <?php echo ucfirst($item['status']); ?>
                            </span>

                            <span class="text-sm text-gray-500">
                                Posted on: <?php echo date('F j, Y, g:i a', strtotime($item['date_posted'])); ?>
                            </span>
                        </div>

                        <h1 class="text-3xl font-bold mb-6 text-gray-800"><?php echo h($item['title']); ?></h1>

                        <div class="space-y-8">
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Description
                                </h3>
                                <div class="bg-gray-50 p-4 rounded-lg whitespace-pre-line">
                                    <?php echo nl2br(h($item['description'])); ?>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Location
                                </h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <?php echo h($item['location']); ?>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-700 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Posted By
                                </h3>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <span class="font-medium"><?php echo h($item['username']); ?></span>
                                </div>
                            </div>

                            <?php if ($is_owner): ?>
                                <div class="border-t border-gray-200 pt-6">
                                    <h3 class="text-lg font-semibold mb-4 text-gray-700">Manage Your Item</h3>
                                    <div class="flex flex-wrap gap-3">
                                        <a href="<?php echo url('pages/items/edit_item.php?id=' . $item['id']); ?>"
                                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-300 inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Item
                                        </a>
                                        <a href="<?php echo url('pages/user/dashboard.php?delete=' . $item['id']); ?>"
                                            onclick="return confirm('Are you sure you want to delete this item?')"
                                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-300 inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete Item
                                        </a>
                                    </div>
                                </div>
                            <?php elseif (isset($_SESSION['user_id'])): ?>
                                <div class="border-t border-gray-200 pt-6">
                                    <h3 class="text-lg font-semibold mb-3 text-gray-700">Contact Information</h3>
                                    <p class="mb-4">If you have information about this item, you can contact the poster:</p>
                                    <div class="flex flex-wrap gap-3">
                                        <a href="<?php echo url('pages/user/claim_item.php?id=' . $item['id']); ?>"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-300 inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Claim This Item
                                        </a>
                                        <a href="mailto:<?php echo h($item['email']); ?>"
                                            class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-300 inline-flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            Contact via Email
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="border-t border-gray-200 pt-6">
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 mr-3"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            <p class="text-yellow-700">
                                                Please <a href="<?php echo url('pages/auth/login.php'); ?>"
                                                    class="font-bold text-yellow-800 underline hover:text-yellow-900">log
                                                    in</a> to contact the person who posted this item.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Include footer
    require_once ROOT_PATH . '/includes/templates/footer.php';
    ?>