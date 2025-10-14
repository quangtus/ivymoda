<?php
/**
 * CartController - MIGRATED TO VARIANT SYSTEM
 * 
 * Version 2.0 - Sử dụng CartModel với variant_id
 * Tương thích với database schema mới
 */

class CartController extends Controller {
    private $productModel;
    private $cartModel;
    
    public function __construct() {
        // Khởi tạo models
        $this->productModel = $this->model('ProductModel');
        $this->cartModel = $this->model('CartModel');
    }
    
    /**
     * Hiển thị trang giỏ hàng (MIGRATED TO VARIANT SYSTEM)
     */
    public function index() {
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        
        // Lấy giỏ hàng từ CartModel
        $cartItems = $this->cartModel->getCartItems($sessionId, $userId);
        $totalAmount = $this->cartModel->getCartTotal($sessionId, $userId);
        $cartCount = $this->cartModel->getCartCount($sessionId, $userId);
        
        $data = [
            'title' => 'Giỏ hàng - IVY moda',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'cartCount' => $cartCount
        ];
        
        $this->view('frontend/cart/index', $data);
    }
    
    /**
     * Thêm sản phẩm vào giỏ hàng (MIGRATED TO VARIANT SYSTEM)
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
            return;
        }
        
        $variant_id = (int)($_POST['variant_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if (!$variant_id || $quantity <= 0) {
            $_SESSION['error'] = 'Thông tin sản phẩm không hợp lệ';
            $this->redirect('cart');
            return;
        }
        
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        
        // Thêm vào giỏ hàng sử dụng CartModel
        $result = $this->cartModel->addToCart($sessionId, $userId, $variant_id, $quantity);
        
        if ($result) {
            $_SESSION['success'] = 'Đã thêm sản phẩm vào giỏ hàng';
        } else {
            $_SESSION['error'] = 'Không thể thêm sản phẩm vào giỏ hàng. Vui lòng kiểm tra tồn kho.';
        }
        
        $this->redirect('cart');
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng (MIGRATED TO VARIANT SYSTEM)
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
            return;
        }
        
        $cart_id = (int)($_POST['cart_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        
        if (!$cart_id) {
            $_SESSION['error'] = 'Thông tin sản phẩm không hợp lệ';
            $this->redirect('cart');
            return;
        }
        
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        
        if ($quantity <= 0) {
            // Xóa sản phẩm nếu quantity = 0
            $result = $this->cartModel->removeFromCart($cart_id, $sessionId, $userId);
            if ($result) {
                $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
            } else {
                $_SESSION['error'] = 'Không thể xóa sản phẩm';
            }
        } else {
            // Cập nhật số lượng
            $result = $this->cartModel->updateCartQuantity($cart_id, $quantity, $sessionId, $userId);
            if ($result) {
                $_SESSION['success'] = 'Đã cập nhật số lượng sản phẩm';
            } else {
                $_SESSION['error'] = 'Không thể cập nhật số lượng. Vui lòng kiểm tra tồn kho.';
            }
        }
        
        $this->redirect('cart');
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng (MIGRATED TO VARIANT SYSTEM)
     */
    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
            return;
        }
        
        $cart_id = (int)($_POST['cart_id'] ?? 0);
        
        if (!$cart_id) {
            $_SESSION['error'] = 'Thông tin sản phẩm không hợp lệ';
            $this->redirect('cart');
            return;
        }
        
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        
        // Debug logging
        error_log("CartController::remove - cart_id=$cart_id, sessionId=$sessionId, userId=$userId");
        
        $result = $this->cartModel->removeFromCart($cart_id, $sessionId, $userId);
        
        // Debug logging
        error_log("CartController::remove - result=" . ($result ? 'true' : 'false'));
        
        if ($result) {
            $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
        } else {
            $_SESSION['error'] = 'Không thể xóa sản phẩm khỏi giỏ hàng';
        }
        
        $this->redirect('cart');
    }
    
    /**
     * Xóa tất cả sản phẩm trong giỏ hàng (MIGRATED TO VARIANT SYSTEM)
     */
    public function clear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
            return;
        }
        
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        
        $result = $this->cartModel->clearCart($sessionId, $userId);
        
        if ($result) {
            $_SESSION['success'] = 'Đã xóa tất cả sản phẩm khỏi giỏ hàng';
        } else {
            $_SESSION['error'] = 'Không thể xóa giỏ hàng';
        }
        
        $this->redirect('cart');
    }
    
    /**
     * API endpoint để lấy thông tin giỏ hàng (cho AJAX)
     */
    public function api() {
        header('Content-Type: application/json');
        
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'count':
                $count = $this->cartModel->getCartCount($sessionId, $userId);
                echo json_encode(['success' => true, 'count' => $count]);
                break;
            case 'list':
                $items = $this->cartModel->getCartItems($sessionId, $userId);
                $totalAmount = $this->cartModel->getCartTotal($sessionId, $userId);
                echo json_encode([
                    'success' => true, 
                    'items' => $items,
                    'totalAmount' => $totalAmount,
                    'count' => $this->cartModel->getCartCount($sessionId, $userId)
                ]);
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }
}
