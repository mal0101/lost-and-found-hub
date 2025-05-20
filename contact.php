<?php
// Define root path constant
define('ROOT_PATH', __DIR__);

// Set page title
$page_title = "Contact Us";

// Include essential files
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/includes/helpers/functions.php';
require_once ROOT_PATH . '/includes/templates/header.php';

$success = $error = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validate input
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill all required fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        // In a real application, you would:
        // 1. Save the message to a database
        // 2. Send an email notification to the administrator
        // For simplicity, we'll just show a success message
        
        $success = "Your message has been sent! We'll get back to you soon.";
        
        // For demonstration, let's pretend we're sending an email
        $to = "admin@example.com"; // Replace with your email
        $email_subject = "Contact Form: " . $subject;
        $email_message = "Name: " . $name . "\n";
        $email_message .= "Email: " . $email . "\n\n";
        $email_message .= "Message:\n" . $message;
        
        // Uncomment this to actually send the email in a production environment
        // mail($to, $email_subject, $email_message);
        
        // Clear form data
        $name = $email = $subject = $message = "";
    }
}
?>

<div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h2 class="text-2xl font-bold mb-6">Contact Us</h2>
    
    <p class="mb-6">
        Have questions or need assistance with our Lost and Found system? Fill out the form below and we'll get back to you as soon as possible.
    </p>
    
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
        
        <form action="<?php echo url('contact.php'); ?>" method="post">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Your Name *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="name" type="text" name="name" value="<?php echo isset($name) ? h($name) : ''; ?>" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Your Email *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="email" type="email" name="email" value="<?php echo isset($email) ? h($email) : ''; ?>" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="subject">
                    Subject *
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="subject" type="text" name="subject" value="<?php echo isset($subject) ? h($subject) : ''; ?>" required>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="message">
                    Message *
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                          id="message" name="message" rows="5" required><?php echo isset($message) ? h($message) : ''; ?></textarea>
            </div>
            
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                        type="submit">
                    Send Message
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>