# Hướng dẫn tích hợp MoMo Payment - IVY Moda

## Tổng quan
Dự án IVY Moda đã được tích hợp hoàn toàn với MoMo Payment Gateway sử dụng cấu hình từ dự án MoMo chính thức.

## Cấu hình

### 1. File .env
```env
# MoMo Payment Configuration - Development
DEV=development
DEV_MOMO_ENDPOINT=https://test-payment.momo.vn
DEV_ACCESS_KEY=mTCKt9W3eU1m39TW
DEV_PARTNER_CODE=MOMOIQA420180417
DEV_SECRET_KEY=PPuDXq1KowPT1ftR8DvlQTHhC03aul17
DEV_PRIVATE_KEY=MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCSlr6oxdLo7XXHuMEaj1bfLF4cSwB3IMTjRSheGhB/0rpq21IhyVNkwQZ+rqCYmbYGiBUm0+SO+uLn+P2YGe0DyoozPNqdJBoERuvwXIHIed/bbp2wu3VFslIwuMJ3dQLWfRirwoq98eG+a9VsZxZ+mB+juJBNWYvSvV5DHOCdKrfUQvXJg0y5pmOkeZC5PbgebGX1b16dG0nJVNSHAqG2M5I6xHTewyexySkVP2mfX9X0ETYp/esUapzom6ReSorplb2a2YYtKrcr4lFGrbDw9WZxty9Ov9p1bgQvbJzWqT0Rzge2IOG1Jh3r+i/zEtWQoBCMtW3sTHf9qBE98+s3AgMBAAECggEAQxBiU9aFgnk5HFGDTwJrDRlASRNrOBUu3odCS6MDD2e6T67daYWw+HRy4zxDTu1r4JsbijMA6wUPEG/SnWanD8f26DAcGC5vFKvZv5Ki8bQIXVzDGhr5MRS/E3lDxuEqljSPN+1+Ch6CV9r/vmN/YBV6zC1hH3IrTRPD71Jj1KMITCDQlKcDbZqgFTY0wq2ONrzQ5lF0u1sSrdnHLny2kayIAocWqSVbfcSE/9iKN4jkc2/zBQOAFgBQVPuZOdLL+rf1PTKus75aJm/TzaCcoxF496kTw/mRJ77rOxB8mNDEhGULTopG0Bk12upA+QXzxsWJKm8pgv/iXV+0Hi27oQKBgQDCMAydxOCybtOnTkRQ66typlRJQDVgBCD4yhNchOd6jWk34GRY64MuNbyyrD8A5P/ioI4OvRs00S28Sb/G/w3ldciR0j7lm9FgbjkTDCrVVbp4P8gczgL+z5mPdCua1KQD+2C5RA2tMRJlAfczIVekoxCriuCQSO9RltsGT7LmEQKBgQDBP/bzTD+PKWmxeBOTLeNGH8IM63DeccWtowxRgeF1xohFK1ipi5RKxoKOVLxku0U3tKOe6thE2IhpaqYFcCRs2TFZidChyytEjD4LVlECfe9OvCqfVL8IvDUzw8B3850HYrGUh8y4Mmry3JJYLOKoAPBqEg9NLe9c8yI9rI3UxwKBgGVQjnSOMLHH8vPaePhDTUtfDqC9OFvlK5LCU8G0sdUWDKyTjad7ERE+BjqudZyw3fTO0e9MqPIwpQ0U6VMY5ZYvkrrKF/jSCDaoq2yNr5doyAZPOMgWkCeEBtl6wflhMkXFlNx0bjJLZQ6ALQpnPgPu9BacObft5bcK3zF2yZ8RAoGBAIgkYfuBKf3XdQh7yX6Ug1qxoOmtLHTpvhPXnCQH1ig811+za+D13mDXfL5838QvUlIuRl78n6PQ0DlD0vZdzKuKT4P+3SY+lZrTGhqukp+ozOCxG23oLDUhMnHnZD6dN3EujGBRU14o1sOFtOu9o2gsUTLIylLbG5hmCSdd2wWdAoGBAIvddYHkS1b8B8TCv1+CVObe5WCUvqpZgbHo3oztD0KxlgWvl+f6y8DUToK3KU9sp512Ivk43mn1Xv2QftBx8E4vyhWeltdiKOJOhMsk6djjoyb8AOuyPVumXTQBuue1yRrTKLAl1SaZnzdrKzpXsI8OBpnI0bjFxA2SNnU/iD0R
DEV_PUBLIC_KEY=MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkpa+qMXS6O11x7jBGo9W3yxeHEsAdyDE40UoXhoQf9K6attSIclTZMEGfq6gmJm2BogVJtPkjvri5/j9mBntA8qKMzzanSQaBEbr8FyByHnf226dsLt1RbJSMLjCd3UC1n0Yq8KKvfHhvmvVbGcWfpgfo7iQTVmL0r1eQxzgnSq31EL1yYNMuaZjpHmQuT24Hmxl9W9enRtJyVTUhwKhtjOSOsR03sMnsckpFT9pn1/V9BE2Kf3rFGqc6JukXkqK6ZW9mtmGLSq3K+JRRq2w8PVmcbcvTr/adW4EL2yc1qk9Ec4HtiDhtSYd6/ov8xLVkKAQjLVt7Ex3/agRPfPrNwIDAQAB
```

