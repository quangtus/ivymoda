<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\includes\config.php

// Định nghĩa các constants nếu chưa được định nghĩa

// Database configuration
if (!defined('DB_HOST'))     define('DB_HOST', 'localhost');
if (!defined('DB_USER'))     define('DB_USER', 'root');
if (!defined('DB_PASS'))     define('DB_PASS', '');
if (!defined('DB_NAME'))     define('DB_NAME', 'ivymoda');
if (!defined('DB_CHARSET'))  define('DB_CHARSET', 'utf8mb4');

// Application configuration
if (!defined('ENVIRONMENT')) define('ENVIRONMENT', 'development');

// URL configuration
if (!defined('BASE_URL'))    define('BASE_URL', '/ivymoda/ivymoda_mvc/');
if (!defined('ADMIN_URL'))   define('ADMIN_URL', BASE_URL . 'admin/');
if (!defined('PUBLIC_URL'))  define('PUBLIC_URL', BASE_URL . 'public/');
if (!defined('ASSETS_URL'))  define('ASSETS_URL', PUBLIC_URL . 'assets/');

// Email configuration
if (!defined('EMAIL_HOST'))       define('EMAIL_HOST', 'smtp.gmail.com');
if (!defined('EMAIL_USERNAME'))   define('EMAIL_USERNAME', 'your_email@gmail.com');
if (!defined('EMAIL_PASSWORD'))   define('EMAIL_PASSWORD', 'your_password');
if (!defined('EMAIL_PORT'))       define('EMAIL_PORT', 587);
if (!defined('EMAIL_FROM'))       define('EMAIL_FROM', 'noreply@ivymoda.com');
if (!defined('EMAIL_FROM_NAME'))  define('EMAIL_FROM_NAME', 'IVY moda');

// Application settings
if (!defined('ITEMS_PER_PAGE'))     define('ITEMS_PER_PAGE', 12);
if (!defined('MAX_LOGIN_ATTEMPTS')) define('MAX_LOGIN_ATTEMPTS', 5);
if (!defined('URLROOT')) define('URLROOT', BASE_URL);

return [
    'database' => [
        'host' => DB_HOST,
        'username' => DB_USER,
        'password' => DB_PASS,
        'db_name' => DB_NAME,
        'charset' => DB_CHARSET
    ],
    'email' => [
        'from' => EMAIL_FROM,
        'from_name' => EMAIL_FROM_NAME,
        'host' => EMAIL_HOST,
        'username' => EMAIL_USERNAME,
        'password' => EMAIL_PASSWORD,
        'port' => EMAIL_PORT
    ],
    'site' => [
        'name' => 'Ivymoda',
        'url' => BASE_URL,
        'admin_url' => ADMIN_URL,
        'public_url' => PUBLIC_URL,
        'assets_url' => ASSETS_URL
    ],
    'paths' => [
        'base' => BASE_URL,
        'public' => PUBLIC_URL,
        'assets' => ASSETS_URL
    ]
];