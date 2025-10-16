# Hướng dẫn chi tiết luồng thanh toán MoMo

## 🔄 **Luồng thanh toán MoMo hoàn chỉnh:**

### **Bước 1: User chọn MoMo và xác nhận đặt hàng**
```
User → CheckoutController::process() → Tạo đơn hàng → PaymentController::momo()
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
- Tạo đơn hàng trong database
- Lưu thông tin vào `tbl_order`
- Redirect đến `PaymentController::momo()`

### **2. PaymentController::momo()**
- Lấy thông tin đơn hàng
- Gọi `MomoPaymentModel::createPaymentRequest()`
- Redirect user đến MoMo Gateway

### **3. MomoPaymentModel::createPaymentRequest()**
- Tạo signature để bảo mật
- Gửi request đến MoMo API
- Trả về `payUrl` để redirect

### **4. momoReturn.php**
- Xử lý khi user quay lại từ MoMo
- Hiển thị kết quả cho user
- Redirect đến trang success hoặc error

### **5. momoNotify.php**
- Xử lý IPN (Instant Payment Notification) từ MoMo
- Verify signature để đảm bảo an toàn
- Cập nhật trạng thái đơn hàng
- Xóa giỏ hàng

## 🛠️ **Cách debug và xử lý lỗi:**

### **Lỗi "Thông tin thanh toán không hợp lệ":**
Có thể do:
1. Thiếu thông tin đơn hàng
2. Lỗi tạo payment request
3. Lỗi kết nối đến MoMo API

### **Cách debug:**
1. Kiểm tra log files
2. Kiểm tra thông tin đơn hàng
3. Test với MoMo Sandbox
