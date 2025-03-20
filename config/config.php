<?php
// Application configuration
define('APP_NAME', 'SportsVani');
define('APP_URL', 'https://sportsvani.in');
define('APP_VERSION', '1.0.0');

// Path definitions
define('ROOT_PATH', dirname(__DIR__));
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('LOGS_PATH', ROOT_PATH . '/logs');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', LOGS_PATH . '/error.log');

// Include database configuration
require_once 'database.php';

// Firebase configuration
define('FIREBASE_API_KEY', $_ENV['FIREBASE_API_KEY'] ?? '');
define('FIREBASE_AUTH_DOMAIN', $_ENV['FIREBASE_AUTH_DOMAIN'] ?? '');
define('FIREBASE_PROJECT_ID', $_ENV['FIREBASE_PROJECT_ID'] ?? '');
define('FIREBASE_STORAGE_BUCKET', $_ENV['FIREBASE_STORAGE_BUCKET'] ?? '');
define('FIREBASE_MESSAGING_SENDER_ID', $_ENV['FIREBASE_MESSAGING_SENDER_ID'] ?? '');
define('FIREBASE_APP_ID', $_ENV['FIREBASE_APP_ID'] ?? '');

// YouTube API configuration
define('YOUTUBE_API_KEY', $_ENV['YOUTUBE_API_KEY'] ?? '');

// Super Admin credentials
define('SUPER_ADMIN_EMAIL', 'sportavani@gmail.com');
define('SUPER_ADMIN_PASSWORD', '12345678');

// Helper functions
function redirect($url) {
    header("Location: $url");
    exit;
}

function generateUniqueId($prefix = '') {
    return uniqid($prefix) . bin2hex(random_bytes(4));
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isSuperAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'superadmin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

function requireSuperAdmin() {
    if (!isLoggedIn() || !isSuperAdmin()) {
        redirect('/superadmin/login.php');
    }
}

function getUploadedFilePath($file, $directory) {
    if (!file_exists(UPLOADS_PATH . '/' . $directory)) {
        mkdir(UPLOADS_PATH . '/' . $directory, 0777, true);
    }
    
    $filename = time() . '_' . basename($file['name']);
    $target_path = UPLOADS_PATH . '/' . $directory . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return '/uploads/' . $directory . '/' . $filename;
    }
    
    return null;
}

function jsonResponse($data, $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

