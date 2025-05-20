<?php
// Define root path
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Set page title
$page_title = "Report Found Item";

// Include essential files
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/includes/helpers/functions.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    set_flash_message('error', 'You must be logged in to report a found item');
    redirect('pages/auth/login.php');
}

$success = $error = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $status = 'found'; // Always found for this form
    
    // Validate input
    if (empty($title) || empty($description) || empty($location)) {
        $error = "Please fill all required fields";
    } else {
        // Handle image upload if provided
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Create uploads directory if it doesn't exist
            $upload_dir = ROOT_PATH . '/public/uploads';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error = "Only JPG, PNG and GIF images are allowed";
            } elseif ($_FILES['image']['size'] > $max_size) {
                $error = "Image size must be less than 2MB";
            } else {
                $file_name = time() . '_' . $_FILES['image']['name']; 
                $destination = 'public/uploads/' . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], ROOT_PATH . '/' . $destination)) {
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
                
                $success = "Found item reported successfully!";
                // Clear form data
                $title = $description = $location = "";
                
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

// Include header
require_once ROOT_PATH . '/includes/templates/header.php';
?>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h2 class="text-2xl font-bold mb-6">Report a Found Item</h2>
    
    <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p><?php echo $success; ?></p>
            <p class="mt-2">
                <a href="<?php echo url('index.php'); ?>" class="text-green-700 font-bold underline">View all items</a> or 
                <a href="<?php echo url('pages/user/dashboard.php'); ?>" class="text-green-700 font-bold underline">go to your dashboard</a>
            </p>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <form action="<?php echo url('pages/items/report_found_item.php'); ?>" method="post" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                Item Title *
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="title" type="text" name="title" value="<?php echo isset($title) ? h($title) : ''; ?>" required>
            <p class="text-sm text-gray-600 mt-1">Example: "Gold watch with brown strap" or "Black wallet with ID"</p>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Description *
            </label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                      id="description" name="description" rows="4" required><?php echo isset($description) ? h($description) : ''; ?></textarea>
            <p class="text-sm text-gray-600 mt-1">Include details like brand, color, and any identifying features</p>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                Where did you find it? *
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="location" type="text" name="location" value="<?php echo isset($location) ? h($location) : ''; ?>" required>
            <p class="text-sm text-gray-600 mt-1">Be as specific as possible (e.g., "Central Park near the fountain" or "Main Library, 2nd floor")</p>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
                Image (Optional)
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="image" type="file" name="image" accept="image/*">
            <p class="text-sm text-gray-600 mt-1">Upload a clear photo of the item (Max size: 2MB)</p>
            <div id="image-preview" class="mt-2 hidden">
                <img src="" alt="Preview" class="max-w-xs rounded">
            </div>
        </div>
        
        <div class="flex items-center justify-between">
            <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                    type="submit">
                Report Found Item
            </button>
            <a href="<?php echo url('pages/user/dashboard.php'); ?>" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Back to Dashboard
            </a>
        </div>
    </form>
</div>

<?php
// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>