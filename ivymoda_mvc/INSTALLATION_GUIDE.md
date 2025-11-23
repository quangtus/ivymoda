# 📦 IVY MODA - Hướng Dẫn Cài Đặt

## 🚀 Cài Đặt Tự Động (Khuyến Nghị)

### Bước 1: Clone Project từ GitHub

```bash
cd C:\xampp\htdocs\ivymoda
git clone https://github.com/your-repo/ivymoda_mvc.git
cd ivymoda_mvc
```

### Bước 2: Chạy File Setup

**Windows:**
```bash
# Double-click vào file setup.bat
# HOẶC chạy từ Command Prompt:
setup.bat
```

**Script sẽ tự động:**
- ✅ Kiểm tra PHP và MySQL
- ✅ Cài đặt Composer (nếu chưa có)
- ✅ **Download tất cả dependencies (PHPMailer, etc.)**
- ✅ Tạo file `.env` từ `.env.example`
- ✅ Cấu hình `.htaccess`
- ✅ Tạo thư mục cần thiết
- ✅ Import database

### Bước 3: Khởi động XAMPP

1. Mở **XAMPP Control Panel**
2. Click **Start** cho **Apache**
3. Click **Start** cho **MySQL**

### Bước 4: Truy cập Website

- **Frontend:** http://localhost/ivymoda/ivymoda_mvc/public/
- **Admin:** http://localhost/ivymoda/ivymoda_mvc/public/admin

**Tài khoản Admin mặc định:**
- Username: `admin`
- Password: `admin123`

---

## ⚠️ Xử Lý Lỗi Thường Gặp

### ❌ Lỗi: PHPMailer Not Found

**Triệu chứng:**
```
Warning: include(vendor/phpmailer/phpmailer/src/PHPMailer.php): 
Failed to open stream: No such file or directory
```

**Nguyên nhân:**
- Bạn **CHƯA CHẠY** file `setup.bat`
- Thư mục `vendor/` **KHÔNG CÓ** trong GitHub (bị ignore)
- Dependencies **CHƯA ĐƯỢC DOWNLOAD**

**Giải pháp:**

**Cách 1: Chạy Setup Script (Khuyến nghị)**
```bash
setup.bat
```

**Cách 2: Cài đặt thủ công**
```bash
# 1. Cài đặt Composer (nếu chưa có)
# Download từ: https://getcomposer.org/download/

# 2. Chạy Composer install
composer install

# 3. Kiểm tra kết quả
dir vendor\phpmailer\phpmailer\src
# Phải thấy file PHPMailer.php
```

**Cách 3: Sử dụng PHP Composer**
```bash
php composer.phar install
```

---

### ❌ Lỗi: Setup.bat Tự Tắt Ngay

**Nguyên nhân:**
- Script chạy quá nhanh
- Có lỗi xảy ra nhưng không kịp đọc

**Giải pháp:**

**Cách 1: Chạy từ Command Prompt**
```bash
# Mở Command Prompt (cmd)
cd C:\xampp\htdocs\ivymoda\ivymoda_mvc
setup.bat

# Script sẽ đợi bạn nhấn phím trước khi tắt
```

**Cách 2: Kiểm tra log**
```bash
# Script có pause ở nhiều điểm, kiểm tra:
# - PHP version
# - Composer installation
# - Dependencies download
# - Database import
```

---

### ❌ Lỗi: 404 Not Found

**Nguyên nhân:**
- `.htaccess` không hoạt động
- `mod_rewrite` chưa bật trong Apache

**Giải pháp:**

1. **Bật mod_rewrite:**
```apache
# Mở file: C:\xampp\apache\conf\httpd.conf
# Tìm dòng:
#LoadModule rewrite_module modules/mod_rewrite.so

# Bỏ dấu # ở đầu dòng:
LoadModule rewrite_module modules/mod_rewrite.so

# Restart Apache
```

2. **Kiểm tra AllowOverride:**
```apache
# Trong httpd.conf, tìm:
<Directory "C:/xampp/htdocs">
    AllowOverride All  # ← Phải là "All", không phải "None"
</Directory>
```

