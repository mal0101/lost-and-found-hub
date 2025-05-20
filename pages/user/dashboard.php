<?php
// Set page title
$page_title = "Dashboard";

// Include header
require_once __DIR__ . '/../../includes/templates/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/auth/login.php");
    exit;
}

// Include database connection
require_once __DIR__ . '/../../config/db.php';

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
        if (!empty($item['image_path']) && file_exists($item['image_path'])) {
            unlink($item['image_path']);
        }
        
        $success = "Item deleted successfully!";
    }
}

// Fetch user's items
$stmt = $pdo->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY date_posted DESC");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();
?>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h2 class="text-2xl font-bold mb-6">My Dashboard</h2>
    
    <?php if (isset($success)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>
    
    <div class="mb-6">
        <h3 class="text-xl font-semibold mb-3">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <a href="report_lost_item.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Report Lost Item
            </a>
            <a href="report_found_item.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Report Found Item
            </a>
            <a href="items_list.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
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
                            <td class="py-3 px-4 border-b"><?php echo htmlspecialchars($item['title']); ?></td>
                            <td class="py-3 px-4 border-b">
                                <span class="px-2 py-1 rounded <?php echo $item['status'] === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?> text-sm font-semibold">
                                    <?php echo ucfirst($item['status']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b"><?php echo date('M j, Y', strtotime($item['date_posted'])); ?></td>
                            <td class="py-3 px-4 border-b">
                                <a href="item_details.php?id=<?php echo $item['id']; ?>" class="text-blue-500 hover:underline mr-2">View</a>
                                <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="text-green-500 hover:underline mr-2">Edit</a>
                                <a href="dashboard.php?delete=<?php echo $item['id']; ?>" 
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
require_once __DIR__ . '/../../includes/templates/footer.php';

?>