<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\config\config.php

// Môi trường (development hoặc production)
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}

// Đường dẫn cơ sở
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/ivymoda/ivymoda_mvc/public/');
}
if (!defined('URLROOT')) {
    define('URLROOT', '/ivymoda/ivymoda_mvc/public/');
}
if (!defined('ADMIN_URL')) {
    define('ADMIN_URL', '/ivymoda/ivymoda_mvc/public/admin/');
}

// Đường dẫn gốc
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)) . '/');
}

// Đường dẫn assets
if (!defined('CSS_PATH')) {
    define('CSS_PATH', BASE_URL . 'assets/css/');
}
if (!defined('JS_PATH')) {
    define('JS_PATH', BASE_URL . 'assets/js/');
}
if (!defined('IMAGE_PATH')) {
    define('IMAGE_PATH', BASE_URL . 'assets/images/');
}
if (!defined('UPLOAD_PATH')) {
    define('UPLOAD_PATH', BASE_URL . 'assets/uploads/');
}
if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', BASE_URL . 'assets/');
}

// Cấu hình email
if (!defined('EMAIL_HOST')) {
    define('EMAIL_HOST', 'smtp.gmail.com');
}
if (!defined('EMAIL_PORT')) {
    define('EMAIL_PORT', 587);
}
if (!defined('EMAIL_USERNAME')) {
    define('EMAIL_USERNAME', 'amnesiaism1@gmail.com');
}
if (!defined('EMAIL_PASSWORD')) {
    define('EMAIL_PASSWORD', 'dame fmgx tsrh fmgw');
}
if (!defined('EMAIL_FROM')) {
    define('EMAIL_FROM', 'amnesiaism1@gmail.com');
}
if (!defined('EMAIL_FROM_NAME')) {
    define('EMAIL_FROM_NAME', 'IVY moda');
}

// Cấu hình khác
if (!defined('ITEMS_PER_PAGE')) {
    define('ITEMS_PER_PAGE', 10);
}
if (!defined('MAX_LOGIN_ATTEMPTS')) {
    define('MAX_LOGIN_ATTEMPTS', 5);
}
if (!defined('SESSION_TIMEOUT')) {
    define('SESSION_TIMEOUT', 1800);
}

// Database configuration
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'ivymoda');
}

// Hiển thị lỗi dựa trên môi trường
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}