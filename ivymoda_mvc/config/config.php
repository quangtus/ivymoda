<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\config\config.php

// Load environment variables
require_once dirname(__FILE__) . '/../app/helpers/EnvHelper.php';
EnvHelper::load();

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

// MoMo Payment Configuration - Load from .env
if (!defined('MOMO_PARTNER_CODE')) {
    define('MOMO_PARTNER_CODE', EnvHelper::get('DEV_PARTNER_CODE', 'MOMO'));
}
if (!defined('MOMO_ACCESS_KEY')) {
    define('MOMO_ACCESS_KEY', EnvHelper::get('DEV_ACCESS_KEY', 'F8BBA842ECF85'));
}
if (!defined('MOMO_SECRET_KEY')) {
    define('MOMO_SECRET_KEY', EnvHelper::get('DEV_SECRET_KEY', 'K951B6PE1waDMi640xX08PD3vg6EkVlz'));
}
if (!defined('MOMO_ENDPOINT')) {
    define('MOMO_ENDPOINT', EnvHelper::get('DEV_MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'));
}
if (!defined('MOMO_RETURN_URL')) {
    define('MOMO_RETURN_URL', BASE_URL . 'payment/momoReturn');
}
if (!defined('MOMO_NOTIFY_URL')) {
    define('MOMO_NOTIFY_URL', BASE_URL . 'payment/momoNotify');
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