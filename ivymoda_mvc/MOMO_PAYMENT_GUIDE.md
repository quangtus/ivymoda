# Hướng dẫn chi tiết về thanh toán MoMo

## 🎯 **Chức năng thanh toán MoMo là gì?**

MoMo là một ví điện tử phổ biến tại Việt Nam. Khi tích hợp MoMo vào website, khách hàng có thể:
- Thanh toán trực tuyến bằng ví MoMo
- Không cần nhập thông tin thẻ tín dụng
- Thanh toán nhanh chóng và an toàn

## 🔄 **Luồng thanh toán MoMo hoàn chỉnh:**

### **Bước 1: User chọn MoMo và xác nhận đặt hàng**
```
User → CheckoutController::process() → Tạo đơn hàng → Lưu vào database
```

### **Bước 2: Tạo payment request**
```
PaymentController::momo() → MomoPaymentModel::createPaymentRequest() → Gửi request đến MoMo API
```

### **Bước 3: MoMo xử lý và trả về payUrl**
```
MoMo API → Trả về payUrl → Redirect user đến MoMo Gateway
```

### **Bước 4: User thanh toán trên MoMo**
```
User → MoMo App/Web → Nhập thông tin thanh toán → Xác nhận thanh toán
```

### **Bước 5: MoMo gửi kết quả về**
```
MoMo → momoReturn.php (user redirect) + momoNotify.php (IPN callback)
```

## 📋 **Chi tiết từng file xử lý:**

### **1. CheckoutController::process()**
**Chức năng:** Tạo đơn hàng và quyết định phương thức thanh toán
```php
// Tạo đơn hàng trong database
$result = $this->orderModel->createOrder($orderData);

// Nếu chọn MoMo
if ($paymentMethod === 'momo') {
    // Lưu thông tin đơn hàng vào session
    $_SESSION['momo_order_info'] = [
        'order_id' => $orderId,
        'order_code' => $result['order_code'],
        'amount' => (int)$totalAmount
    ];
    
    // Redirect đến PaymentController
    $this->redirect('payment/momo?order_id=' . $orderId . '&order_code=' . $result['order_code'] . '&amount=' . $totalAmount);
}
```

### **2. PaymentController::momo()**
**Chức năng:** Tạo payment request và redirect đến MoMo
```php
// Lấy thông tin đơn hàng từ URL hoặc session
$orderId = $_REQUEST['order_id'] ?? $_SESSION['momo_order_info']['order_id'];
$orderCode = $_REQUEST['order_code'] ?? $_SESSION['momo_order_info']['order_code'];
$amount = $_REQUEST['amount'] ?? $_SESSION['momo_order_info']['amount'];

// Tạo payment request
$paymentResult = $this->momoPaymentModel->createPaymentRequest($orderData);

// Redirect đến MoMo
header('Location: ' . $paymentResult['payUrl']);
```

### **3. MomoPaymentModel::createPaymentRequest()**
**Chức năng:** Tạo signature và gửi request đến MoMo API
```php
// Tạo signature để bảo mật
$rawHash = "accessKey=" . $this->accessKey . "&amount=" . $amount . "...";
$signature = hash_hmac('sha256', $rawHash, $this->secretKey);

// Gửi request đến MoMo API
$response = $this->sendRequest($data);

// Trả về payUrl
return ['success' => true, 'payUrl' => $response['payUrl']];
```

### **4. momoReturn.php**
**Chức năng:** Xử lý khi user quay lại từ MoMo
```php
// Lấy kết quả từ MoMo
$resultCode = $_GET['resultCode'];
$orderId = $_GET['orderId'];

if ($resultCode == '0') {
    // Thanh toán thành công
    $_SESSION['success'] = 'Thanh toán thành công!';
    header('Location: ' . BASE_URL . 'checkout/success');
} else {
    // Thanh toán thất bại
    $_SESSION['error'] = 'Thanh toán thất bại';
    header('Location: ' . BASE_URL . 'checkout');
}
```

### **5. momoNotify.php**
**Chức năng:** Xử lý IPN (Instant Payment Notification) từ MoMo
```php
// Verify signature để đảm bảo an toàn
$verification = $momoModel->verifyPayment(...);

if ($verification['valid'] && $verification['success']) {
    // Cập nhật trạng thái đơn hàng
    $orderModel->updateOrderStatus($orderId, 1);
    
    // Xóa giỏ hàng
    $cartModel->clearCart($sessionId, $userId);
    
    echo json_encode(['status' => 'success']);
}
```

## 🛠️ **Cách debug và xử lý lỗi:**

### **Lỗi "Thông tin thanh toán không hợp lệ":**
**Nguyên nhân có thể:**
1. Thiếu thông tin đơn hàng trong URL hoặc session
2. Lỗi tạo payment request
3. Lỗi kết nối đến MoMo API

**Cách debug:**
1. Kiểm tra log files trong thư mục `logs/`
2. Chạy file `debug_payment.php` để kiểm tra
3. Kiểm tra session data
4. Test với MoMo Sandbox

### **Cách kiểm tra log:**
```bash
# Kiểm tra log files
tail -f logs/error.log
tail -f logs/payment.log
```

## 🧪 **Cách test thanh toán MoMo:**

### **1. Test với MoMo Sandbox:**
- Sử dụng thông tin TEST trong file `.env`
- Không trừ tiền thật
- Có thể test tất cả các trường hợp

### **2. Test luồng hoàn chỉnh:**
1. Đăng nhập và thêm sản phẩm vào giỏ hàng
2. Vào `checkout/delivery` và chọn MoMo
3. Vào `checkout/payment` và xác nhận
4. Kiểm tra redirect đến MoMo
5. Test thanh toán trên MoMo
6. Kiểm tra kết quả trả về

### **3. Test với thông tin thật:**
- Thay đổi thông tin trong file `.env`
- Sử dụng thông tin từ tài khoản MoMo của cửa hàng mẹ
- Test với số tiền nhỏ trước

## 📱 **MoMo App và Web:**

### **MoMo App:**
- User có thể thanh toán trực tiếp trên app
- Nhập mã PIN hoặc vân tay
- Xác nhận thanh toán

### **MoMo Web:**
- User có thể thanh toán trên web
- Nhập thông tin tài khoản
- Xác nhận thanh toán

## 🔒 **Bảo mật:**

### **Signature Verification:**
- MoMo sử dụng HMAC-SHA256 để tạo signature
- Mỗi request đều có signature riêng
- Verify signature để đảm bảo an toàn

### **IPN (Instant Payment Notification):**
- MoMo gửi thông báo về kết quả thanh toán
- Xử lý IPN để cập nhật trạng thái đơn hàng
- Đảm bảo tính nhất quán của dữ liệu

## 🚀 **Triển khai Production:**

### **1. Thay đổi thông tin:**
```env
# Thay đổi từ TEST sang PRODUCTION
DEV_PARTNER_CODE=PARTNER_CODE_THAT
DEV_ACCESS_KEY=ACCESS_KEY_THAT
DEV_SECRET_KEY=SECRET_KEY_THAT
DEV_MOMO_ENDPOINT=https://payment.momo.vn
```

### **2. Kiểm tra:**
- Test với số tiền nhỏ
- Kiểm tra log files
- Monitor performance
- Backup database

### **3. Support:**
- Liên hệ MoMo support nếu có vấn đề
- Kiểm tra documentation của MoMo
- Monitor error logs
