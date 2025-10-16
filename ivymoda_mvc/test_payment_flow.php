<?php
/**
 * Test Payment Flow
 * Kiểm tra luồng thanh toán từ giỏ hàng đến thanh toán
 */

// Load configuration
require_once 'config/config.php';

echo "<h1>Test Payment Flow - IVY moda</h1>";

echo "<h2>1. Kiểm tra cấu hình MoMo</h2>";
echo "<p><strong>Partner Code:</strong> " . (defined('MOMO_PARTNER_CODE') ? MOMO_PARTNER_CODE : 'NOT DEFINED') . "</p>";
echo "<p><strong>Access Key:</strong> " . (defined('MOMO_ACCESS_KEY') ? MOMO_ACCESS_KEY : 'NOT DEFINED') . "</p>";
echo "<p><strong>Secret Key:</strong> " . (defined('MOMO_SECRET_KEY') ? MOMO_SECRET_KEY : 'NOT DEFINED') . "</p>";
echo "<p><strong>Endpoint:</strong> " . (defined('MOMO_ENDPOINT') ? MOMO_ENDPOINT : 'NOT DEFINED') . "</p>";
echo "<p><strong>Return URL:</strong> " . (defined('MOMO_RETURN_URL') ? MOMO_RETURN_URL : 'NOT DEFINED') . "</p>";
echo "<p><strong>Notify URL:</strong> " . (defined('MOMO_NOTIFY_URL') ? MOMO_NOTIFY_URL : 'NOT DEFINED') . "</p>";

echo "<h2>2. Kiểm tra file .env</h2>";
$envFile = dirname(__FILE__) . '/.env';
if (file_exists($envFile)) {
    echo "<p>✅ File .env tồn tại</p>";
    $envContent = file_get_contents($envFile);
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        if (strpos($line, 'DEV_') === 0) {
            echo "<p><code>" . htmlspecialchars($line) . "</code></p>";
        }
    }
} else {
    echo "<p>❌ File .env không tồn tại</p>";
}

echo "<h2>3. Kiểm tra database connection</h2>";
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    echo "<p>✅ Kết nối database thành công</p>";
    
    // Kiểm tra bảng tbl_momo_transaction
    $stmt = $pdo->query("SHOW TABLES LIKE 'tbl_momo_transaction'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Bảng tbl_momo_transaction tồn tại</p>";
    } else {
        echo "<p>❌ Bảng tbl_momo_transaction không tồn tại</p>";
    }
    
    // Kiểm tra bảng tbl_order
    $stmt = $pdo->query("SHOW TABLES LIKE 'tbl_order'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Bảng tbl_order tồn tại</p>";
    } else {
        echo "<p>❌ Bảng tbl_order không tồn tại</p>";
    }
    
} catch (PDOException $e) {
    echo "<p>❌ Lỗi kết nối database: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Kiểm tra file payment handlers</h2>";
$momoReturnFile = dirname(__FILE__) . '/public/payment/momoReturn.php';
$momoNotifyFile = dirname(__FILE__) . '/public/payment/momoNotify.php';

if (file_exists($momoReturnFile)) {
    echo "<p>✅ File momoReturn.php tồn tại</p>";
} else {
    echo "<p>❌ File momoReturn.php không tồn tại</p>";
}

if (file_exists($momoNotifyFile)) {
    echo "<p>✅ File momoNotify.php tồn tại</p>";
} else {
    echo "<p>❌ File momoNotify.php không tồn tại</p>";
}

echo "<h2>5. Test MoMo Payment Model</h2>";
try {
    require_once 'app/models/MomoPaymentModel.php';
    $momoModel = new MomoPaymentModel();
    echo "<p>✅ MomoPaymentModel khởi tạo thành công</p>";
    
    // Test tạo payment request
    $testOrderData = [
        'order_code' => 'TEST-' . time(),
        'order_total' => 100000
    ];
    
    echo "<p>🔄 Đang test tạo payment request...</p>";
    $result = $momoModel->createPaymentRequest($testOrderData);
    
    if ($result['success']) {
        echo "<p>✅ Tạo payment request thành công</p>";
        echo "<p><strong>Pay URL:</strong> " . htmlspecialchars($result['payUrl']) . "</p>";
    } else {
        echo "<p>❌ Tạo payment request thất bại: " . htmlspecialchars($result['message']) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Lỗi test MoMo Payment Model: " . $e->getMessage() . "</p>";
}

echo "<h2>6. Hướng dẫn test</h2>";
echo "<div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px;'>";
echo "<h3>Để test luồng thanh toán:</h3>";
echo "<ol>";
echo "<li>Đăng nhập vào hệ thống</li>";
echo "<li>Thêm sản phẩm vào giỏ hàng</li>";
echo "<li>Vào trang checkout/delivery để nhập thông tin giao hàng</li>";
echo "<li>Vào trang checkout/payment để chọn phương thức thanh toán</li>";
echo "<li>Chọn 'Thanh toán qua MoMo' và xác nhận</li>";
echo "<li>Hệ thống sẽ redirect đến MoMo để thanh toán</li>";
echo "<li>Sau khi thanh toán, MoMo sẽ redirect về momoReturn.php</li>";
echo "<li>MoMo sẽ gửi IPN về momoNotify.php để cập nhật trạng thái đơn hàng</li>";
echo "</ol>";
echo "</div>";

echo "<h2>7. Lưu ý quan trọng</h2>";
echo "<div style='background-color: #fff3cd; padding: 20px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
echo "<p><strong>⚠️ Đây là môi trường TEST:</strong></p>";
echo "<ul>";
echo "<li>Sử dụng MoMo Sandbox API</li>";
echo "<li>Không trừ tiền thật</li>";
echo "<li>Chỉ để test và phát triển</li>";
echo "<li>Khi chuyển sang production, cần thay đổi thông tin trong .env</li>";
echo "</ul>";
echo "</div>";

echo "<p><a href='" . BASE_URL . "'>← Quay lại trang chủ</a></p>";
?>
