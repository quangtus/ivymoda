<?php
/**
 * PaymentController - Xử lý thanh toán
 */

class PaymentController extends Controller {
    private $momoPaymentModel;
    private $orderModel;
    private $cartModel;
    
    public function __construct() {
        $this->momoPaymentModel = $this->model('MomoPaymentModel');
        $this->orderModel = $this->model('OrderModel');
        $this->cartModel = $this->model('CartModel');
    }
    
    /**
     * Tạo thanh toán Momo
     */
    public function momo() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('checkout');
            return;
        }
        
        $orderId = $_POST['order_id'] ?? '';
        $orderCode = $_POST['order_code'] ?? '';
        $amount = (int)($_POST['amount'] ?? 0);
        
        if (!$orderId || !$orderCode || $amount <= 0) {
            $_SESSION['error'] = 'Thông tin thanh toán không hợp lệ';
            $this->redirect('checkout');
            return;
        }
        
        // Tạo payment request
        $orderData = [
            'order_code' => $orderCode,
            'order_total' => $amount
        ];
        
        $paymentResult = $this->momoPaymentModel->createPaymentRequest($orderData);
        
        if ($paymentResult['success']) {
            // Lưu payment log
            $this->momoPaymentModel->logPayment($orderId, $paymentResult['requestId'], $amount, 'pending');
            
            // Redirect đến Momo
            header('Location: ' . $paymentResult['payUrl']);
            exit;
        } else {
            $_SESSION['error'] = $paymentResult['message'];
            $this->redirect('checkout');
        }
    }
    
    /**
     * Xử lý return từ Momo
     */
    public function momoReturn() {
        $orderId = $_GET['orderId'] ?? '';
        $resultCode = $_GET['resultCode'] ?? '';
        $message = $_GET['message'] ?? '';
        
        if ($resultCode == '0') {
            // Thanh toán thành công
            $_SESSION['success'] = 'Thanh toán thành công!';
            $this->redirect('checkout/success');
        } else {
            // Thanh toán thất bại
            $_SESSION['error'] = 'Thanh toán thất bại: ' . $message;
            $this->redirect('checkout');
        }
    }
    
    /**
     * Xử lý IPN từ Momo
     */
    public function momoNotify() {
        // Lấy dữ liệu từ POST
        $orderId = $_POST['orderId'] ?? '';
        $requestId = $_POST['requestId'] ?? '';
        $amount = (int)($_POST['amount'] ?? 0);
        $orderInfo = $_POST['orderInfo'] ?? '';
        $orderType = $_POST['orderType'] ?? '';
        $transId = $_POST['transId'] ?? '';
        $resultCode = (int)($_POST['resultCode'] ?? -1);
        $message = $_POST['message'] ?? '';
        $payType = $_POST['payType'] ?? '';
        $responseTime = $_POST['responseTime'] ?? '';
        $extraData = $_POST['extraData'] ?? '';
        $signature = $_POST['signature'] ?? '';
        
        // Verify payment
        $verification = $this->momoPaymentModel->verifyPayment(
            $orderId, $requestId, $amount, $orderInfo, $orderType, 
            $transId, $resultCode, $message, $payType, $responseTime, 
            $extraData, $signature
        );
        
        if ($verification['valid'] && $verification['success']) {
            // Cập nhật trạng thái đơn hàng
            $this->orderModel->updateOrderStatus($orderId, 1); // Đang giao
            
            // Log payment success
            $this->momoPaymentModel->logPayment($orderId, $requestId, $amount, 'success', $_POST);
            
            // Xóa giỏ hàng
            $sessionId = session_id();
            $userId = $_SESSION['user_id'] ?? null;
            $this->cartModel->clearCart($sessionId, $userId);
            
            echo json_encode(['status' => 'success']);
        } else {
            // Log payment failure
            $this->momoPaymentModel->logPayment($orderId, $requestId, $amount, 'failed', $_POST);
            
            echo json_encode(['status' => 'failed', 'message' => $verification['message']]);
        }
    }
    
    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkStatus() {
        header('Content-Type: application/json');
        
        $orderId = $_GET['order_id'] ?? '';
        
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Missing order ID']);
            return;
        }
        
        $order = $this->orderModel->getOrderById($orderId);
        
        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'order_status' => $order['order_status'],
            'order_code' => $order['order_code']
        ]);
    }
}
