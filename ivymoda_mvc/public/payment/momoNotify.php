<?php
/**
 * MoMo Payment Notify Handler (IPN)
 * Xử lý thông báo từ MoMo về kết quả thanh toán
 */

// Load configuration
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/app/core/App.php';

// Load models
require_once dirname(__DIR__, 2) . '/app/models/MomoPaymentModel.php';
require_once dirname(__DIR__, 2) . '/app/models/OrderModel.php';
require_once dirname(__DIR__, 2) . '/app/models/CartModel.php';
require_once dirname(__DIR__, 2) . '/app/core/Database.php';

// Start session
session_start();

// Get POST data from MoMo
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

// Log the notification for debugging
error_log("MoMo Notify - orderId: $orderId, resultCode: $resultCode, message: $message");

try {
    // Initialize models
    $momoModel = new MomoPaymentModel();
    $orderModel = new OrderModel();
    $cartModel = new CartModel();
    
    // Verify payment
    $verification = $momoModel->verifyPayment(
        $orderId, $requestId, $amount, $orderInfo, $orderType, 
        $transId, $resultCode, $message, $payType, $responseTime, 
        $extraData, $signature
    );
    
    if ($verification['valid'] && $verification['success']) {
        // Thanh toán thành công - Cập nhật trạng thái đơn hàng
        $order = $orderModel->getOrderByCode($orderId);
        
        if ($order) {
            // Cập nhật trạng thái đơn hàng thành "Đang giao" (status = 1)
            $orderModel->updateOrderStatus($order['order_id'], 1);
            // Cập nhật trạng thái thanh toán và lưu transaction id
            $orderModel->setPaymentStatus($order['order_id'], 'paid', $verification['transId'] ?? $transId);
            
            // Xóa giỏ hàng
            $sessionId = session_id();
            $userId = $order['user_id'];
            $cartModel->clearCart($sessionId, $userId);
            
            // Log payment success
            $momoModel->logPayment($order['order_id'], $requestId, $amount, 'success', $_POST, $orderId);
            
            echo json_encode(['status' => 'success', 'message' => 'Payment processed successfully']);
        } else {
            // Log error - order not found
            $momoModel->logPayment(null, $requestId, $amount, 'error', 'Order not found: ' . $orderId, $orderId);
            echo json_encode(['status' => 'error', 'message' => 'Order not found']);
        }
    } else {
        // Thanh toán thất bại
        $order = $orderModel->getOrderByCode($orderId);
        $orderIdForLog = $order ? $order['order_id'] : null;
        // Nếu có đơn hàng, cập nhật trạng thái thanh toán failed
        if ($orderIdForLog) {
            $orderModel->setPaymentStatus($orderIdForLog, 'failed', $transId ?: null);
        }
        
        // Log payment failure
        $momoModel->logPayment($orderIdForLog, $requestId, $amount, 'failed', $_POST, $orderId);
        
        echo json_encode(['status' => 'failed', 'message' => $verification['message']]);
    }
    
} catch (Exception $e) {
    // Log error
    error_log("MoMo Notify Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Internal server error']);
}
?>
