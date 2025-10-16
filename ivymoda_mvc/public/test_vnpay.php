<?php
/**
 * VNPay Test Page
 * Test chức năng thanh toán VNPay
 */

// Load configuration
require_once dirname(__FILE__) . '/../config/config.php';

// Load models
require_once dirname(__FILE__) . '/../app/core/Database.php';
require_once dirname(__FILE__) . '/../app/core/Model.php';
require_once dirname(__FILE__) . '/../app/models/VnpayPaymentModel.php';

echo "<h1>🧪 VNPay Payment Test</h1>";

echo "<h2>1. Configuration Check</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Thông số</th><th>Giá trị</th></tr>";
echo "<tr><td><strong>TMN Code</strong></td><td>" . VNPAY_TMN_CODE . "</td></tr>";
echo "<tr><td><strong>Hash Secret</strong></td><td>" . substr(VNPAY_HASH_SECRET, 0, 10) . "...</td></tr>";
echo "<tr><td><strong>Payment URL</strong></td><td>" . VNPAY_URL . "</td></tr>";
echo "<tr><td><strong>Return URL</strong></td><td>" . VNPAY_RETURN_URL . "</td></tr>";
echo "<tr><td><strong>Notify URL</strong></td><td>" . VNPAY_NOTIFY_URL . "</td></tr>";
echo "</table>";

echo "<h2>2. Test Payment Request</h2>";

if (isset($_POST['test_payment'])) {
    try {
        $vnpayModel = new VnpayPaymentModel();
        
        // Test order data
        $orderData = [
            'order_code' => 'TEST-VNPAY-' . time(),
            'order_total' => $_POST['amount'] ?? 100000
        ];
        
        echo "<h3>Test Order Data:</h3>";
        echo "<pre>" . json_encode($orderData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        
        echo "<h3>Creating payment request...</h3>";
        $result = $vnpayModel->createPaymentRequest($orderData);
        
        echo "<h3>Result:</h3>";
        echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        
        if ($result['success']) {
            echo "<div style='color: green; font-weight: bold; background: #d4edda; padding: 15px; border-radius: 5px;'>";
            echo "✅ SUCCESS! Payment request created<br>";
            echo "<strong>Payment URL:</strong> <a href='" . $result['payment_url'] . "' target='_blank' style='color: #155724;'>" . $result['payment_url'] . "</a><br>";
            echo "<strong>Order Code:</strong> " . $result['order_code'] . "<br>";
            echo "</div>";
            
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
            echo "<strong>🎯 Test Instructions:</strong><br>";
            echo "1. Click the Payment URL above<br>";
            echo "2. Chọn ngân hàng NCB<br>";
            echo "3. Sử dụng thẻ test: 9704198526191432198<br>";
            echo "4. Tên chủ thẻ: NGUYEN VAN A<br>";
            echo "5. Ngày phát hành: 07/15<br>";
            echo "6. OTP: 123456<br>";
            echo "7. Xem kết quả tại Return URL<br>";
            echo "</div>";
            
        } else {
            echo "<div style='color: red; font-weight: bold; background: #f8d7da; padding: 15px; border-radius: 5px;'>";
            echo "❌ FAILED! " . $result['message'];
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='color: red; font-weight: bold; background: #f8d7da; padding: 15px; border-radius: 5px;'>";
        echo "❌ EXCEPTION: " . $e->getMessage();
        echo "</div>";
    }
}

echo "<h3>Test Form:</h3>";
echo "<form method='post' style='background: #f8f9fa; padding: 20px; border-radius: 5px;'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='amount'><strong>Số tiền (VND):</strong></label><br>";
echo "<input type='number' id='amount' name='amount' value='100000' min='1000' max='10000000' style='padding: 8px; width: 200px; margin-top: 5px;'>";
echo "</div>";
echo "<button type='submit' name='test_payment' style='padding: 12px 24px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>";
echo "🧪 Test VNPay Payment";
echo "</button>";
echo "</form>";

echo "<hr>";

echo "<h2>3. Test Information</h2>";
echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>VNPay Sandbox Environment:</strong></p>";
echo "<ul>";
echo "<li>✅ Hoàn toàn miễn phí</li>";
echo "<li>✅ QR code thật - quét bằng app ngân hàng</li>";
echo "<li>✅ 40+ ngân hàng Việt Nam</li>";
echo "<li>✅ Không mất tiền thật</li>";
echo "<li>✅ Test được đầy đủ tính năng</li>";
echo "</ul>";
echo "</div>";

echo "<h3>Thông tin thẻ test:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Thông tin</th><th>Giá trị</th></tr>";
echo "<tr><td><strong>Ngân hàng</strong></td><td>NCB</td></tr>";
echo "<tr><td><strong>Số thẻ</strong></td><td>9704198526191432198</td></tr>";
echo "<tr><td><strong>Tên chủ thẻ</strong></td><td>NGUYEN VAN A</td></tr>";
echo "<tr><td><strong>Ngày phát hành</strong></td><td>07/15</td></tr>";
echo "<tr><td><strong>Mật khẩu OTP</strong></td><td>123456</td></tr>";
echo "</table>";

echo "<hr>";
echo "<h2>4. Integration Status</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>✅ Configuration:</strong> VNPay credentials loaded</p>";
echo "<p><strong>✅ Model:</strong> VnpayPaymentModel created</p>";
echo "<p><strong>✅ Return Handler:</strong> vnpay_return.php ready</p>";
echo "<p><strong>✅ Notify Handler:</strong> vnpay_notify.php ready</p>";
echo "<p><strong>✅ Test Page:</strong> Ready for testing</p>";
echo "<p><strong>🔄 Next:</strong> Test with real payment flow</p>";
echo "</div>";

echo "<hr>";
echo "<p><a href='payment/vnpay_return.php'>🔍 View Return Handler</a> | ";
echo "<a href='payment/vnpay_notify.php'>📡 View Notify Handler</a></p>";
?>
