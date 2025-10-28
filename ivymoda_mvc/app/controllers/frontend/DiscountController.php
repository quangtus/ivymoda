<?php
/**
 * DiscountController - Xử lý mã giảm giá ở frontend
 */

class DiscountController extends Controller {
    private $discountModel;
    private $cartModel;
    
    public function __construct() {
        $this->discountModel = $this->model('DiscountModel');
        $this->cartModel = $this->model('CartModel');
    }
    
    /**
     * Validate mã giảm giá (AJAX endpoint)
     */
    public function validate() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Phương thức không hợp lệ'
            ]);
            return;
        }
        
        $code = trim($_POST['code'] ?? '');
        
        if (empty($code)) {
            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng nhập mã giảm giá'
            ]);
            return;
        }
        
        // Lấy tổng tiền giỏ hàng hiện tại
        $sessionId = session_id();
        $userId = $_SESSION['user_id'] ?? null;
        // Áp dụng theo các item đang được chọn
        $selected = isset($_SESSION['cart_selected']) && is_array($_SESSION['cart_selected']) ? array_map('intval', $_SESSION['cart_selected']) : null;
        $orderTotal = $this->cartModel->getCartTotal($sessionId, $userId, $selected);
        
        if ($orderTotal <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Giỏ hàng trống, không thể áp dụng mã giảm giá'
            ]);
            return;
        }
        
        // Validate mã giảm giá
        $validation = $this->discountModel->validateDiscount($code, $orderTotal);
        
        if (!$validation['valid']) {
            echo json_encode([
                'success' => false,
                'message' => $validation['message']
            ]);
            return;
        }
        
        $discount = $validation['discount'];
        $discountValue = $this->discountModel->calculateDiscountValue($discount, $orderTotal);
        $finalTotal = $orderTotal - $discountValue;
        
        // Lưu mã giảm giá vào session để sử dụng khi checkout
        $_SESSION['applied_discount'] = [
            'code' => $code,
            'name' => $discount->ma_ten,
            'discount_id' => $discount->ma_id,
            'discount_value' => $discountValue,
            'discount_type' => $discount->loai_giam,
            'discount_amount' => $discount->ma_giam,
            'original_total' => $orderTotal,
            'final_total' => $finalTotal
        ];
        
        echo json_encode([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công',
            'discount' => [
                'code' => $code,
                'name' => $discount->ma_ten,
                'type' => $discount->loai_giam,
                'value' => $discount->ma_giam,
                'discount_value' => $discountValue,
                'original_total' => $orderTotal,
                'final_total' => $finalTotal
            ]
        ]);
    }
    
    /**
     * Xóa mã giảm giá đã áp dụng (AJAX endpoint)
     */
    public function remove() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Phương thức không hợp lệ'
            ]);
            return;
        }
        
        unset($_SESSION['applied_discount']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Đã xóa mã giảm giá'
        ]);
    }
    
    /**
     * Lấy thông tin mã giảm giá đã áp dụng (AJAX endpoint)
     */
    public function getApplied() {
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