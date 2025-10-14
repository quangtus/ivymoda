<?php
/**
 * DiscountController - Xử lý mã giảm giá
 */

class DiscountController extends Controller {
    private $discountModel;
    private $cartModel;
    
    public function __construct() {
        $this->discountModel = $this->model('DiscountModel');
        $this->cartModel = $this->model('CartModel');
    }
    
    /**
     * Validate mã giảm giá (AJAX)
     */
    public function validate() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $code = trim($_POST['code'] ?? '');
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá']);
            return;
        }
        
        // Lấy tổng tiền giỏ hàng
        $cartTotal = $this->cartModel->getCartTotal($sessionId, $userId);
        
        if ($cartTotal <= 0) {
            echo json_encode(['success' => false, 'message' => 'Giỏ hàng trống']);
            return;
        }
        
        // Validate mã giảm giá
        $validation = $this->discountModel->validateDiscount($code, $cartTotal);
        
        if (!$validation['valid']) {
            echo json_encode(['success' => false, 'message' => $validation['message']]);
            return;
        }
        
        $discount = $validation['discount'];
        $discountValue = $this->discountModel->calculateDiscountValue($discount, $cartTotal);
        $finalTotal = $cartTotal - $discountValue;
        
        // Lưu mã giảm giá vào session
        $_SESSION['applied_discount'] = [
            'code' => $code,
            'discount_id' => $discount->ma_id,
            'discount_value' => $discountValue,
            'discount_type' => $discount->ma_loai,
            'original_total' => $cartTotal,
            'final_total' => $finalTotal
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount' => [
                'code' => $code,
                'value' => $discountValue,
                'type' => $discount->ma_loai == 1 ? 'percent' : 'fixed',
                'original_total' => $cartTotal,
                'final_total' => $finalTotal
            ]
        ]);
    }
    
    /**
     * Xóa mã giảm giá (AJAX)
     */
    public function remove() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        // Xóa mã giảm giá khỏi session
        unset($_SESSION['applied_discount']);
        
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        $cartTotal = $this->cartModel->getCartTotal($sessionId, $userId);
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá',
            'cart_total' => $cartTotal
        ]);
    }
    
    /**
     * Lấy thông tin mã giảm giá hiện tại (AJAX)
     */
    public function getCurrent() {
        header('Content-Type: application/json');
        
        if (isset($_SESSION['applied_discount'])) {
            echo json_encode([
                'success' => true,
                'discount' => $_SESSION['applied_discount']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Chưa có mã giảm giá nào được áp dụng'
            ]);
        }
    }
}
