<?php
// filepath: ivymoda_mvc/public/index.php

// Định nghĩa đường dẫn gốc
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Thời gian bắt đầu
$start_time = microtime(true);

// Bật báo lỗi trong môi trường phát triển
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Tạo thư mục logs nếu chưa tồn tại
if(!is_dir(ROOT_PATH . 'logs')) {
    mkdir(ROOT_PATH . 'logs', 0755, true);
}

// Load cấu hình
require_once ROOT_PATH . 'config/config.php';

// Khởi động session
session_start();

// Autoload classes
spl_autoload_register(function($class) {
    // Chuyển đổi namespace thành đường dẫn file
    $class = str_replace('\\', '/', $class);
    
    // Các thư mục cần tìm
    $directories = [
        'app/core/',
        'app/controllers/frontend/',
        'app/controllers/admin/',
        'app/models/',
        'app/helpers/'
    ];
    
    // Tìm file trong các thư mục
    foreach($directories as $directory) {
        $file = ROOT_PATH . $directory . $class . '.php';
        if(file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Load core classes
require_once ROOT_PATH . 'app/core/Controller.php';
require_once ROOT_PATH . 'app/core/Model.php';
require_once ROOT_PATH . 'app/core/App.php';

// Khởi chạy ứng dụng
$app = new App();

// Ghi log thời gian xử lý
$execution_time = microtime(true) - $start_time;
file_put_contents(
    ROOT_PATH . 'logs/performance.log',
    date('Y-m-d H:i:s') . " - Execution time: " . number_format($execution_time * 1000, 2) . "ms - URL: {$_SERVER['REQUEST_URI']}\n",
    FILE_APPEND
);