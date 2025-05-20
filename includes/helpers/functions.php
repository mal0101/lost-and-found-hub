<?php
/**
 * Common utility functions for the application
 */

/**
 * Generate a URL relative to the project root
 */
function url($path) {
    return '/' . ltrim($path, '/');
}

/**
 * Redirect to another page
 */
function redirect($path) {
    header("Location: " . url($path));
    exit;
}

/**
 * Sanitize output
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Require user to be logged in
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('pages/auth/login.php');
    }
}

/**
 * Get current user ID
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Flash message handling
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}
?>