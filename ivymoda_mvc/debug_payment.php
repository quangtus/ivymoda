<?php
/**
 * Debug Payment Flow
 * Kiểm tra luồng thanh toán MoMo
 */

// Load configuration
require_once 'config/config.php';

echo "<h1>Debug Payment Flow - IVY moda</h1>";

echo "<h2>1. Kiểm tra session data</h2>";
session_start();
echo "<pre>";
echo "Session data:\n";
print_r($_SESSION);
echo "</pre>";

echo "<h2>2. Kiểm tra delivery_info</h2>";
if (isset($_SESSION['delivery_info'])) {
    echo "<pre>";
    print_r($_SESSION['delivery_info']);
    echo "</pre>";
} else {
    echo "<p>❌ Không có delivery_info trong session</p>";
}

echo "<h2>3. Test tạo đơn hàng</h2>";
try {
    require_once 'app/models/OrderModel.php';
    $orderModel = new OrderModel();
    
    // Test data
    $testOrderData = [
        'user_id' => 1,
        'session_id' => 'test_session_' . time(),
        'customer_name' => 'Test User',
        'customer_phone' => '0123456789',
        'customer_email' => 'test@example.com',
        'customer_address' => 'Test Address',
        'order_total' => 100000,
        'order_status' => 0,
        'payment_method' => 'momo',
        'shipping_method' => 'Standard',
        'order_note' => 'Test order'
    ];
    
    echo "<p>🔄 Đang test tạo đơn hàng...</p>";
    $result = $orderModel->createOrder($testOrderData);
    
    if ($result['success']) {
        echo "<p>✅ Tạo đơn hàng thành công</p>";
        echo "<p><strong>Order ID:</strong> " . $result['order_id'] . "</p>";
        echo "<p><strong>Order Code:</strong> " . $result['order_code'] . "</p>";
        
        // Test URL redirect
        $redirectUrl = BASE_URL . 'payment/momo?order_id=' . urlencode($result['order_id']) . '&order_code=' . urlencode($result['order_code']) . '&amount=' . urlencode(100000);
        echo "<p><strong>Redirect URL:</strong> <a href='$redirectUrl' target='_blank'>$redirectUrl</a></p>";
        
    } else {
        echo "<p>❌ Tạo đơn hàng thất bại</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Lỗi test order creation: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Test MoMo Payment Request</h2>";
try {
    require_once 'app/models/MomoPaymentModel.php';
    $momoModel = new MomoPaymentModel();
    
    $testOrderData = [
        'order_code' => 'TEST-' . time(),
        'order_total' => 100000
    ];
    
    echo "<p>🔄 Đang test tạo MoMo payment request...</p>";
    $result = $momoModel->createPaymentRequest($testOrderData);
    
    if ($result['success']) {
        echo "<p>✅ Tạo MoMo payment request thành công</p>";
        echo "<p><strong>Pay URL:</strong> <a href='" . $result['payUrl'] . "' target='_blank'>" . $result['payUrl'] . "</a></p>";
    } else {
        echo "<p>❌ Tạo MoMo payment request thất bại: " . $result['message'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Lỗi test MoMo payment: " . $e->getMessage() . "</p>";
}

echo "<h2>5. Kiểm tra URL routing</h2>";
echo "<p><strong>BASE_URL:</strong> " . BASE_URL . "</p>";
echo "<p><strong>Payment URL:</strong> " . BASE_URL . "payment/momo</p>";

echo "<h2>6. Hướng dẫn debug</h2>";
echo "<div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px;'>";
echo "<h3>Để debug luồng thanh toán:</h3>";
echo "<ol>";
echo "<li>Đăng nhập và thêm sản phẩm vào giỏ hàng</li>";
echo "<li>Vào checkout/delivery và chọn MoMo</li>";
echo "<li>Kiểm tra session data ở đây</li>";
echo "<li>Vào checkout/payment và xác nhận</li>";
echo "<li>Kiểm tra log files nếu có lỗi</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='" . BASE_URL . "'>← Quay lại trang chủ</a></p>";
echo "<p><a href='" . BASE_URL . "checkout/delivery'>→ Test luồng thanh toán</a></p>";
?>
