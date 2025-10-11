<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\public\ajax\wishlist_ajax.php

// Định nghĩa đường dẫn gốc
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2) . '/');
}

// Load cấu hình
require_once ROOT_PATH . 'config/config.php';

// Khởi động session
session_start();

// Set header JSON
header('Content-Type: application/json');

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Lấy action
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            addToWishlist();
            break;
        case 'remove':
            removeFromWishlist();
            break;
        case 'list':
            getWishlist();
            break;
        case 'check':
            checkInWishlist();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function addToWishlist() {
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    // Khởi tạo wishlist nếu chưa có
    if (!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    
    // Kiểm tra sản phẩm đã có trong wishlist chưa
    if (in_array($product_id, $_SESSION['wishlist'])) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm đã có trong danh sách yêu thích']);
        return;
    }
    
    // Thêm sản phẩm vào wishlist
    $_SESSION['wishlist'][] = $product_id;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đã thêm vào danh sách yêu thích',
        'wishlist_count' => count($_SESSION['wishlist'])
    ]);
}

function removeFromWishlist() {
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    if (isset($_SESSION['wishlist'])) {
        $key = array_search($product_id, $_SESSION['wishlist']);
        if ($key !== false) {
            unset($_SESSION['wishlist'][$key]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']); // Re-index array
            echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi danh sách yêu thích']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong danh sách yêu thích']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Danh sách yêu thích trống']);
    }
}

function getWishlist() {
    if (!isset($_SESSION['wishlist']) || empty($_SESSION['wishlist'])) {
        echo json_encode(['success' => true, 'items' => []]);
        return;
    }
    
    // Load model để lấy thông tin sản phẩm
    require_once ROOT_PATH . 'app/core/Model.php';
    require_once ROOT_PATH . 'app/models/ProductModel.php';
    
    $productModel = new ProductModel();
    $items = [];
    
    foreach ($_SESSION['wishlist'] as $product_id) {
        $product = $productModel->getProductById($product_id);
        if ($product) {
            $items[] = [
                'product_id' => $product->sanpham_id,
                'name' => $product->sanpham_tieude,
                'price' => $product->sanpham_gia,
                'image' => $product->sanpham_anh,
                'category' => $product->danhmuc_ten ?? 'N/A'
            ];
        }
    }
    
    echo json_encode(['success' => true, 'items' => $items]);
}

function checkInWishlist() {
    $product_id = (int)($_GET['product_id'] ?? 0);
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    $in_wishlist = isset($_SESSION['wishlist']) && in_array($product_id, $_SESSION['wishlist']);
    
    echo json_encode([
        'success' => true, 
        'in_wishlist' => $in_wishlist
    ]);
}
?>
