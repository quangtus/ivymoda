<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\public\ajax\cart_ajax.php

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
            addToCart();
            break;
        case 'remove':
            removeFromCart();
            break;
        case 'update':
            updateCart();
            break;
        case 'count':
            getCartCount();
            break;
        case 'list':
            getCartList();
            break;
        case 'clear':
            clearCart();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function addToCart() {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if (!$product_id || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID or quantity']);
        return;
    }
    
    // Khởi tạo giỏ hàng nếu chưa có
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Kiểm tra sản phẩm đã có trong giỏ chưa
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    // Cập nhật số lượng giỏ hàng
    updateCartCount();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đã thêm sản phẩm vào giỏ hàng',
        'cart_count' => array_sum($_SESSION['cart'])
    ]);
}

function removeFromCart() {
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        updateCartCount();
        echo json_encode(['success' => true, 'message' => 'Đã xóa sản phẩm khỏi giỏ hàng']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng']);
    }
}

function updateCart() {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    
    if (!$product_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        return;
    }
    
    if ($quantity <= 0) {
        // Xóa sản phẩm nếu quantity = 0
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    } else {
        // Cập nhật số lượng
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    updateCartCount();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đã cập nhật giỏ hàng',
        'cart_count' => array_sum($_SESSION['cart'])
    ]);
}

function getCartCount() {
    $count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    echo json_encode(['success' => true, 'count' => $count]);
}

function getCartList() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['success' => true, 'items' => []]);
        return;
    }
    
    // Load model để lấy thông tin sản phẩm
    require_once ROOT_PATH . 'app/core/Model.php';
    require_once ROOT_PATH . 'app/models/ProductModel.php';
    
    $productModel = new ProductModel();
    $items = [];
    
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = $productModel->getProductById($product_id);
        if ($product) {
            $items[] = [
                'product_id' => $product->sanpham_id,
                'name' => $product->sanpham_tieude,
                'price' => $product->sanpham_gia,
                'image' => $product->sanpham_anh,
                'quantity' => $quantity,
                'total' => $product->sanpham_gia * $quantity
            ];
        }
    }
    
    echo json_encode(['success' => true, 'items' => $items]);
}

function clearCart() {
    $_SESSION['cart'] = [];
    updateCartCount();
    echo json_encode(['success' => true, 'message' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng']);
}

function updateCartCount() {
    $_SESSION['cart_count'] = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}
?>
