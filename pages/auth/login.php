<?php
// Define root path
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Set page title
$page_title = "Login";

// Include essential files
require_once ROOT_PATH . '/config/db.php';
require_once ROOT_PATH . '/includes/helpers/functions.php';

// Start session
session_start();

// If user is already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$error = "";

// Process login form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, store data in session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Set success message
            set_flash_message('success', 'Welcome back, ' . $user['username'] . '!');
            
            // Redirect to index page
            redirect('index.php');
        } else {
            $error = "Invalid email or password";
        }
    }
}

// Include header
require_once ROOT_PATH . '/includes/templates/header.php';
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8 mt-8">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Log In</h2>
    
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <form action="<?php echo url('pages/auth/login.php'); ?>" method="post">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                Email
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="email" type="email" name="email" value="<?php echo isset($email) ? h($email) : ''; ?>" required>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Password
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                   id="password" type="password" name="password" required>
        </div>
        
        <div class="flex items-center justify-between">
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                    type="submit">
                Log In
            </button>
            <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="<?php echo url('pages/auth/register.php'); ?>">
                Create an account
            </a>
        </div>
    </form>
</div>

<?php
// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>