---

### ❌ Lỗi: Database Connection Failed

**Giải pháp:**

1. **Kiểm tra MySQL đang chạy:**
   - Mở XAMPP Control Panel
   - MySQL phải có trạng thái **Running**

2. **Kiểm tra file `.env`:**
```env
DB_HOST=localhost
DB_NAME=ivymoda
DB_USER=root
DB_PASS=          # ← Để trống nếu dùng XAMPP mặc định
```

3. **Import database thủ công:**
```bash
# Mở phpMyAdmin: http://localhost/phpmyadmin
# 1. Tạo database tên "ivymoda"
# 2. Import file: ivymoda_final.sql
```

---

## 🛠️ Cài Đặt Thủ Công (Advanced)

Nếu script tự động không hoạt động, làm theo các bước sau:

### 1. Cài Đặt Dependencies

```bash
composer install
```

### 2. Cấu Hình Environment

```bash
copy .env.example .env
# Sửa file .env:
# - DB_HOST, DB_NAME, DB_USER, DB_PASS
# - BASE_URL (http://localhost/ivymoda/ivymoda_mvc/public/)
```

### 3. Tạo Thư Mục

```bash
mkdir public\assets\uploads
mkdir public\assets\uploads\reviews
mkdir logs
```

### 4. Import Database

```bash
# Tạo database
mysql -u root -e "CREATE DATABASE ivymoda CHARACTER SET utf8mb4"

# Import SQL
mysql -u root ivymoda < ivymoda_final.sql
```

### 5. Cấu Hình Permissions (Windows)

```bash
icacls public\assets\uploads /grant Users:(OI)(CI)F /T
icacls logs /grant Users:(OI)(CI)F /T
```

---

## 📋 Checklist Sau Khi Cài Đặt

- [ ] **Dependencies:** Kiểm tra `vendor/phpmailer/phpmailer/src/PHPMailer.php` tồn tại
- [ ] **Environment:** File `.env` đã được tạo và cấu hình đúng
- [ ] **Database:** Database `ivymoda` đã được import thành công
- [ ] **Apache:** mod_rewrite đã được bật
- [ ] **Permissions:** Thư mục `uploads` và `logs` có quyền ghi
- [ ] **Access:** Website chạy được tại http://localhost/...

---

## 🔍 Kiểm Tra Cài Đặt

### Test 1: Kiểm tra PHPMailer
```bash
# Chạy lệnh sau để kiểm tra:
dir vendor\phpmailer\phpmailer\src\PHPMailer.php

# Kết quả phải hiển thị file, KHÔNG được là "File Not Found"
```

### Test 2: Kiểm tra Database
```sql
-- Mở phpMyAdmin
-- Chạy query:
USE ivymoda;
SELECT COUNT(*) FROM tbl_sanpham;
-- Phải có kết quả > 0
```

### Test 3: Kiểm tra Website
```
1. Truy cập: http://localhost/ivymoda/ivymoda_mvc/public/
2. Phải thấy trang chủ IVY Moda
3. KHÔNG được thấy lỗi 404 hoặc 500
```

---

## 📞 Hỗ Trợ

Nếu gặp vấn đề:

1. **Kiểm tra log:** `logs/error.log`
2. **Kiểm tra Apache log:** `C:\xampp\apache\logs\error.log`
3. **Kiểm tra PHP log:** `C:\xampp\php\logs\php_error_log`

**Các lỗi phổ biến:**
- PHPMailer not found → Chạy `composer install`
- 404 error → Bật `mod_rewrite` trong Apache
- Database error → Import lại file SQL

---

## ✅ Hoàn Thành!

Sau khi hoàn tất các bước trên, bạn có thể:

- 🛒 Truy cập trang chủ và mua sắm
- 👨‍💼 Đăng nhập admin và quản lý
- 📧 Gửi email xác nhận đơn hàng
- 💬 Sử dụng chatbot AI

**Happy Coding! 🎉**
