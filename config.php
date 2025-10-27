<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tour');

// Application Configuration
define('APP_NAME', 'Tourismo Zamboanga');
define('APP_VERSION', '1.0.0');

// Timezone
date_default_timezone_set('Asia/Manila');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Helper function to check user role
function hasRole($role) {
    return isset($_SESSION['user']) && $_SESSION['user']['role_name'] === $role;
}

// Helper function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /tour-guide-zamboanga/index.php');
        exit;
    }
}

// Helper function to redirect if not specific role
function requireRole($role) {
    if (!hasRole($role)) {
        header('Location: /tour-guide-zamboanga/index.php');
        exit;
    }
}
