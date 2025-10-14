<?php
/**
 * CheckoutController - MIGRATED TO VARIANT SYSTEM
 * 
 * Version 2.0 - Sử dụng CartModel với variant_id
 * 
 * Workflow:
 * 1. Validate cart với CartModel::validateCartForCheckout()
 * 2. Create order
 * 3. Add order items với variant_id + snapshot
 * 4. Decrease stock với ProductModel::decreaseVariantStock()
 * 5. Clear cart
 */

class CheckoutController extends Controller {
    private $productModel;
    private $userModel;
    private $orderModel;
    private $cartModel;
    
    public function __construct() {
        // Khởi tạo models
        $this->productModel = $this->model('ProductModel');
        $this->userModel = $this->model('UserModel');
        $this->orderModel = $this->model('OrderModel');
        $this->cartModel = $this->model('CartModel');
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để thanh toán';
            $this->redirect('auth/login');
            return;
        }
        
        // Kiểm tra giỏ hàng (dùng CartModel)
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        $cartCount = $this->cartModel->getCartCount($sessionId, $userId);
        
        if ($cartCount == 0) {
            $_SESSION['error'] = 'Giỏ hàng của bạn đang trống';
            $this->redirect('cart');
            return;
        }
    }
    
    /**
     * Hiển thị trang thanh toán
     */
    public function index() {
        $sessionId = session_id();
        $userId = $_SESSION['user_id'];
        
        // Lấy giỏ hàng từ CartModel
        $cartItems = $this->cartModel->getCartItems($sessionId, $userId);
        $totalAmount = $this->cartModel->getCartTotal($sessionId, $userId);
        $user = $this->userModel->getUserById($userId);
        
        $data = [
            'title' => 'Thanh toán - IVY moda',
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'user' => $user,
            'cartCount' => $this->cartModel->getCartCount($sessionId, $userId)
        ];
        
        $this->view('frontend/checkout/index', $data);
    }
    
    /**
     * Xử lý thanh toán (MIGRATED TO VARIANT SYSTEM)
     */
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('checkout');
            return;
        }
        
        // Validate dữ liệu form
        $errors = $this->validateCheckoutData($_POST);
        
        if (!empty($errors)) {
            $_SESSION['checkout_errors'] = $errors;
            $this->redirect('checkout');
            return;
        }
        
        // Lấy session và user info
        $sessionId = session_id();
        $userId = $_SESSION['user_id'];
        
        // *** BƯỚC QUAN TRỌNG: Validate giỏ hàng trước khi checkout ***
        $cartValidation = $this->cartModel->validateCartForCheckout($sessionId, $userId);
        
        if (!$cartValidation['valid']) {
            $_SESSION['checkout_errors'] = $cartValidation['errors'];
            $_SESSION['error'] = "Không thể thanh toán:\n" . implode("\n", $cartValidation['errors']);
            $this->redirect('checkout');
            return;
        }
        
        $cartItems = $cartValidation['items'];
        $totalAmount = $this->cartModel->getCartTotal($sessionId, $userId);
        
        // Lấy thông tin user
        $user = $this->userModel->getUserById($userId);
        
        // Tạo đơn hàng
        $orderData = [
            'user_id' => $userId,
            'session_id' => $sessionId,
            'customer_name' => $user->fullname ?? $_POST['customer_name'],
            'customer_phone' => $user->phone ?? $_POST['customer_phone'],
            'customer_email' => $user->email,
            'customer_address' => $_POST['customer_address'],
            'order_total' => $totalAmount,
            'order_status' => 0,
            'payment_method' => $_POST['payment_method'],
            'shipping_method' => $_POST['shipping_method'] ?? 'Standard',
            'order_note' => $_POST['notes'] ?? ''
        ];
        
        // BEGIN TRANSACTION (quan trọng để đảm bảo data consistency)
        try {
            // Lưu đơn hàng vào database
            $result = $this->orderModel->createOrder($orderData);
            
            if (!$result['success']) {
                throw new Exception('Không thể tạo đơn hàng');
            }
            
            $orderId = $result['order_id'];
            
            // Lưu chi tiết đơn hàng + Trừ tồn kho
            foreach ($cartItems as $item) {
                // Kiểm tra lại tồn kho (double-check)
                if (!$this->productModel->checkVariantStock($item->variant_id)) {
                    throw new Exception("Sản phẩm '{$item->sanpham_tieude}' ({$item->color_ten}, {$item->size_ten}) đã hết hàng");
                }
                
                // Thêm order item với variant_id + snapshot
                $addItemResult = $this->orderModel->addOrderItem([
                    'order_id' => $orderId,
                    'variant_id' => $item->variant_id,
                    'sanpham_ten' => $item->sanpham_tieude,
                    'sanpham_gia' => $item->gia_hien_tai,
                    'sanpham_soluong' => $item->quantity,
                    'sanpham_size' => $item->size_ten,
                    'sanpham_color' => $item->color_ten,
                    'sanpham_anh' => $item->sanpham_anh
                ]);
                
                if (!$addItemResult) {
                    throw new Exception("Không thể thêm sản phẩm vào đơn hàng");
                }
                
                // Trừ tồn kho
                $decreaseResult = $this->productModel->decreaseVariantStock($item->variant_id, $item->quantity);
                
                if (!$decreaseResult) {
                    throw new Exception("Không thể cập nhật tồn kho cho '{$item->sanpham_tieude}'");
                }
            }
            
            // Xử lý thanh toán
            $paymentMethod = $_POST['payment_method'];
            
            if ($paymentMethod === 'momo') {
                // Redirect đến thanh toán Momo
                $this->redirect('payment/momo', [
                    'order_id' => $orderId,
                    'order_code' => $result['order_code'],
                    'amount' => $totalAmount
                ]);
            } else {
                // COD - Xóa giỏ hàng và redirect đến success
                $this->cartModel->clearCart($sessionId, $userId);
                
                // Lưu thông báo thành công
                $_SESSION['success'] = 'Đặt hàng thành công! Mã đơn hàng: ' . $result['order_code'];
                $_SESSION['order_code'] = $result['order_code'];
                
                $this->redirect('checkout/success');
            }
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi (TODO: implement transaction trong Database class)
            error_log("Checkout error: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
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
