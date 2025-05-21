<?php
// Define root path
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Set page title
$page_title = "Dashboard";

// Include essential files
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/includes/helpers/functions.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    set_flash_message('error', 'You must be logged in to view your dashboard');
    redirect('pages/auth/login.php');
}

// Handle delete request
if (isset($_GET['delete'])) {
    $item_id = $_GET['delete'];

    // Check if the item belongs to the current user
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
    $stmt->execute([$item_id, $_SESSION['user_id']]);
    $item = $stmt->fetch();

    if ($item) {
        // Delete the item
        $delete_stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
        $delete_stmt->execute([$item_id]);

        // Delete associated image if exists
        if (!empty($item['image_path']) && file_exists(ROOT_PATH . '/' . $item['image_path'])) {
            unlink(ROOT_PATH . '/' . $item['image_path']);
        }

        set_flash_message('success', 'Item deleted successfully!');
        redirect('pages/user/dashboard.php');
    }
}

// Fetch user's items
$stmt = $pdo->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY date_posted DESC");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

// Include header
require_once ROOT_PATH . '/includes/templates/header.php';
?>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h2 class="text-2xl font-bold mb-6">My Dashboard</h2>

    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-3">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo url('pages/items/report_item.php'); ?>"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Report Item
            </a>
            <a href="<?php echo url('pages/items/item_list.php'); ?>"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Browse All Items
            </a>
        </div>
    </div>

    <h3 class="text-xl font-semibold mb-3">My Items</h3>

    <?php if (count($items) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-100 text-left">Title</th>
                        <th class="py-3 px-4 bg-gray-100 text-left">Status</th>
                        <th class="py-3 px-4 bg-gray-100 text-left">Date Posted</th>
                        <th class="py-3 px-4 bg-gray-100 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="py-3 px-4 border-b"><?php echo h($item['title']); ?></td>
                            <td class="py-3 px-4 border-b">
                                <span
                                    class="px-2 py-1 rounded <?php echo $item['status'] === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?> text-sm font-semibold">
                                    <?php echo ucfirst($item['status']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b"><?php echo date('M j, Y', strtotime($item['date_posted'])); ?></td>
                            <td class="py-3 px-4 border-b">
                                <a href="<?php echo url('pages/items/item_details.php?id=' . $item['id']); ?>"
                                    class="text-blue-500 hover:underline mr-2">View</a>
                                <a href="<?php echo url('pages/items/edit_item.php?id=' . $item['id']); ?>"
                                    class="text-green-500 hover:underline mr-2">Edit</a>
                                <a href="<?php echo url('pages/user/dashboard.php?delete=' . $item['id']); ?>"
                                    onclick="return confirm('Are you sure you want to delete this item?')"
                                    class="text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="bg-gray-100 p-4 rounded">
            <p>You haven't posted any items yet.</p>
            <p class="mt-2">Use the buttons above to report a lost or found item.</p>
        </div>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>