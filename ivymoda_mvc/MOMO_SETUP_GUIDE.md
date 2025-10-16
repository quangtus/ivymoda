# Hướng dẫn cấu hình MoMo Payment cho IVY moda

## 1. Cấu hình hiện tại

Dự án đã được tích hợp sẵn MoMo Payment với cấu hình TEST. Tất cả thông tin cấu hình được lưu trong file `.env`.

### File .env hiện tại:
```env
DEV=development
DEV_MOMO_ENDPOINT=https://test-payment.momo.vn
DEV_ACCESS_KEY=F8BBA842ECF85
DEV_PARTNER_CODE=MOMO
DEV_SECRET_KEY=K951B6PE1waDMi640xX08PD3vg6EkVlz
DEV_PRIVATE_KEY=MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCSlr6oxdLo7XXHuMEaj1bfLF4cSwB3IMTjRSheGhB/0rpq21IhyVNkwQZ+rqCYmbYGiBUm0+SO+uLn+P2YGe0DyoozPNqdJBoERuvwXIHIed/bbp2wu3VFslIwuMJ3dQLWfRirwoq98eG+a9VsZxZ+mB+juJBNWYvSvV5DHOCdKrfUQvXJg0y5pmOkeZC5PbgebGX1b16dG0nJVNSHAqG2M5I6xHTewyexySkVP2mfX9X0ETYp/esUapzom6ReSorplb2a2YYtKrcr4lFGrbDw9WZxty9Ov9p1bgQvbJzWqT0Rzge2IOG1Jh3r+i/zEtWQoBCMtW3sTHf9qBE98+s3AgMBAAECggEAQxBiU9aFgnk5HFGDTwJrDRlASRNrOBUu3odCS6MDD2e6T67daYWw+HRy4zxDTu1r4JsbijMA6wUPEG/SnWanD8f26DAcGC5vFKvZv5Ki8bQIXVzDGhr5MRS/E3lDxuEqljSPN+1+Ch6CV9r/vmN/YBV6zC1hH3IrTRPD71Jj1KMITCDQlKcDbZqgFTY0wq2ONrzQ5lF0u1sSrdnHLny2kayIAocWqSVbfcSE/9iKN4jkc2/zBQOAFgBQVPuZOdLL+rf1PTKus75aJm/TzaCcoxF496kTw/mRJ77rOxB8mNDEhGULTopG0Bk12upA+QXzxsWJKm8pgv/iXV+0Hi27oQKBgQDCMAydxOCybtOnTkRQ66typlRJQDVgBCD4yhNchOd6jWk34GRY64MuNbyyrD8A5P/ioI4OvRs00S28Sb/G/w3ldciR0j7lm9FgbjkTDCrVVbp4P8gczgL+z5mPdCua1KQD+2C5RA2tMRJlAfczIVekoxCriuCQSO9RltsGT7LmEQKBgQDBP/bzTD+PKWmxeBOTLeNGH8IM63DeccWtowxRgeF1xohFK1ipi5RKxoKOVLxku0U3tKOe6thE2IhpaqYFcCRs2TFZidChyytEjD4LVlECfe9OvCqfVL8IvDUzw8B3850HYrGUh8y4Mmry3JJYLOKoAPBqEg9NLe9c8yI9rI3UxwKBgGVQjnSOMLHH8vPaePhDTUtfDqC9OFvlK5LCU8G0sdUWDKyTjad7ERE+BjqudZyw3fTO0e9MqPIwpQ0U6VMY5ZYvkrrKF/jSCDaoq2yNr5doyAZPOMgWkCeEBtl6wflhMkXFlNx0bjJLZQ6ALQpnPgPu9BacObft5bcK3zF2yZ8RAoGBAIgkYfuBKf3XdQh7yX6Ug1qxoOmtLHTpvhPXnCQH1ig811+za+D13mDXfL5838QvUlIuRl78n6PQ0DlD0vZdzKuKT4P+3SY+lZrTGhqukp+ozOCxG23oLDUhMnHnZD6dN3EujGBRU14o1sOFtOu9o2gsUTLIylLbG5hmCSdd2wWdAoGBAIvddYHkS1b8B8TCv1+CVObe5WCUvqpZgbHo3ozt0KxlgWvl+f6y8DUToK3KU9sp512Ivk43mn1Xv2QftBx8E4vyhWeltdiKOJOhMsk6djjoyb8AOuyPVumXTQBuue1yRrTKLAl1SaZnzdrKzpXsI8OBpnI0bjFxA2SNnU/iD0R
DEV_PUBLIC_KEY=MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkpa+qMXS6O11x7jBGo9W3yxeHEsAdyDE40UoXhoQf9K6attSIclTZMEGfq6gmJm2BogVJtPkjvri5/j9mBntA8qKMzzanSQaBEbr8FyByHnf226dsLt1RbJSMLjCd3UC1n0Yq8KKvfHhvmvVbGcWfpgfo7iQTVmL0r1eQxzgnSq31EL1yYNMuaZjpHmQuT24Hmxl9W9enRtJyVTUhwKhtjOSOsR03sMnsckpFT9pn1/V9BE2Kf3rFGqc6JukXkqK6ZW9mtmGLSq3K+JRRq2w8PVmcbcvTr/adW4EL2yc1qk9Ec4HtiDhtSYd6/ov8xLVkKAQjLVt7Ex3/agRPfPrNwIDAQAB
```

