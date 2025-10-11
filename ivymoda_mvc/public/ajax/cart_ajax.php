<?php
/**
 * Cart AJAX Endpoint - MIGRATED TO VARIANT SYSTEM
 * 
 * Version 2.0 - Sử dụng variant_id thay vì product_id + size + color
 * 
 * Actions:
 * - add: Thêm variant vào giỏ
 * - remove: Xóa item khỏi giỏ
 * - update: Cập nhật số lượng
 * - count: Đếm số items
 * - list: Lấy danh sách giỏ hàng
 * - clear: Xóa toàn bộ giỏ
 */

// Định nghĩa đường dẫn gốc
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2) . '/');
}

// Load dependencies
require_once ROOT_PATH . 'config/config.php';
require_once ROOT_PATH . 'app/core/Database.php';
require_once ROOT_PATH . 'app/core/Model.php';
require_once ROOT_PATH . 'app/models/CartModel.php';
require_once ROOT_PATH . 'app/models/ProductModel.php';

// Khởi động session
session_start();

// Set header JSON
header('Content-Type: application/json; charset=utf-8');

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Initialize models
$db = new Database();
$cartModel = new CartModel($db);
$productModel = new ProductModel($db);

// Lấy session_id và user_id
$sessionId = session_id();
$userId = $_SESSION['user_id'] ?? null;

// Lấy action
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            addToCart($cartModel, $productModel, $sessionId, $userId);
            break;
        case 'remove':
            removeFromCart($cartModel);
            break;
        case 'update':
            updateCart($cartModel);
            break;
        case 'count':
            getCartCount($cartModel, $sessionId, $userId);
            break;
        case 'list':
            getCartList($cartModel, $sessionId, $userId);
            break;
        case 'clear':
            clearCart($cartModel, $sessionId, $userId);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

// ============================================
// FUNCTIONS - MIGRATED TO VARIANT SYSTEM
// ============================================

/**
 * Thêm variant vào giỏ hàng
 * 
 * POST params:
 * - variant_id (required): ID của variant
 * - quantity (optional): Số lượng (default: 1)
 */
function addToCart($cartModel, $productModel, $sessionId, $userId) {
    $variantId = (int)($_POST['variant_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if (!$variantId || $quantity <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu variant_id hoặc quantity không hợp lệ'
        ]);
        return;
    }
    
    // Thêm vào giỏ (validate tồn kho trong CartModel)
    $result = $cartModel->addToCart($sessionId, $userId, $variantId, $quantity);
    
    if ($result) {
        // Lấy thông tin variant vừa thêm
        $cartItems = $cartModel->getCartItems($sessionId, $userId);
        $addedItem = null;
        foreach ($cartItems as $item) {
            if ($item->variant_id == $variantId) {
                $addedItem = $item;
                break;
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'cart_count' => $cartModel->getCartCount($sessionId, $userId),
            'cart_total' => number_format($cartModel->getCartTotal($sessionId, $userId), 0, ',', '.'),
            'item' => $addedItem ? [
                'product_name' => $addedItem->sanpham_tieude,
                'color' => $addedItem->color_ten,
                'size' => $addedItem->size_ten,
                'quantity' => $addedItem->quantity,
                'price' => number_format($addedItem->gia_hien_tai, 0, ',', '.')
            ] : null
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Không thể thêm vào giỏ hàng. Vui lòng kiểm tra tồn kho.'
        ]);
    }
}

/**
 * Xóa item khỏi giỏ hàng
 * 
 * POST params:
 * - cart_id (required): ID của cart item
 */
function removeFromCart($cartModel) {
    $cartId = (int)($_POST['cart_id'] ?? 0);
    
    if (!$cartId) {
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu cart_id'
        ]);
        return;
    }
    
    $result = $cartModel->removeItem($cartId);
    
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Đã xóa sản phẩm khỏi giỏ hàng' : 'Không thể xóa sản phẩm'
    ]);
}

/**
 * Cập nhật số lượng item trong giỏ
 * 
 * POST params:
 * - cart_id (required): ID của cart item
 * - quantity (required): Số lượng mới
 */
function updateCart($cartModel) {
    $cartId = (int)($_POST['cart_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 0);
    
    if (!$cartId) {
        echo json_encode([
            'success' => false,
            'message' => 'Thiếu cart_id'
        ]);
        return;
    }
    
    $result = $cartModel->updateQuantity($cartId, $quantity);
    
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Đã cập nhật giỏ hàng' : 'Không thể cập nhật. Vui lòng kiểm tra tồn kho.'
    ]);
}

/**
 * Lấy số lượng items trong giỏ
 */
function getCartCount($cartModel, $sessionId, $userId) {
    $count = $cartModel->getCartCount($sessionId, $userId);
    echo json_encode([
        'success' => true,
        'count' => $count
    ]);
}

/**
 * Lấy danh sách items trong giỏ hàng (đầy đủ thông tin)
 */
function getCartList($cartModel, $sessionId, $userId) {
    $cartItems = $cartModel->getCartItems($sessionId, $userId);
    
    $items = [];
    foreach ($cartItems as $item) {
        $items[] = [
            'cart_id' => $item->cart_id,
            'variant_id' => $item->variant_id,
            'sku' => $item->sku,
            'product_id' => $item->sanpham_id,
            'product_name' => $item->sanpham_tieude,
            'product_image' => $item->sanpham_anh,
            'color_name' => $item->color_ten,
            'color_code' => $item->color_ma,
            'size_name' => $item->size_ten,
            'price' => (float)$item->gia_hien_tai,
            'quantity' => (int)$item->quantity,
            'stock' => (int)$item->ton_kho,
            'is_valid' => (bool)$item->is_valid,
            'subtotal' => (float)($item->gia_hien_tai * $item->quantity)
        ];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $cartModel->getCartTotal($sessionId, $userId),
        'count' => $cartModel->getCartCount($sessionId, $userId)
    ]);
}

/**
 * Xóa toàn bộ giỏ hàng
 */
function clearCart($cartModel, $sessionId, $userId) {
    $result = $cartModel->clearCart($sessionId, $userId);
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Đã xóa giỏ hàng' : 'Không thể xóa giỏ hàng'
    ]);
}