### 2. Cấu hình Production
Để chuyển sang production, uncomment và điền thông tin production:
```env
#PROD=production
#PROD_MOMO_ENDPOINT=https://payment.momo.vn
#PROD_ACCESS_KEY=your_production_access_key
#PROD_PARTNER_CODE=your_production_partner_code
#PROD_SECRET_KEY=your_production_secret_key
#PROD_PRIVATE_KEY=your_production_private_key
#PROD_PUBLIC_KEY=your_production_public_key
```

## Các file chính

### 1. MomoPaymentModel.php
- Xử lý tạo payment request
- Verify payment result
- Log payment transactions
- Gửi request đến MoMo API

### 2. PaymentController.php
- Controller xử lý thanh toán MoMo
- Redirect đến MoMo
- Xử lý return từ MoMo

### 3. momoNotify.php
- Xử lý IPN (Instant Payment Notification) từ MoMo
- Cập nhật trạng thái đơn hàng
- Gửi email xác nhận

### 4. momoReturn.php
- Xử lý khi user quay lại từ MoMo
- Cập nhật trạng thái thanh toán
- Redirect đến trang thành công/thất bại

## Luồng thanh toán

### 1. Tạo thanh toán
```
User chọn MoMo → PaymentController::momo() → MomoPaymentModel::createPaymentRequest() → Redirect đến MoMo
```

### 2. Xử lý kết quả
```
MoMo gửi IPN → momoNotify.php → Verify payment → Cập nhật database → Gửi email
User quay lại → momoReturn.php → Hiển thị kết quả
```

## Test tích hợp

### 1. Chạy file test
Truy cập: `http://localhost/ivymoda/ivymoda_mvc/test_momo.php`

### 2. Test thực tế
1. Tạo đơn hàng test
2. Chọn thanh toán MoMo
3. Kiểm tra log để debug

## Debug

### 1. Kiểm tra log
- PHP error log: `error_log()` trong code
- MoMo transaction log: Bảng `tbl_momo_transaction`

### 2. Các lỗi thường gặp
- **Lỗi cấu hình**: Kiểm tra file .env và config.php
- **Lỗi kết nối**: Kiểm tra internet và cURL
- **Lỗi signature**: Kiểm tra secret key và raw hash
- **Lỗi redirect**: Kiểm tra return URL và notify URL

### 3. Log quan trọng
```php
error_log("MomoPaymentModel initialized - PartnerCode: {$this->partnerCode}");
error_log("MomoPaymentModel::createPaymentRequest - RawHash: $rawHash");
error_log("MomoPaymentModel::createPaymentRequest - Response: " . json_encode($response));
```

## Bảo mật

### 1. Signature verification
- Tất cả request/response đều được verify signature
- Sử dụng HMAC SHA256 với secret key

### 2. HTTPS
- Production phải sử dụng HTTPS
- Test environment có thể dùng HTTP

### 3. IPN Security
- Verify signature trước khi xử lý
- Log tất cả IPN để audit

## Monitoring

### 1. Database
- Bảng `tbl_momo_transaction` lưu tất cả giao dịch
- Bảng `tbl_order` cập nhật trạng thái thanh toán

### 2. Logs
- PHP error log
- Custom payment logs trong database

## Troubleshooting

### 1. Payment không tạo được
- Kiểm tra cấu hình .env
- Kiểm tra kết nối internet
- Kiểm tra log để xem lỗi chi tiết

### 2. IPN không nhận được
- Kiểm tra notify URL có accessible không
- Kiểm tra firewall/security
- Kiểm tra log server

### 3. Return URL không hoạt động
- Kiểm tra return URL trong cấu hình
- Kiểm tra routing trong application

## Liên hệ hỗ trợ

- MoMo Developer: https://developers.momo.vn/
- Documentation: https://developers.momo.vn/#/docs/aio/
- FAQ: https://developers.momo.vn/#/docs/aio/?id=faq
