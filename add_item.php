<?php
// Set page title
$page_title = "Add Item";

// Include header
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once 'includes/db.php';
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
        $image_path = null;
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
                    $image_path = $destination;
                } else {
                    $error = "Failed to upload image";
                }
            }
        }
        
        if (empty($error)) {
            // Insert item into database
            $sql = "INSERT INTO items (user_id, title, description, status, location, image_path) 
                    VALUES (:user_id, :title, :description, :status, :location, :image_path)";
            
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $_SESSION['user_id'],
                    ':title' => $title,
                    ':description' => $description,
                    ':status' => $status,
                    ':location' => $location,
                    ':image_path' => $image_path
                ]);
                
                $success = "Item posted successfully!";
                // Clear form data
                $title = $description = $location = "";
                
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h2 class="text-2xl font-bold mb-6">Post a New Item</h2>
    
    <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p><?php echo $success; ?></p>
            <p class="mt-2">
                <a href="index.php" class="text-green-700 font-bold underline">View all items</a> or 
                <a href="dashboard.php" class="text-green-700 font-bold underline">view your items</a>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <form action="add_item.php" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                Status
            </label>
            <select name="status" id="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="lost" <?php echo isset($status) && $status === 'lost' ? 'selected' : ''; ?>>Lost Item</option>
                <option value="found" <?php echo isset($status) && $status === 'found' ? 'selected' : ''; ?>>Found Item</option>
            </select>
            <p class="text-sm text-gray-600 mt-1">Or use dedicated pages: 
                <a href="report_lost_item.php" class="text-blue-500 hover:underline">Report Lost Item</a> / 
                <a href="report_found_item.php" class="text-blue-500 hover:underline">Report Found Item</a>
            </p>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                Title *
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="title" type="text" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
            <p class="text-sm text-gray-600 mt-1">Example: "Gold watch with brown strap" or "Black wallet with ID"</p>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Description *
            </label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                      id="description" name="description" rows="4" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
            <p class="text-sm text-gray-600 mt-1">Include details like brand, color, and any identifying features</p>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                Location *
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="location" type="text" name="location" value="<?php echo isset($location) ? htmlspecialchars($location) : ''; ?>" required>
            <p class="text-sm text-gray-600 mt-1">Where the item was lost or found (e.g., "Central Park" or "Main Library, 2nd floor")</p>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
                Image (Optional)
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="image" type="file" name="image" accept="image/*">
            <p class="text-sm text-gray-600 mt-1">Upload a clear photo of the item (Max size: 2MB)</p>
        </div>
        
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                    type="submit">
                Post Item
            </button>
            <a href="index.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>