## 2. Cách thay đổi sang thông tin của cửa hàng mẹ

### Bước 1: Lấy thông tin từ cửa hàng mẹ
Bạn cần lấy các thông tin sau từ tài khoản MoMo của cửa hàng mẹ:

- **Partner Code**: Mã đối tác
- **Access Key**: Mã truy cập
- **Secret Key**: Mã bí mật
- **Private Key**: Khóa riêng tư (nếu có)
- **Public Key**: Khóa công khai (nếu có)

### Bước 2: Cập nhật file .env
Thay thế các giá trị trong file `.env`:

```env
# Thay đổi từ TEST sang thông tin thật của cửa hàng mẹ
DEV_PARTNER_CODE=PARTNER_CODE_CUA_HANG_ME
DEV_ACCESS_KEY=ACCESS_KEY_CUA_HANG_ME
DEV_SECRET_KEY=SECRET_KEY_CUA_HANG_ME
DEV_PRIVATE_KEY=PRIVATE_KEY_CUA_HANG_ME
DEV_PUBLIC_KEY=PUBLIC_KEY_CUA_HANG_ME

# Nếu chuyển sang production, thay đổi endpoint
DEV_MOMO_ENDPOINT=https://payment.momo.vn
```

### Bước 3: Kiểm tra cấu hình
Chạy file test để kiểm tra:
```
http://localhost/ivymoda/ivymoda_mvc/test_payment_flow.php
```

## 3. Luồng thanh toán

### 3.1. Luồng COD (Cash on Delivery)
1. User chọn sản phẩm → Thêm vào giỏ hàng
2. Vào checkout/delivery → Nhập thông tin giao hàng
3. Vào checkout/payment → Chọn "Thanh toán khi nhận hàng (COD)"
4. Xác nhận đặt hàng → Tạo đơn hàng → Xóa giỏ hàng → Redirect đến success

### 3.2. Luồng MoMo
1. User chọn sản phẩm → Thêm vào giỏ hàng
2. Vào checkout/delivery → Nhập thông tin giao hàng
3. Vào checkout/payment → Chọn "Thanh toán qua MoMo"
4. Xác nhận đặt hàng → Tạo đơn hàng → Tạo payment request → Redirect đến MoMo
5. User thanh toán trên MoMo → MoMo redirect về momoReturn.php
6. MoMo gửi IPN về momoNotify.php → Cập nhật trạng thái đơn hàng → Xóa giỏ hàng

## 4. Các file quan trọng

### 4.1. Backend
- `app/models/MomoPaymentModel.php` - Xử lý logic MoMo
- `app/controllers/frontend/PaymentController.php` - Controller xử lý thanh toán
- `app/controllers/frontend/CheckoutController.php` - Controller checkout
- `config/config.php` - Load cấu hình từ .env

### 4.2. Frontend
- `app/views/frontend/checkout/payment.php` - Trang chọn phương thức thanh toán
- `public/assets/css/payment.css` - CSS cho trang thanh toán

### 4.3. Payment Handlers
- `public/payment/momoReturn.php` - Xử lý return từ MoMo
- `public/payment/momoNotify.php` - Xử lý IPN từ MoMo

## 5. Database

### 5.1. Bảng tbl_momo_transaction
Lưu log các giao dịch MoMo:
```sql
CREATE TABLE `tbl_momo_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `request_id` varchar(255) NOT NULL,
  `order_code` varchar(255) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `result_code` varchar(10) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_id` (`request_id`)
);
```

## 6. Test và Debug

### 6.1. Test cấu hình
Chạy file test để kiểm tra:
```
http://localhost/ivymoda/ivymoda_mvc/test_payment_flow.php
```

### 6.2. Log files
Kiểm tra log trong thư mục `logs/` để debug các lỗi.

### 6.3. MoMo Sandbox
- URL: https://test-payment.momo.vn
- Tài khoản test: Sử dụng thông tin từ MoMo Sandbox
- Không trừ tiền thật

## 7. Lưu ý quan trọng

### 7.1. Bảo mật
- **KHÔNG BAO GIỜ** commit file `.env` lên Git
- **KHÔNG BAO GIỜ** chia sẻ thông tin Secret Key, Private Key
- Sử dụng HTTPS trong production

### 7.2. Môi trường
- **Development**: Sử dụng MoMo Sandbox
- **Production**: Sử dụng MoMo Production API

### 7.3. Error Handling
- Tất cả lỗi được log vào file log
- User được thông báo lỗi thân thiện
- Hệ thống có cơ chế rollback khi có lỗi

## 8. Troubleshooting

### 8.1. Lỗi thường gặp
1. **"Invalid signature"**: Kiểm tra Secret Key
2. **"Partner not found"**: Kiểm tra Partner Code
3. **"Order not found"**: Kiểm tra order_code trong database
4. **"Connection timeout"**: Kiểm tra kết nối internet và MoMo API

### 8.2. Debug steps
1. Kiểm tra file `.env` có đúng không
2. Kiểm tra log files
3. Test với MoMo Sandbox trước
4. Kiểm tra database connection
5. Kiểm tra URL callback có đúng không

## 9. Support

Nếu có vấn đề, hãy:
1. Kiểm tra log files
2. Chạy file test
3. Kiểm tra cấu hình MoMo
4. Liên hệ MoMo support nếu cần
