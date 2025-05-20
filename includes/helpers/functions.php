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
?>