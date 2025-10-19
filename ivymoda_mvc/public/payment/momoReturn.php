<?php
/**
 * MoMo Payment Return Handler
 * Xử lý khi user quay lại từ MoMo sau khi thanh toán
 */

// Load configuration
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/app/core/App.php';

// Load models
require_once dirname(__DIR__, 2) . '/app/models/OrderModel.php';

// Start session
session_start();

// Log all incoming data for debugging
error_log("MoMo Return - Raw GET data: " . print_r($_GET, true));

// Get parameters from MoMo
$orderId = $_GET['orderId'] ?? '';
$resultCode = $_GET['resultCode'] ?? '';
$message = $_GET['message'] ?? '';
$requestId = $_GET['requestId'] ?? '';
$amount = $_GET['amount'] ?? '';
$transId = $_GET['transId'] ?? '';

// Log the return for debugging
error_log("MoMo Return - Parsed data: orderId=$orderId, resultCode=$resultCode, message=$message, requestId=$requestId, amount=$amount, transId=$transId");

try {
    if ($resultCode == '0') {
        // Thanh toán thành công
        $_SESSION['success'] = 'Thanh toán thành công!';
        $_SESSION['order_code'] = $orderId;
        
        // Load order model to verify order exists
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderByCode($orderId);
        
        if ($order) {
            // Update order status to confirmed if not already updated by IPN
            if ($order['order_status'] == 0) {
                $orderModel->updateOrderStatus($order['order_id'], 1); // 1 = Đang giao
            }
            if ($order['payment_status'] != 'paid') {
                $orderModel->setPaymentStatus($order['order_id'], 'paid', $transId);
            }
        }
        
        // Redirect to success page
        header('Location: ' . BASE_URL . 'checkout/success');
        exit;
    } else {
        // Thanh toán thất bại
        $_SESSION['error'] = 'Thanh toán thất bại: ' . $message;
        
        // Update order status to failed if order exists
        if (!empty($orderId)) {
            $orderModel = new OrderModel();
            $order = $orderModel->getOrderByCode($orderId);
            if ($order) {
                $orderModel->setPaymentStatus($order['order_id'], 'failed', $transId);
            }
        }
        
        // Redirect back to checkout
        header('Location: ' . BASE_URL . 'checkout');
        exit;
    }
} catch (Exception $e) {
    error_log("MoMo Return Error: " . $e->getMessage());
    $_SESSION['error'] = 'Có lỗi xảy ra khi xử lý thanh toán. Vui lòng thử lại.';
    header('Location: ' . BASE_URL . 'checkout');
    exit;
}
?>
