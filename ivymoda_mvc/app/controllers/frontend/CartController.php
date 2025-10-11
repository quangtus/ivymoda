<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\controllers\frontend\CartController.php

class CartController extends Controller {
    private $productModel;
    
    public function __construct() {
        // Khởi tạo model
        $this->productModel = $this->model('ProductModel');
        
        // Khởi tạo giỏ hàng nếu chưa có
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    /**
     * Hiển thị trang giỏ hàng
     */
    public function index() {
        $cartItems = $this->getCartItems();
        $totalAmount = $this->calculateTotalAmount($cartItems);
        
        $data = [
            'title' => 'Giỏ hàng - IVY moda',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'cartCount' => $this->getCartCount()
        ];
        
        $this->view('frontend/cart/index', $data);
    }
    
    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
            return;
        }
        
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if (!$product_id || $quantity <= 0) {
            $_SESSION['error'] = 'Thông tin sản phẩm không hợp lệ';
            $this->redirect('cart');
            return;
        }
        
        // Kiểm tra sản phẩm có tồn tại không
        $product = $this->productModel->getProductById($product_id);
        if (!$product) {
            $_SESSION['error'] = 'Sản phẩm không tồn tại';
            $this->redirect('cart');
            return;
        }
        
        // Thêm vào giỏ hàng
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        
        $this->updateCartCount();
        
        $_SESSION['success'] = 'Đã thêm sản phẩm vào giỏ hàng';
        $this->redirect('cart');
    }
    
    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
            return;
        }
        
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        
        if (!$product_id) {
            $_SESSION['error'] = 'Thông tin sản phẩm không hợp lệ';
            $this->redirect('cart');
            return;
        }
        
        if ($quantity <= 0) {
            // Xóa sản phẩm nếu quantity = 0
            if (isset($_SESSION['cart'][$product_id])) {
                unset($_SESSION['cart'][$product_id]);
                $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
            }
        } else {
            // Cập nhật số lượng
            $_SESSION['cart'][$product_id] = $quantity;
            $_SESSION['success'] = 'Đã cập nhật số lượng sản phẩm';
        }
        
        $this->updateCartCount();
        $this->redirect('cart');
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
            return;
        }
        
        $product_id = (int)($_POST['product_id'] ?? 0);
        
        if (!$product_id) {
            $_SESSION['error'] = 'Thông tin sản phẩm không hợp lệ';
            $this->redirect('cart');
            return;
        }
        
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $this->updateCartCount();
            $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
        } else {
            $_SESSION['error'] = 'Sản phẩm không có trong giỏ hàng';
        }
        
        $this->redirect('cart');
    }
    
    /**
     * Xóa tất cả sản phẩm trong giỏ hàng
     */
    public function clear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('cart');
            return;
        }
        
        $_SESSION['cart'] = [];
        $this->updateCartCount();
        $_SESSION['success'] = 'Đã xóa tất cả sản phẩm khỏi giỏ hàng';
        
        $this->redirect('cart');
    }
    
    /**
     * Lấy danh sách sản phẩm trong giỏ hàng với thông tin chi tiết
     */
    private function getCartItems() {
        if (empty($_SESSION['cart'])) {
            return [];
        }
        
        $items = [];
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = $this->productModel->getProductById($product_id);
            if ($product) {
                $items[] = [
                    'product_id' => $product->sanpham_id,
                    'name' => $product->sanpham_tieude,
                    'price' => $product->sanpham_gia,
                    'image' => $product->sanpham_anh,
                    'quantity' => $quantity,
                    'total' => $product->sanpham_gia * $quantity,
                    'description' => $product->sanpham_mota ?? '',
                    'category' => $product->danhmuc_ten ?? ''
                ];
            }
        }
        
        return $items;
    }
    
    /**
     * Tính tổng tiền trong giỏ hàng
     */
    private function calculateTotalAmount($cartItems) {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['total'];
        }
        return $total;
    }
    
    /**
     * Lấy số lượng sản phẩm trong giỏ hàng
     */
    private function getCartCount() {
        return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    }
    
    /**
     * Cập nhật số lượng giỏ hàng trong session
     */
    private function updateCartCount() {
        $_SESSION['cart_count'] = $this->getCartCount();
    }
    
    /**
     * API endpoint để lấy thông tin giỏ hàng (cho AJAX)
     */
    public function api() {
        header('Content-Type: application/json');
        
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'count':
                echo json_encode(['success' => true, 'count' => $this->getCartCount()]);
                break;
            case 'list':
                $items = $this->getCartItems();
                $totalAmount = $this->calculateTotalAmount($items);
                echo json_encode([
                    'success' => true, 
                    'items' => $items,
                    'totalAmount' => $totalAmount,
                    'count' => $this->getCartCount()
                ]);
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    }
}
