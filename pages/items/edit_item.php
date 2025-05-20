<?php
// Set page title
$page_title = "Edit Item";

// Include header
require_once __DIR__ . '/../../includes/templates/header.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /pages/auth/login.php");
    exit;
}

// Include database connection
require_once __DIR__ . '/../../config/db.php';

// Check if item ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: /pages/user/dashboard.php");    exit;
}

// Fetch item details and check ownership
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$item = $stmt->fetch();

// If item doesn't exist or doesn't belong to current user, redirect
if (!$item) {
    header("Location: /pages/user/dashboard.php");    exit;
}

$success = $error = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $location = trim($_POST['location']);
    
    // Validate input
    if (empty($title) || empty($description) || empty($location)) {
        $error = "Please fill all required fields";
    } else {
        // Handle image upload if provided
        $image_path = $item['image_path']; // Keep existing image by default
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Create uploads directory if it doesn't exist
            if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error = "Only JPG, PNG and GIF images are allowed";
            } elseif ($_FILES['image']['size'] > $max_size) {
                $error = "Image size must be less than 2MB";
            } else {
                $file_name = time() . '_' . $_FILES['image']['name']; 
                $destination = 'uploads/' . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    // Delete old image if exists and not the default
                    if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                        unlink($item['image_path']);
                    }
                    $image_path = $destination;
                } else {
                    $error = "Failed to upload image";
                }
            }
        }
        
        // Handle image deletion if requested
        if (isset($_POST['delete_image']) && $_POST['delete_image'] == 1) {
            if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                unlink($item['image_path']);
            }
            $image_path = null;
        }
        
        if (empty($error)) {
            // Update item in database
            $sql = "UPDATE items SET title = :title, description = :description, status = :status, 
                    location = :location, image_path = :image_path WHERE id = :id";
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':title' => $title,
                    ':description' => $description,
                    ':status' => $status,
                    ':location' => $location,
                    ':image_path' => $image_path,
                    ':id' => $item['id']
                ]);
                
                $success = "Item updated successfully!";
                
                // Refresh item data
                $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
                $stmt->execute([$item['id']]);
                $item = $stmt->fetch();
                
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

// Pre-fill form with item data
$title = $item['title'];
$description = $item['description'];
$status = $item['status'];
$location = $item['location'];
?>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h2 class="text-2xl font-bold mb-6">Edit Item</h2>
    
    <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p><?php echo $success; ?></p>
            <p class="mt-2">
                <a href="item_details.php?id=<?php echo $item['id']; ?>" class="text-green-700 font-bold underline">View item details</a> or 
                <a href="dashboard.php" class="text-green-700 font-bold underline">go to your dashboard</a>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <form action="edit_item.php?id=<?php echo $item['id']; ?>" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                Status
            </label>
            <select name="status" id="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="lost" <?php echo $status === 'lost' ? 'selected' : ''; ?>>Lost Item</option>
                <option value="found" <?php echo $status === 'found' ? 'selected' : ''; ?>>Found Item</option>
            </select>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                Title *
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="title" type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Description *
            </label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                      id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                Location *
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="location" type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Image
            </label>
            <?php if (!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                <div class="mb-2">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="Item Image" class="max-w-xs rounded">
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="delete_image" value="1" class="form-checkbox">
                            <span class="ml-2">Delete current image</span>
                        </label>
                    </div>
                </div>
            <?php endif; ?>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="image" type="file" name="image" accept="image/*">
            <p class="text-sm text-gray-600 mt-1">Upload a new image or leave empty to keep the current one (Max size: 2MB)</p>
        </div>
        
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                    type="submit">
                Update Item
            </button>
            <a href="dashboard.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php
// Include footer
require_once __DIR__ . '/../../includes/templates/footer.php';
?>