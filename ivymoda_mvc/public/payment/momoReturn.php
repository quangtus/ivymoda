<?php
/**
 * MoMo Payment Return Handler
 * Xử lý khi user quay lại từ MoMo sau khi thanh toán
 */

// Load configuration
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/app/core/App.php';

// Start session
session_start();

// Get parameters from MoMo
$orderId = $_GET['orderId'] ?? '';
$resultCode = $_GET['resultCode'] ?? '';
$message = $_GET['message'] ?? '';

// Log the return for debugging
error_log("MoMo Return - orderId: $orderId, resultCode: $resultCode, message: $message");

if ($resultCode == '0') {
    // Thanh toán thành công
    $_SESSION['success'] = 'Thanh toán thành công!';
    $_SESSION['order_code'] = $orderId;
    
    // Redirect to success page
    header('Location: ' . BASE_URL . 'checkout/success');
    exit;
} else {
    // Thanh toán thất bại
    $_SESSION['error'] = 'Thanh toán thất bại: ' . $message;
    
    // Redirect back to checkout
    header('Location: ' . BASE_URL . 'checkout');
    exit;
}
?>
