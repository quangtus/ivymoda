<?php
/**
 * Test MoMo Flow - Kiểm tra luồng thanh toán MoMo
 */

// Load configuration
require_once 'config/config.php';

echo "<h1>Test MoMo Flow - IVY moda</h1>";

echo "<h2>1. Kiểm tra cấu hình MoMo</h2>";
echo "<p><strong>Partner Code:</strong> " . (defined('MOMO_PARTNER_CODE') ? MOMO_PARTNER_CODE : 'NOT DEFINED') . "</p>";
echo "<p><strong>Access Key:</strong> " . (defined('MOMO_ACCESS_KEY') ? MOMO_ACCESS_KEY : 'NOT DEFINED') . "</p>";
echo "<p><strong>Secret Key:</strong> " . (defined('MOMO_SECRET_KEY') ? MOMO_SECRET_KEY : 'NOT DEFINED') . "</p>";
echo "<p><strong>Endpoint:</strong> " . (defined('MOMO_ENDPOINT') ? MOMO_ENDPOINT : 'NOT DEFINED') . "</p>";

echo "<h2>2. Test tạo đơn hàng</h2>";
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
        
        // Test MoMo payment request
        echo "<h2>3. Test MoMo Payment Request</h2>";
        try {
            require_once 'app/models/MomoPaymentModel.php';
            $momoModel = new MomoPaymentModel();
            
            $orderData = [
                'order_code' => $result['order_code'],
                'order_total' => 100000
            ];
            
            echo "<p>🔄 Đang test tạo MoMo payment request...</p>";
            $paymentResult = $momoModel->createPaymentRequest($orderData);
            
            if ($paymentResult['success']) {
                echo "<p>✅ Tạo MoMo payment request thành công</p>";
                echo "<p><strong>Pay URL:</strong> <a href='" . $paymentResult['payUrl'] . "' target='_blank'>" . $paymentResult['payUrl'] . "</a></p>";
                echo "<p><strong>Request ID:</strong> " . $paymentResult['requestId'] . "</p>";
            } else {
                echo "<p>❌ Tạo MoMo payment request thất bại: " . $paymentResult['message'] . "</p>";
            }
            
        } catch (Exception $e) {
            echo "<p>❌ Lỗi test MoMo payment: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p>❌ Tạo đơn hàng thất bại</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Lỗi test order creation: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Hướng dẫn test luồng thanh toán</h2>";
echo "<div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px;'>";
echo "<h3>Để test luồng thanh toán MoMo:</h3>";
echo "<ol>";
echo "<li><strong>Đăng nhập:</strong> Vào trang chủ và đăng nhập</li>";
echo "<li><strong>Thêm sản phẩm:</strong> Thêm sản phẩm vào giỏ hàng</li>";
echo "<li><strong>Checkout Delivery:</strong> Vào checkout/delivery và chọn MoMo</li>";
echo "<li><strong>Checkout Payment:</strong> Vào checkout/payment và xác nhận</li>";
echo "<li><strong>MoMo Gateway:</strong> Sẽ redirect đến MoMo để thanh toán</li>";
echo "<li><strong>Thanh toán:</strong> Thanh toán trên MoMo (TEST mode)</li>";
echo "<li><strong>Kết quả:</strong> MoMo sẽ redirect về và cập nhật trạng thái đơn hàng</li>";
echo "</ol>";
echo "</div>";

echo "<h2>5. Lưu ý quan trọng</h2>";
echo "<div style='background-color: #fff3cd; padding: 20px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
echo "<p><strong>⚠️ Đây là môi trường TEST:</strong></p>";
echo "<ul>";
echo "<li>Sử dụng MoMo Sandbox API</li>";
echo "<li>Không trừ tiền thật</li>";
echo "<li>Chỉ để test và phát triển</li>";
echo "<li>Khi chuyển sang production, cần thay đổi thông tin trong .env</li>";
echo "</ul>";
echo "</div>";

echo "<h2>6. Debug và Troubleshooting</h2>";
echo "<div style='background-color: #f8d7da; padding: 20px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
echo "<h3>Nếu gặp lỗi:</h3>";
echo "<ol>";
echo "<li>Kiểm tra log files trong thư mục logs/</li>";
echo "<li>Chạy file debug_payment.php để kiểm tra</li>";
echo "<li>Kiểm tra session data</li>";
echo "<li>Kiểm tra database connection</li>";
echo "<li>Kiểm tra MoMo configuration</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='" . BASE_URL . "'>← Quay lại trang chủ</a></p>";
echo "<p><a href='" . BASE_URL . "checkout/delivery'>→ Test luồng thanh toán</a></p>";
echo "<p><a href='debug_payment.php'>→ Debug Payment</a></p>";
?>
