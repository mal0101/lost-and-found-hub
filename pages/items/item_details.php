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
    echo '<div class="bg-white p-6 rounded shadow">';
    echo '<p class="text-red-500">Error: ' . $error_message . '</p>';
    echo '<p><a href="' . url('index.php') . '" class="text-blue-500 hover:underline">Return to homepage</a></p>';
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
    echo '<div class="bg-white p-6 rounded shadow">';
    echo '<p class="text-red-500">Error: ' . $error_message . '</p>';
    echo '<p><a href="' . url('index.php') . '" class="text-blue-500 hover:underline">Return to homepage</a></p>';
    echo '</div>';
    require_once ROOT_PATH . '/includes/templates/footer.php';
    exit;
}

$is_owner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $item['user_id'];
?>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <div class="mb-4">
        <a href="javascript:history.back()" class="text-blue-500 hover:underline">&larr; Back</a>
    </div>
    
    <div class="flex flex-wrap">
        <?php if (!empty($item['image_path']) && file_exists(ROOT_PATH . '/' . $item['image_path'])): ?>
            <div class="w-full md:w-1/3 pr-4 mb-4">
                <img src="<?php echo url($item['image_path']); ?>" alt="<?php echo h($item['title']); ?>" class="w-full rounded">
            </div>
            <div class="w-full md:w-2/3">
        <?php else: ?>
            <div class="w-full">
        <?php endif; ?>
            
            <span class="inline-block px-3 py-1 rounded <?php echo $item['status'] === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?> text-sm font-semibold mb-2">
                <?php echo ucfirst($item['status']); ?>
            </span>
            
            <h1 class="text-3xl font-bold mb-4"><?php echo h($item['title']); ?></h1>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Description</h3>
                <p class="whitespace-pre-line"><?php echo nl2br(h($item['description'])); ?></p>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Location</h3>
                <p><?php echo h($item['location']); ?></p>
            </div>
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Posted By</h3>
                <p><?php echo h($item['username']); ?></p>
                <p class="text-sm text-gray-500">Posted on: <?php echo date('F j, Y, g:i a', strtotime($item['date_posted'])); ?></p>
            </div>
            
            <?php if ($is_owner): ?>
                <div class="mb-6 border-t pt-4">
                    <h3 class="text-lg font-semibold mb-2">Actions</h3>
                    <div class="flex space-x-2">
                        <a href="<?php echo url('pages/items/edit_item.php?id=' . $item['id']); ?>" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Edit Item
                        </a>
                        <a href="<?php echo url('pages/user/dashboard.php?delete=' . $item['id']); ?>" 
                           onclick="return confirm('Are you sure you want to delete this item?')" 
                           class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Delete Item
                        </a>
                    </div>
                </div>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                <div class="mb-6 border-t pt-4">
                    <h3 class="text-lg font-semibold mb-2">Contact</h3>
                    <p>If you have information about this item, please contact the poster:</p>
                    <a href="<?php echo url('pages/user/claim_item.php?id=' . $item['id']); ?>" class="mt-2 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Claim This Item
                    </a>
                    <a href="mailto:<?php echo h($item['email']); ?>" class="mt-2 inline-block ml-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Contact via Email
                    </a>
                </div>
            <?php else: ?>
                <div class="mb-6 border-t pt-4">
                    <p class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                        Please <a href="<?php echo url('pages/auth/login.php'); ?>" class="font-bold underline">log in</a> to contact the person who posted this item.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>