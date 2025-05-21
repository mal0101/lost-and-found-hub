<?php
// Define root path
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Get item type from URL parameter with 'lost' as default
$item_type = isset($_GET['type']) && $_GET['type'] === 'found' ? 'found' : 'lost';

// Set dynamic page title
$page_title = "Report " . ucfirst($item_type) . " Item";

// Include essential files
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/includes/helpers/functions.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    set_flash_message('error', 'You must be logged in to report a ' . $item_type . ' item');
    redirect('pages/auth/login.php');
}

$success = $error = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $status = trim($_POST['item_status']); // Get status from form
    $category = isset($_POST['category']) ? trim($_POST['category']) : null;
    $date_occurred = !empty($_POST['date_occurred']) ? $_POST['date_occurred'] : date('Y-m-d');

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
            $sql = "INSERT INTO items (user_id, title, description, status, location, image_path, date_occured) 
                    VALUES (:user_id, :title, :description, :status, :location, :image_path, :date_occured)";

            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $_SESSION['user_id'],
                    ':title' => $title,
                    ':description' => $description,
                    ':status' => $status,
                    ':location' => $location,
                    ':image_path' => $image_path,
                    ':date_occured' => $date_occurred
                ]);

                $success = ucfirst($status) . " item reported successfully!";
                // Clear form data
                $title = $description = $location = $category = "";

            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

// Include header
require_once ROOT_PATH . '/includes/templates/header.php';
?>

<div class="max-w-4xl mx-auto my-8 px-4">
    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Card Header with Tabs -->
        <div class="bg-blue-600 p-6">
            <h2 class="text-2xl font-bold text-white mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <?php if ($item_type === 'lost'): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <?php else: ?>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    <?php endif; ?>
                </svg>
                Report an Item
            </h2>

            <!-- Tab Navigation -->
            <div class="flex border-b border-blue-500">
                <a href="<?php echo url('pages/items/report_item.php?type=lost'); ?>"
                    class="<?php echo $item_type === 'lost' ? 'bg-white text-blue-600' : 'bg-blue-700 text-white hover:bg-blue-800'; ?> inline-block px-6 py-3 font-medium rounded-t-lg mr-1 transition duration-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    I Lost Something
                </a>
                <a href="<?php echo url('pages/items/report_item.php?type=found'); ?>"
                    class="<?php echo $item_type === 'found' ? 'bg-white text-blue-600' : 'bg-blue-700 text-white hover:bg-blue-800'; ?> inline-block px-6 py-3 font-medium rounded-t-lg transition duration-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    I Found Something
                </a>
            </div>
        </div>
        <!-- Card Body -->
        <div class="p-6">
            <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded flex items-start"
                    role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5 text-green-500" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="font-medium"><?php echo $success; ?></p>
                        <p class="mt-2">
                            <a href="<?php echo url('index.php'); ?>"
                                class="text-green-700 font-bold underline hover:text-green-800">View all items</a> or
                            <a href="<?php echo url('pages/user/dashboard.php'); ?>"
                                class="text-green-700 font-bold underline hover:text-green-800">go to your dashboard</a>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded flex items-start"
                    role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5 text-red-500" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?php echo url('pages/items/report_item.php?type=' . $item_type); ?>" method="post"
                enctype="multipart/form-data" id="reportForm">
                <!-- Hidden status field -->
                <input type="hidden" name="item_status" value="<?php echo $item_type; ?>">

                <!-- Item Information Section -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Item Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title Field -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                                Item Title <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                    </svg>
                                </div>
                                <input
                                    class="pl-10 block w-full shadow-sm rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3"
                                    id="title" type="text" name="title" placeholder="Gold watch with brown strap"
                                    value="<?php echo isset($title) ? h($title) : ''; ?>" required>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Be descriptive so others can identify the item</p>
                        </div>

                        <!-- Category Field -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                                Category
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <select
                                    class="pl-10 block w-full shadow-sm rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3"
                                    id="category" name="category">
                                    <option value="">Select a category</option>
                                    <option value="electronics">Electronics</option>
                                    <option value="jewelry">Jewelry & Watches</option>
                                    <option value="clothing">Clothing & Accessories</option>
                                    <option value="documents">Documents & IDs</option>
                                    <option value="keys">Keys</option>
                                    <option value="bags">Bags & Luggage</option>
                                    <option value="pets">Pets</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Date Field -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="date_occurred">
                                Date <?php echo $item_type === 'lost' ? 'Lost' : 'Found'; ?> <span
                                    class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input
                                    class="pl-10 block w-full shadow-sm rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3"
                                    id="date_occurred" type="date" name="date_occurred"
                                    value="<?php echo isset($date_occurred) ? h($date_occurred) : date('Y-m-d'); ?>"
                                    max="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>

                        <!-- Description Field -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <textarea
                                    class="block w-full shadow-sm rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3"
                                    id="description" name="description" rows="4"
                                    placeholder="Include details like brand, color, size, identifying marks, etc."
                                    required><?php echo isset($description) ? h($description) : ''; ?></textarea>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">The more details you provide, the better chance of
                                recovery</p>
                        </div>
                    </div>
                </div>

                <!-- Location Section -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Location Details
                    </h3>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                            Where did you <?php echo $item_type === 'lost' ? 'lose' : 'find'; ?> it? <span
                                class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <input
                                class="pl-10 block w-full shadow-sm rounded-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-3"
                                id="location" type="text" name="location"
                                placeholder="Be as specific as possible (e.g., Central Park near the fountain)"
                                value="<?php echo isset($location) ? h($location) : ''; ?>" required>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Specific locations increase the chances of recovery</p>
                    </div>
                </div>

                <!-- Image Upload Section -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Image Upload
                    </h3>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="image">
                            Item Image (Optional)
                        </label>
                        <div class="mt-1 flex items-center">
                            <label
                                class="relative cursor-pointer bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <span>Upload a file</span>
                                <input id="image" name="image" type="file" accept="image/*" class="sr-only"
                                    onchange="showPreview(event)">
                            </label>
                            <p class="ml-3 text-sm text-gray-500">JPG, PNG, GIF up to 2MB</p>
                        </div>
                        <div id="image-preview" class="mt-3 hidden">
                            <img src="" alt="Preview" class="max-h-48 rounded-md">
                            <button type="button" onclick="clearImage()"
                                class="mt-2 text-sm text-red-600 hover:text-red-800">
                                Remove image
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row justify-between items-center">
                    <button
                        class="w-full sm:w-auto bg-<?php echo $item_type === 'lost' ? 'red' : 'green'; ?>-600 hover:bg-<?php echo $item_type === 'lost' ? 'red' : 'green'; ?>-700 hover:text-white font-bold py-3 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-<?php echo $item_type === 'lost' ? 'red' : 'green'; ?>-500 transition duration-150 mb-4 sm:mb-0"
                        type="submit">
                        <?php echo $item_type === 'lost' ? 'Report Lost Item' : 'Report Found Item'; ?>
                    </button>
                    <a href="<?php echo url('pages/user/dashboard.php'); ?>"
                        class="text-blue-600 hover:text-blue-800 font-medium">
                        Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for image preview -->
<script>
    function showPreview(event) {
        if (event.target.files.length > 0) {
            const preview = document.getElementById('image-preview');
            const previewImg = preview.querySelector('img');

            previewImg.src = URL.createObjectURL(event.target.files[0]);
            preview.classList.remove('hidden');
        }
    }

    function clearImage() {
        const input = document.getElementById('image');
        const preview = document.getElementById('image-preview');

        input.value = '';
        preview.classList.add('hidden');
    }
</script>

<?php
// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>