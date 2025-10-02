<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\controllers\frontend\CheckoutController.php

class CheckoutController extends Controller {
    private $productModel;
    private $userModel;
    private $orderModel;
    
    public function __construct() {
        // Khởi tạo model
        $this->productModel = $this->model('ProductModel');
        $this->userModel = $this->model('UserModel');
        $this->orderModel = $this->model('OrderModel');
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để thanh toán';
            $this->redirect('auth/login');
            return;
        }
        
        // Kiểm tra giỏ hàng
        if (empty($_SESSION['cart'])) {
            $_SESSION['error'] = 'Giỏ hàng của bạn đang trống';
            $this->redirect('cart');
            return;
        }
    }
    
    /**
     * Hiển thị trang thanh toán
     */
    public function index() {
        $cartItems = $this->getCartItems();
        $totalAmount = $this->calculateTotalAmount($cartItems);
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        $data = [
            'title' => 'Thanh toán - IVY moda',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'user' => $user,
            'cartCount' => $this->getCartCount()
        ];
        
        $this->view('frontend/checkout/index', $data);
    }
    
    /**
     * Xử lý thanh toán
     */
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('checkout');
            return;
        }
        
        // Validate dữ liệu
        $errors = $this->validateCheckoutData($_POST);
        
        if (!empty($errors)) {
            $_SESSION['checkout_errors'] = $errors;
            $this->redirect('checkout');
            return;
        }
        
        // Lấy thông tin giỏ hàng
        $cartItems = $this->getCartItems();
        $totalAmount = $this->calculateTotalAmount($cartItems);
        
        // Lấy thông tin user
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        // Tạo đơn hàng
        $orderData = [
            'user_id' => $_SESSION['user_id'],
            'session_id' => session_id(),
            'customer_name' => $user['fullname'] ?? $_POST['customer_name'],
            'customer_phone' => $user['phone'] ?? $_POST['customer_phone'],
            'customer_email' => $user['email'],
            'customer_address' => $_POST['customer_address'],
            'order_total' => $totalAmount,
            'order_status' => 0,
            'payment_method' => $_POST['payment_method'],
            'shipping_method' => $_POST['shipping_method'] ?? 'Standard',
            'order_note' => $_POST['notes'] ?? ''
        ];
        
        // Lưu đơn hàng vào database
        $result = $this->orderModel->createOrder($orderData);
        
        if ($result['success']) {
            // Lưu chi tiết đơn hàng
            foreach ($cartItems as $item) {
                $this->orderModel->addOrderItem([
                    'order_id' => $result['order_id'],
                    'sanpham_id' => $item['product_id'],
                    'sanpham_ten' => $item['name'],
                    'sanpham_gia' => $item['price'],
                    'sanpham_soluong' => $item['quantity'],
                    'sanpham_size' => $item['size'] ?? null,
                    'sanpham_color' => $item['color'] ?? null,
                    'sanpham_anh' => $item['image']
                ]);
            }
            
            // Xóa giỏ hàng sau khi thanh toán thành công
            $_SESSION['cart'] = [];
            $this->updateCartCount();
            
            $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng: ' . $result['order_code'];
            $_SESSION['order_code'] = $result['order_code'];
            $this->redirect('checkout/success');
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.';
            $this->redirect('checkout');
        }
    }
    
    /**
     * Trang thanh toán thành công
     */
    public function success() {
        $data = [
            'title' => 'Đặt hàng thành công - IVY moda'
        ];
        
        $this->view('frontend/checkout/success', $data);
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
                    'total' => $product->sanpham_gia * $quantity
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
     * Validate dữ liệu thanh toán
     */
    private function validateCheckoutData($data) {
        $errors = [];
        
        if (empty($data['customer_address'])) {
            $errors['customer_address'] = 'Vui lòng nhập địa chỉ giao hàng đầy đủ';
        }
        
        if (empty($data['payment_method'])) {
            $errors['payment_method'] = 'Vui lòng chọn phương thức thanh toán';
        }
        
        return $errors;
    }
}
