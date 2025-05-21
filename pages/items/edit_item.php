<?php
// Define root path
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Set page title
$page_title = "Edit Item";

// Include essential files
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/includes/helpers/functions.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    set_flash_message('error', 'You must be logged in to edit an item');
    redirect('pages/auth/login.php');
}

// Check if item ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    set_flash_message('error', 'No item specified');
    redirect('pages/user/dashboard.php');
}

// Fetch item details and check ownership
$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$item = $stmt->fetch();

// If item doesn't exist or doesn't belong to current user, redirect
if (!$item) {
    set_flash_message('error', 'You do not have permission to edit this item');
    redirect('pages/user/dashboard.php');
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
                    // Delete old image if exists and not the default
                    if (!empty($item['image_path']) && file_exists(ROOT_PATH . '/' . $item['image_path'])) {
                        unlink(ROOT_PATH . '/' . $item['image_path']);
                    }
                    $image_path = $destination;
                } else {
                    $error = "Failed to upload image";
                }
            }
        }

        // Handle image deletion if requested
        if (isset($_POST['delete_image']) && $_POST['delete_image'] == 1) {
            if (!empty($item['image_path']) && file_exists(ROOT_PATH . '/' . $item['image_path'])) {
                unlink(ROOT_PATH . '/' . $item['image_path']);
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

// Include header
require_once ROOT_PATH . '/includes/templates/header.php';
?>

<!-- Back navigation -->
<div class="max-w-4xl mx-auto my-8 px-4 sm:px-0">
    <div class="mb-6">
        <a href="<?php echo url('pages/items/item_details.php?id=' . $item['id']); ?>"
            class="flex items-center text-blue-600 hover:text-blue-800 transition duration-300 group">
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 mr-1 group-hover:-translate-x-1 transition-transform duration-300" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="font-medium">Back to Item Details</span>
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="border-b border-gray-200">
            <div class="px-6 py-5">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Item
                </h2>
            </div>
        </div>

        <div class="p-6">
            <?php if ($success): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-5 mb-6 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium"><?php echo $success; ?></span>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-3">
                        <a href="<?php echo url('pages/items/item_details.php?id=' . $item['id']); ?>"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium transition duration-300 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Item Details
                        </a>
                        <a href="<?php echo url('pages/user/dashboard.php'); ?>"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition duration-300 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-5 mb-6 rounded-md" role="alert">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-red-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium"><?php echo $error; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?php echo url('pages/items/edit_item.php?id=' . $item['id']); ?>" method="post"
                enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="flex items-center text-gray-700 font-medium mb-2" for="status">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Status
                    </label>
                    <div class="relative">
                        <select name="status" id="status"
                            class="block w-full pl-3 pr-10 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 appearance-none bg-white text-gray-700"
                            required>
                            <option value="lost" <?php echo $status === 'lost' ? 'selected' : ''; ?>>Lost Item</option>
                            <option value="found" <?php echo $status === 'found' ? 'selected' : ''; ?>>Found Item</option>
                        </select>
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="flex items-center text-gray-700 font-medium mb-2" for="title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Title <span class="text-red-600">*</span>
                    </label>
                    <input
                        class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                        id="title" type="text" name="title" value="<?php echo h($title); ?>"
                        placeholder="Enter item title" required>
                </div>

                <div>
                    <label class="flex items-center text-gray-700 font-medium mb-2" for="description">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        Description <span class="text-red-600">*</span>
                    </label>
                    <textarea
                        class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                        id="description" name="description" rows="5"
                        placeholder="Provide detailed description of the item"
                        required><?php echo h($description); ?></textarea>
                    <p class="mt-2 text-sm text-gray-500">Include details like color, brand, unique features, etc.</p>
                </div>

                <div>
                    <label class="flex items-center text-gray-700 font-medium mb-2" for="location">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Location <span class="text-red-600">*</span>
                    </label>
                    <input
                        class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                        id="location" type="text" name="location" value="<?php echo h($location); ?>"
                        placeholder="Where was the item lost/found?" required>
                </div>

                <div>
                    <label class="flex items-center text-gray-700 font-medium mb-2" for="image">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Image
                    </label>

                    <?php if (!empty($item['image_path']) && file_exists(ROOT_PATH . '/' . $item['image_path'])): ?>
                        <div class="mb-4 border rounded-lg p-4 bg-gray-50">
                            <p class="text-sm text-gray-600 mb-2">Current image:</p>
                            <div class="relative max-w-sm">
                                <img src="<?php echo url($item['image_path']); ?>" alt="Item Image"
                                    class="rounded-lg shadow-sm max-h-60 object-contain bg-white">
                                <div class="mt-3">
                                    <label class="inline-flex items-center text-sm">
                                        <input type="checkbox" name="delete_image" value="1"
                                            class="rounded text-blue-600 focus:ring-blue-500 cursor-pointer w-5 h-5">
                                        <span class="ml-2 text-gray-700">Delete current image</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mt-2">
                        <div
                            class="relative border border-gray-300 rounded-md px-4 py-3 bg-white shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition duration-150">
                            <label for="image"
                                class="absolute -top-2 left-2 -mt-px inline-block px-1 bg-white text-xs font-medium text-gray-700">
                                Choose a new image
                            </label>
                            <input class="block w-full text-gray-700 focus:outline-none" id="image" type="file"
                                name="image" accept="image/*">
                        </div>
                        <p class="mt-2 text-sm text-gray-500 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Upload a new image or leave empty to keep the current one (Max size: 2MB)
                        </p>
                    </div>

                    <div id="image-preview" class="mt-4 hidden">
                        <p class="text-sm text-gray-600 mb-2">Image preview:</p>
                        <img src="" alt="Preview" class="rounded-lg shadow-sm max-h-60 object-contain bg-white">
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 flex flex-wrap justify-between items-center gap-4">
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium transition duration-300 inline-flex items-center shadow-sm"
                        type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Update Item
                    </button>
                    <div class="flex gap-4">
                        <a href="<?php echo url('pages/items/item_details.php?id=' . $item['id']); ?>"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Item
                        </a>
                        <a href="<?php echo url('pages/user/dashboard.php'); ?>"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add simple JavaScript for image preview -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fileInput = document.getElementById('image');
        const previewContainer = document.getElementById('image-preview');
        const previewImage = previewContainer.querySelector('img');

        fileInput.addEventListener('change', function () {
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.addEventListener('load', function () {
                    previewImage.setAttribute('src', this.result);
                    previewContainer.classList.remove('hidden');
                });

                reader.readAsDataURL(file);
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    });
</script>

<?php
// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>