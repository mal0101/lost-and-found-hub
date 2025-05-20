<?php
// Define root path
define('ROOT_PATH', dirname(dirname(__DIR__)));

// Set page title
$page_title = "Register";

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
$success = "";

// Process registration form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Simple validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email already registered";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            
            try {
                $stmt->execute([$username, $email, $hashed_password]);
                $success = "Registration successful! You can now <a href='".url('pages/auth/login.php')."'>login</a>.";
                // Clear form data on success
                $username = $email = "";
            } catch (PDOException $e) {
                $error = "Registration failed: " . $e->getMessage();
            }
        }
    }
}

// Include header
require_once ROOT_PATH . '/includes/templates/header.php';
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8 mt-8">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create an Account</h2>
    
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p><?php echo $success; ?></p>
        </div>
    <?php else: ?>
        <form action="<?php echo url('pages/auth/register.php'); ?>" method="post">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="username" type="text" name="username" value="<?php echo isset($username) ? h($username) : ''; ?>" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="email" type="email" name="email" value="<?php echo isset($email) ? h($email) : ''; ?>" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="password" type="password" name="password" required>
                <p class="text-sm text-gray-600 mt-1">Must be at least 6 characters</p>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">
                    Confirm Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       id="confirm_password" type="password" name="confirm_password" required>
            </div>
            
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                        type="submit">
                    Register
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="<?php echo url('pages/auth/login.php'); ?>">
                    Already have an account?
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
// Include footer
require_once ROOT_PATH . '/includes/templates/footer.php';
?>