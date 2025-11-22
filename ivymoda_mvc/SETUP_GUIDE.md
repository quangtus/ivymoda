# Hướng Dẫn Cài Đặt IVY Moda với XAMPP

## Yêu Cầu Hệ Thống

- Windows 7/8/10/11
- XAMPP (PHP 7.4+ và MySQL)
- Kết nối Internet (để tải Composer dependencies)

## Các Bước Cài Đặt

### 1. Cài Đặt XAMPP

1. Tải XAMPP từ: https://www.apachefriends.org/
2. Cài đặt XAMPP (khuyến nghị: `C:\xampp`)
3. Khởi động XAMPP Control Panel
4. Start Apache và MySQL services

### 2. Clone Repository

```bash
git clone <repository-url>
cd ivymoda/ivymoda_mvc
```

### 3. Chạy Setup Script

1. Mở Command Prompt hoặc PowerShell
2. Di chuyển đến thư mục dự án:
   ```cmd
   cd C:\xampp\htdocs\ivymoda\ivymoda_mvc
   ```
3. Chạy file setup:
   ```cmd
   setup.bat
   ```

### 4. Script Sẽ Tự Động:

- ✅ Kiểm tra PHP và các extension cần thiết
- ✅ Cài đặt Composer (nếu chưa có)
- ✅ Cài đặt PHP dependencies (PHPMailer)
- ✅ Tự động phát hiện đường dẫn dự án
- ✅ Tạo file `.env` từ `.env.example`
- ✅ Cập nhật BASE_URL trong `.env` và `.htaccess`
- ✅ Tạo các thư mục cần thiết (uploads, logs)
- ✅ Thiết lập quyền truy cập
- ✅ Tạo database `ivymoda`
- ✅ Import file SQL `ivymoda_final.sql`

### 5. Kiểm Tra Cài Đặt

Sau khi script chạy xong:

1. **Đảm bảo XAMPP đang chạy:**
   - Apache: ✅ Running
   - MySQL: ✅ Running

2. **Truy cập ứng dụng:**
   - Frontend: `http://localhost/[your-project-path]/public/`
   - Admin: `http://localhost/[your-project-path]/public/admin`

### 6. Cấu Hình Bổ Sung (Tùy Chọn)

#### Cấu hình Email (trong file `.env`):
```env
SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_PORT=587
```

#### Cấu hình Gemini AI (cho Chatbot):
```env
GEMINI_API_KEY=your-gemini-api-key
```

## Xử Lý Lỗi

### Lỗi: "PHP is not installed"
- Đảm bảo XAMPP đã được cài đặt
- Hoặc thêm PHP vào PATH của Windows

### Lỗi: "Cannot connect to MySQL"
- Mở XAMPP Control Panel
- Click "Start" cho MySQL service
- Chạy lại `setup.bat`

### Lỗi: "404 Not Found"
- Kiểm tra Apache mod_rewrite đã được bật
- Kiểm tra file `.htaccess` có tồn tại
- Kiểm tra BASE_URL trong file `.env`

### Lỗi: "Database connection failed"
- Kiểm tra MySQL đang chạy
- Kiểm tra thông tin database trong `.env`:
  ```env
  DB_HOST=localhost
  DB_NAME=ivymoda
  DB_USER=root
  DB_PASS=
  ```

## Cấu Hình Virtual Host (Tùy Chọn)

Để sử dụng domain tùy chỉnh (ví dụ: `ivymoda.local`):

1. Mở file `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Thêm cấu hình:
   ```apache
   <VirtualHost *:80>
       ServerName ivymoda.local
       DocumentRoot "C:/xampp/htdocs/ivymoda/ivymoda_mvc/public"
       <Directory "C:/xampp/htdocs/ivymoda/ivymoda_mvc/public">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
3. Mở file `C:\Windows\System32\drivers\etc\hosts` (với quyền Admin)
4. Thêm dòng:
   ```
   127.0.0.1    ivymoda.local
   ```
5. Restart Apache
6. Truy cập: `http://ivymoda.local`

## Thông Tin Quan Trọng

- **Database Name:** `ivymoda`
- **Database User:** `root` (mặc định XAMPP)
- **Database Password:** (để trống - mặc định XAMPP)
- **Project Path:** Tự động phát hiện từ vị trí thư mục

## Hỗ Trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra logs trong thư mục `logs/`
2. Kiểm tra Apache error log: `C:\xampp\apache\logs\error.log`
3. Kiểm tra MySQL error log: `C:\xampp\mysql\data\mysql_error.log`

