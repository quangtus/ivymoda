<?php
/**
 * PaymentController - Xử lý thanh toán
 */

class PaymentController extends Controller {
    private $momoPaymentModel;
    private $vnpayPaymentModel;
    private $orderModel;
    private $cartModel;
    
    public function __construct() {
        $this->momoPaymentModel = $this->model('MomoPaymentModel');
        $this->vnpayPaymentModel = $this->model('VnpayPaymentModel');
        $this->orderModel = $this->model('OrderModel');
        $this->cartModel = $this->model('CartModel');
    }
    
    /**
     * Tạo thanh toán Momo
     */
    public function momo() {
        // Cho phép cả POST (từ form) và GET (từ redirect nút thanh toán)
        $orderId = $_REQUEST['order_id'] ?? '';
        $orderCode = $_REQUEST['order_code'] ?? '';
        $amount = (int)($_REQUEST['amount'] ?? 0);
        
        // Nếu không có thông tin trong URL, lấy từ session
        if (!$orderId || !$orderCode || $amount <= 0) {
            if (isset($_SESSION['momo_order_info'])) {
                $momoInfo = $_SESSION['momo_order_info'];
                $orderId = $momoInfo['order_id'];
                $orderCode = $momoInfo['order_code'];
                $amount = $momoInfo['amount'];
            }
        }
        
        if (!$orderId || !$orderCode || $amount <= 0) {
            // Debug logging
            error_log("PaymentController::momo - Missing order info: orderId=$orderId, orderCode=$orderCode, amount=$amount");
            error_log("PaymentController::momo - Session data: " . print_r($_SESSION, true));
            
            $_SESSION['error'] = 'Thông tin thanh toán không hợp lệ';
            $this->redirect('checkout');
            return;
        }
        
        // Debug logging
        error_log("PaymentController::momo - Processing payment: orderId=$orderId, orderCode=$orderCode, amount=$amount");
        
        // Tạo payment request
        $orderData = [
            'order_code' => $orderCode,
            'order_total' => $amount
        ];
        
        $paymentResult = $this->momoPaymentModel->createPaymentRequest($orderData);
        
        if ($paymentResult['success']) {
            // Lưu payment log
            $this->momoPaymentModel->logPayment($orderId, $paymentResult['requestId'], $amount, 'pending', null, $orderCode);
            
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
     * Xử lý IPN từ Momo - Redirect đến file handler
     */
    public function momoNotify() {
        // Redirect đến file handler để tránh xung đột
        header('Location: ' . BASE_URL . 'payment/momoNotify.php');
        exit;
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
    
    /**
     * Tạo thanh toán VNPay
     */
    public function vnpay() {
        // Cho phép cả POST (từ form) và GET (từ redirect nút thanh toán)
        $orderId = $_REQUEST['order_id'] ?? '';
        $orderCode = $_REQUEST['order_code'] ?? '';
        $amount = (int)($_REQUEST['amount'] ?? 0);
        
        if (!$orderId || !$orderCode || $amount <= 0) {
            $_SESSION['error'] = 'Thông tin thanh toán không hợp lệ';
            $this->redirect('checkout');
            return;
        }
        
        // Kiểm tra đơn hàng có tồn tại không
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng';
            $this->redirect('checkout');
            return;
        }
        
        // Tạo payment request
        $orderData = [
            'order_code' => $orderCode,
            'order_total' => $amount
        ];
        
        $result = $this->vnpayPaymentModel->createPaymentRequest($orderData);
        
        if ($result['success']) {
            // Log payment request
            $this->vnpayPaymentModel->logPayment(
                $orderCode,
                $amount,
                'pending',
                $result
            );
            
            // Redirect đến VNPay
            header('Location: ' . $result['payment_url']);
            exit;
        } else {
            $_SESSION['error'] = 'Lỗi tạo thanh toán VNPay: ' . $result['message'];
            $this->redirect('checkout');
        }
    }
    
    /**
     * Xử lý VNPay return
     */
    public function vnpayReturn() {
        // Redirect đến return handler
        header('Location: ' . BASE_URL . 'payment/vnpay_return.php?' . http_build_query($_GET));
        exit;
    }
    
    /**
     * Xử lý VNPay notify (IPN)
     */
    public function vnpayNotify() {
        // Redirect đến notify handler
        header('Location: ' . BASE_URL . 'payment/vnpay_notify.php');
        exit;
    }
}
