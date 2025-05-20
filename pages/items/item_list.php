<?php
// Set page title
$page_title = "All Items";

// Include header
require_once __DIR__ . '/../../includes/templates/header.php';

// Include database connection
require_once __DIR__ . '/../../config/db.php';

// Pagination settings
$items_per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Fetch items from the database
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query
$sql_count = "SELECT COUNT(*) FROM items i JOIN users u ON i.user_id = u.id WHERE 1 = 1";
$sql = "SELECT i.*, u.username FROM items i JOIN users u ON i.user_id = u.id WHERE 1 = 1";

$params = [];

if ($status_filter) {
    $sql .= " AND i.status = ?";
    $sql_count .= " AND i.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $sql .= " AND (i.title LIKE ? OR i.description LIKE ? OR i.location LIKE ?)";
    $sql_count .= " AND (i.title LIKE ? OR i.description LIKE ? OR i.location LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

// Get total items count
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_items = $stmt_count->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

// Add pagination to the query
$sql .= " ORDER BY i.date_posted DESC LIMIT $offset, $items_per_page";

// Get items for current page
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="bg-white rounded shadow p-6 mb-6">
    <h1 class="text-2xl font-bold mb-4">All Items</h1>
    
    <div class="mt-6">
        <form action="items_list.php" method="get" class="flex flex-wrap gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Search for items..." 
                       class="w-full px-4 py-2 border rounded"
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div>
                <select name="status" class="px-4 py-2 border rounded">
                    <option value="">All Items</option>
                    <option value="lost" <?php echo $status_filter === 'lost' ? 'selected' : ''; ?>>Lost Items</option>
                    <option value="found" <?php echo $status_filter === 'found' ? 'selected' : ''; ?>>Found Items</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Filter
                </button>
            </div>
            <?php if ($search || $status_filter): ?>
                <div>
                    <a href="items_list.php" class="text-blue-500 hover:underline">Clear Filters</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (count($items) > 0): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($items as $item): ?>
            <div class="bg-white rounded shadow overflow-hidden">
                <?php if (!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                    <div class="h-48 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>
                <div class="p-4">
                    <span class="inline-block px-2 py-1 rounded <?php echo $item['status'] === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?> text-sm font-semibold mb-2">
                        <?php echo ucfirst($item['status']); ?>
                    </span>
                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="text-gray-700"><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . (strlen($item['description']) > 100 ? '...' : ''); ?></p>
                    
                    <div class="mt-4 text-sm text-gray-600">
                        <p>Location: <?php echo htmlspecialchars($item['location']); ?></p>
                        <p>Posted by: <?php echo htmlspecialchars($item['username']); ?></p>
                        <p>Date: <?php echo date('M j, Y', strtotime($item['date_posted'])); ?></p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="item_details.php?id=<?php echo $item['id']; ?>" class="text-blue-500 hover:underline">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="mt-8 flex justify-center">
            <div class="flex space-x-1">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                       class="px-4 py-2 bg-white border rounded hover:bg-gray-100">
                        Previous
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="px-4 py-2 bg-blue-500 text-white border rounded">
                            <?php echo $i; ?>
                        </span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                           class="px-4 py-2 bg-white border rounded hover:bg-gray-100">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo $status_filter ? '&status='.$status_filter : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                       class="px-4 py-2 bg-white border rounded hover:bg-gray-100">
                        Next
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    
<?php else: ?>
    <div class="bg-white p-6 rounded shadow text-center">
        <p class="text-lg text-gray-700">No items found matching your criteria.</p>
        <?php if ($search || $status_filter): ?>
            <p class="mt-2">
                <a href="items_list.php" class="text-blue-500 hover:underline">Clear filters</a> to see all items.
            </p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
// Include footer
require_once __DIR__ . '/../../includes/templates/footer.php';
?>