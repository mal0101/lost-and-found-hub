<?php
// Define root path
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Set page title
$page_title = "Claim Item";

// Include essential files
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/includes/helpers/functions.php';

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    set_flash_message('error', 'You must be logged in to claim an item');
    redirect('pages/auth/login.php');
}

// Check if item ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    set_flash_message('error', 'No item specified');
    redirect('index.php');
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

// If item doesn't exist, redirect to home
if (!$item) {
    set_flash_message('error', 'Item not found');
    redirect('index.php');
}

// Cannot claim your own items
if ($item['user_id'] == $_SESSION['user_id']) {
    set_flash_message('error', 'You cannot claim your own item');
    redirect('pages/items/item_details.php?id=' . $item['id']);
}

$success = $error = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $contact_info = trim($_POST['contact_info']);
    
    // Validate input
    if (empty($message) || empty($contact_info)) {
        $error = "Please fill all required fields";
    } else {
        // In a real application, you would:
        // 1. Save the claim to a database
        // 2. Send an email notification to the item owner
        // For simplicity, we'll just show a success message
        
        $success = "Your claim has been submitted! The owner of this item will contact you if your information matches.";
        
        // For demonstration, let's pretend we're sending an email
        $to = $item['email'];
        $subject = "Claim for your " . ($item['status'] === 'lost' ? 'lost' : 'found') . " item: " . $item['title'];
        $email_message = "Hello " . $item['username'] . ",\n\n";
        $email_message .= "Someone has claimed your " . ($item['status'] === 'lost' ? 'lost' : 'found') . " item: " . $item['title'] . "\n\n";
        $email_message .= "Message: " . $message . "\n\n";
        $email_message .= "Contact information: " . $contact_info . "\n\n";
        $email_message .= "You can view the item at: " . url('pages/items/item_details.php?id=' . $item['id']) . "\n\n";
        $email_message .= "Regards,\nThe Lost and Found Team";
        
        // Uncomment this to actually send the email in a production environment
        // mail($to, $subject, $email_message);
    }
}

// Include header
require_once ROOT_PATH . '/includes/templates/header.php';
?>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h2 class="text-2xl font-bold mb-6">Claim This Item</h2>
    
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Item Details</h3>
        <p>
            <span class="inline-block px-2 py-1 rounded <?php echo $item['status'] === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?> text-sm font-semibold">
                <?php echo ucfirst($item['status']); ?>
            </span>
            <span class="font-semibold"><?php echo h($item['title']); ?></span>
        </p>
        <p class="text-sm text-gray-700">Posted by: <?php echo h($item['username']); ?></p>
    </div>
    
    <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p><?php echo $success; ?></p>
            <p class="mt-2">
                <a href="<?php echo url('index.php'); ?>" class="text-green-700 font-bold underline">Return to homepage</a>
            </p>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <form action="<?php echo url('pages/user/claim_item.php?id=' . $item['id']); ?>" method="post">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="message">
                    Describe why you think this is your item or how you can identify it *
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                          id="message" name="message" rows="4" required><?php echo isset($message) ? h($message) : ''; ?></textarea>
                <p class="text-sm text-gray-600 mt-1">Provide unique identifying features that only the true owner would know</p>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="contact_info">
                    Your Contact Information *
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                          id="contact_info" name="contact_info" rows="2" required><?php echo isset($contact_info) ? h($contact_info) : ''; ?></textarea>
                <p class="text-sm text-gray-600 mt-1">Phone number, email, or preferred method of contact</p>
            </div>
            
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                        type="submit">
                    Submit Claim
                </button>
                <a href="<?php echo url('pages/items/item_details.php?id=' . $item['id']); ?>" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancel
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>