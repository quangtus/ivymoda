# Hệ thống Email IVY Moda - Hướng dẫn sử dụng

## Tổng quan
Hệ thống email của IVY Moda đã được tích hợp đầy đủ với các tính năng:
- Email xác nhận đăng ký tài khoản
- Email xác nhận đơn hàng
- Email thông báo đổi mật khẩu
- Email khuyến mãi hàng loạt
- Quản lý cài đặt email cho khách hàng

## Cài đặt

### 1. Cấu hình SMTP
Cập nhật file `.env` với thông tin SMTP của bạn:

```env
SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_PORT=587
SMTP_SECURE=tls
SMTP_FROM_EMAIL=noreply@ivymoda.com
SMTP_FROM_NAME=IVY Moda
BASE_URL=http://localhost/ivymoda_mvc/public
```

### 2. Cập nhật Database
Nếu bạn đã có database cũ, chạy migration:

```sql
-- Chạy file migration
mysql -u root -p ivymoda < migration_add_email_settings.sql
```

Hoặc import database mới:
```sql
mysql -u root -p < ivymoda_final.sql
```

## Tính năng

### 1. Email tự động
- **Đăng ký tài khoản**: Gửi email xác nhận khi user đăng ký
- **Đặt hàng**: Gửi email xác nhận đơn hàng (COD và Momo)
- **Đổi mật khẩu**: Gửi email thông báo khi đổi mật khẩu thành công
- **Quên mật khẩu**: Gửi email link đặt lại mật khẩu

### 2. Email khuyến mãi
- Admin có thể gửi email khuyến mãi hàng loạt
- Chỉ gửi cho khách hàng đã đồng ý nhận email khuyến mãi
- Giới hạn 100 email/phút để tránh spam

### 3. Quản lý email cho khách hàng
- Khách hàng có thể quản lý cài đặt email tại `/user/emailSettings`
- Xem lịch sử email đã nhận
- Bật/tắt thông báo email và email khuyến mãi

## Cấu trúc Database

### Bảng users (cập nhật)
```sql
ALTER TABLE `users` 
ADD COLUMN `email_notifications` tinyint(1) DEFAULT 1,
ADD COLUMN `promotion_emails` tinyint(1) DEFAULT 1;
```

### Bảng email templates
```sql
CREATE TABLE `tbl_email_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`template_id`)
);
```

### Bảng email logs
```sql
CREATE TABLE `tbl_email_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `status` enum('sent','failed') DEFAULT 'sent',
  `sent_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
);
```

## API và Controllers

### Frontend Controllers
- `AuthController`: Xử lý email đăng ký và đặt lại mật khẩu
- `CheckoutController`: Gửi email xác nhận đơn hàng
- `UserController`: Quản lý cài đặt email và thông báo đổi mật khẩu

### Admin Controllers
- `EmailController`: Quản lý template, gửi email khuyến mãi, xem logs

### Models
- `EmailModel`: Xử lý database operations cho email
- `EmailHelper`: Xử lý logic gửi email và template

## Template Email

### Biến có thể sử dụng trong template:
- `{username}`: Tên người dùng
- `{email}`: Email người dùng
- `{activation_link}`: Link kích hoạt tài khoản
- `{reset_link}`: Link đặt lại mật khẩu
- `{order_code}`: Mã đơn hàng
- `{customer_name}`: Tên khách hàng
- `{order_total}`: Tổng tiền đơn hàng
- `{order_date}`: Ngày đặt hàng
- `{customer_address}`: Địa chỉ giao hàng
- `{payment_method}`: Phương thức thanh toán
- `{order_items}`: Danh sách sản phẩm trong đơn hàng

## Testing

### Logs
Xem logs email tại `/admin/email/logs` để theo dõi:
- Email đã gửi thành công
- Email gửi thất bại và lý do
- Thống kê email

## Bảo mật

### Rate Limiting
- Giới hạn 100 email/phút cho email khuyến mãi
- Delay 60 giây sau mỗi 100 email

### Privacy
- Chỉ gửi email cho user đã đồng ý
- Không lưu nội dung email nhạy cảm
- Log chỉ lưu thông tin cơ bản

## Troubleshooting

### Email không gửi được
1. Kiểm tra cấu hình SMTP trong `.env`
2. Kiểm tra logs tại `/admin/email/logs`

### Template không hiển thị đúng
1. Kiểm tra biến trong template có đúng format `{variable_name}`
2. Kiểm tra template có tồn tại trong database
3. Kiểm tra encoding UTF-8

### User không nhận được email
1. Kiểm tra user có bật email notifications không
2. Kiểm tra email có trong spam folder
3. Kiểm tra địa chỉ email có đúng không

## Support
Nếu gặp vấn đề, vui lòng kiểm tra:
1. Logs email tại `/admin/email/logs`
2. Logs server tại `logs/email.log`
3. Cấu hình SMTP trong `.env`
