# 🛍️ IVY MODA - E-Commerce Platform

> Website bán quần áo thời trang với kiến trúc MVC tùy chỉnh, tích hợp AI Chatbot, thanh toán MoMo và Email marketing.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

---

## 📋 Mục Lục

- [Tính Năng Chính](#-tính-năng-chính)
- [Công Nghệ Sử Dụng](#-công-nghệ-sử-dụng)
- [Yêu Cầu Hệ Thống](#-yêu-cầu-hệ-thống)
- [Cài Đặt Nhanh](#-cài-đặt-nhanh)
- [Hướng Dẫn Cài Đặt Chi Tiết](#-hướng-dẫn-cài-đặt-chi-tiết)
- [Cấu Hình](#-cấu-hình)
- [Cấu Trúc Dự Án](#-cấu-trúc-dự-án)
- [Sử Dụng](#-sử-dụng)
- [Tài Khoản Demo](#-tài-khoản-demo)
- [Troubleshooting](#-troubleshooting)

---

## ✨ Tính Năng Chính

### 🎯 Frontend (Khách Hàng)
- ✅ **Xem & Tìm Kiếm Sản Phẩm** - Lọc theo danh mục, giá, màu sắc, size
- 🛒 **Giỏ Hàng Thông Minh** - Session-based cart với AJAX
- 💳 **Thanh Toán MoMo** - Tích hợp MoMo Payment Gateway
- 🤖 **AI Chatbot** - Hỗ trợ khách hàng 24/7 với Google Gemini AI
- ⭐ **Đánh Giá & Nhận Xét** - Review sản phẩm với rating
- 👤 **Quản Lý Tài Khoản** - Profile, đơn hàng, lịch sử mua hàng
- 📧 **Email Notification** - Xác nhận đơn hàng, trạng thái giao hàng

### 🔐 Admin Panel
- 📊 **Dashboard** - Thống kê doanh thu, đơn hàng, sản phẩm
- 🏷️ **Quản Lý Sản Phẩm** - CRUD products với upload nhiều ảnh
- 📁 **Quản Lý Danh Mục** - Hierarchical categories
- 📦 **Quản Lý Đơn Hàng** - Cập nhật trạng thái, xuất báo cáo
- 👥 **Quản Lý Người Dùng** - User roles & permissions
- 🎁 **Khuyến Mãi & Giảm Giá** - Discount codes, promotions
- 💬 **Quản Lý Chatbot** - FAQ management, training data
- 📈 **Báo Cáo & Thống Kê** - Sales reports, analytics

---

## 🚀 Công Nghệ Sử Dụng

### Backend
- **PHP 7.4+** - Custom MVC Framework
- **MySQL 8.0+** - Database với PDO
- **Composer** - Dependency management
- **PHPMailer 6.8** - Email service

### Frontend
- **HTML5/CSS3** - Responsive UI
- **JavaScript (ES6+)** - Dynamic interactions
- **jQuery** - AJAX requests
- **Bootstrap 5** - UI Framework

### Integrations
- **Google Gemini AI** - Chatbot intelligence
- **MoMo Payment API** - Online payment
- **SMTP (Gmail)** - Email notifications

### Architecture
- **Custom MVC Pattern** - Separation of concerns
- **RESTful-style Routing** - Clean URLs
- **Session Management** - Secure authentication
- **PDO with Prepared Statements** - SQL injection prevention

---

## 💻 Yêu Cầu Hệ Thống

### Bắt Buộc
- **PHP**: >= 7.4 (khuyến nghị 8.0+)
- **MySQL**: >= 5.7 hoặc MariaDB >= 10.2
- **Composer**: Latest version
- **Web Server**: Apache 2.4+ hoặc Nginx
- **PHP Extensions**:
  - `pdo_mysql`
  - `mbstring`
  - `openssl`
  - `curl`
  - `gd` (image processing)
  - `fileinfo`

### Khuyến Nghị
- **OS**: Windows 10/11, Ubuntu 20.04+, macOS 12+
- **RAM**: >= 2GB
- **Disk Space**: >= 500MB

### Development Tools
- **XAMPP** / **WAMP** / **MAMP** (cho Windows/Mac)
- **Git** - Version control
- **VS Code** - Code editor (khuyến nghị)

---

## ⚡ Cài Đặt Nhanh

### Option 1: Auto Setup Script (Windows)

```batch
# 1. Clone repository
git clone https://github.com/quangtus/ivymoda.git
cd ivymoda/ivymoda_mvc

# 2. Chạy script tự động
setup.bat

# 3. Import database (script sẽ hướng dẫn)
# Truy cập: http://localhost/ivymoda/ivymoda_mvc/public
```

### Option 2: Manual Setup (Tất Cả OS)

```bash
# 1. Clone repository
git clone https://github.com/quangtus/ivymoda.git
cd ivymoda/ivymoda_mvc

# 2. Install dependencies
composer install

# 3. Copy environment file
copy .env.example .env   # Windows
# cp .env.example .env   # Linux/Mac

# 4. Import database
# Mở phpMyAdmin → Import file: ivymoda_final.sql

# 5. Start server
php -S localhost:8000 -t public
# Hoặc cấu hình Apache virtual host
```

---

## 📖 Hướng Dẫn Cài Đặt Chi Tiết

### **Bước 1: Cài Đặt Môi Trường**

#### Windows (XAMPP)
```batch
# Download XAMPP từ https://www.apachefriends.org/
# Cài đặt XAMPP vào C:\xampp
# Start Apache và MySQL từ XAMPP Control Panel
```

#### macOS (MAMP)
```bash
# Download MAMP từ https://www.mamp.info/
# Hoặc dùng Homebrew:
brew install php@8.0 mysql composer
```

#### Linux (Ubuntu/Debian)
```bash
# Update package list
sudo apt update

# Install Apache, PHP, MySQL
sudo apt install apache2 php8.0 mysql-server php8.0-mysql php8.0-mbstring php8.0-xml php8.0-curl php8.0-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

### **Bước 2: Clone Project**

```bash
# Di chuyển vào thư mục web root
cd C:\xampp\htdocs          # Windows XAMPP
# cd /Applications/MAMP/htdocs  # macOS MAMP
# cd /var/www/html             # Linux

# Clone repository
git clone https://github.com/quangtus/ivymoda.git
cd ivymoda/ivymoda_mvc
```

---

### **Bước 3: Cài Đặt Dependencies**

```bash
# Cài đặt Composer dependencies
composer install

# Nếu gặp lỗi, thử:
composer update
composer dump-autoload
```

---

### **Bước 4: Tạo Database**

#### Option A: Qua phpMyAdmin (Dễ nhất)

1. Mở trình duyệt: `http://localhost/phpmyadmin`
2. Click **New** → Tạo database tên `ivymoda`
3. Chọn database `ivymoda` → Click **Import**
4. Chọn file `ivymoda_final.sql` → Click **Go**

#### Option B: Command Line

```bash
# Login MySQL
mysql -u root -p

# Tạo database
CREATE DATABASE ivymoda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import SQL file
USE ivymoda;
SOURCE /path/to/ivymoda_final.sql;

# Kiểm tra
SHOW TABLES;
EXIT;
```

---

### **Bước 5: Cấu Hình Environment**

```bash
# Copy file environment
copy .env.example .env   # Windows
# cp .env.example .env   # Linux/Mac
```

#### Chỉnh sửa file `.env`:

```bash
# ===== DATABASE =====
DB_HOST=localhost
DB_NAME=ivymoda
DB_USER=root
DB_PASS=           # Để trống nếu không có password

# ===== APPLICATION =====
BASE_URL=http://localhost/ivymoda/ivymoda_mvc/public/

# ===== EMAIL (Gmail) =====
SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password   # Lấy từ Google App Passwords
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME=IVY Moda

# ===== GEMINI AI (Optional) =====
GEMINI_API_KEY=your-api-key   # Lấy từ https://makersuite.google.com/app/apikey
```

**📧 Lấy Gmail App Password:**
1. Truy cập: https://myaccount.google.com/apppasswords
2. Chọn app: **Mail**, device: **Other**
3. Copy password 16 ký tự vào `SMTP_PASSWORD`

**🤖 Lấy Gemini API Key (Chatbot):**
1. Truy cập: https://makersuite.google.com/app/apikey
2. Click **Create API Key**
3. Copy key vào `GEMINI_API_KEY`

---

### **Bước 6: Cấu Hình Web Server**

#### Option A: PHP Built-in Server (Development)

```bash
cd ivymoda_mvc
php -S localhost:8000 -t public

# Truy cập: http://localhost:8000
```

#### Option B: Apache Virtual Host (Production)

**Windows XAMPP:**

Chỉnh sửa `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName ivymoda.local
    DocumentRoot "C:/xampp/htdocs/ivymoda/ivymoda_mvc/public"
    
    <Directory "C:/xampp/htdocs/ivymoda/ivymoda_mvc/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/ivymoda-error.log"
    CustomLog "logs/ivymoda-access.log" common
</VirtualHost>
```

Chỉnh sửa `C:\Windows\System32\drivers\etc\hosts` (Run as Administrator):

```
127.0.0.1    ivymoda.local
```

Restart Apache → Truy cập: `http://ivymoda.local`

---

### **Bước 7: Kiểm Tra Cài Đặt**

```bash
# Test database connection
php -r "require 'vendor/autoload.php'; require 'app/core/Database.php'; echo 'DB OK';"

# Test web server
curl http://localhost:8000

# Check file permissions (Linux/Mac)
chmod -R 755 public/assets/uploads
chmod -R 755 logs
```

---

## ⚙️ Cấu Hình

### Cấu Hình Database (`config/database.php`)

```php
<?php
// Load từ .env file
return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'name' => getenv('DB_NAME') ?: 'ivymoda',
    'user' => getenv('DB_USER') ?: 'root',
    'pass' => getenv('DB_PASS') ?: '',
    'charset' => 'utf8mb4'
];
```

### Cấu Hình App (`config/config.php`)

```php
<?php
// Base URL - QUAN TRỌNG!
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost/ivymoda/ivymoda_mvc/public/');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/public/assets/uploads/');
define('LOG_PATH', ROOT_PATH . '/logs/');

// Session
define('SESSION_TIMEOUT', 3600); // 1 hour
```

### Cấu Hình MoMo Payment

File `.env`:
```bash
# Sử dụng test credentials (đã có sẵn)
DEV=development
DEV_MOMO_ENDPOINT=https://test-payment.momo.vn/v2/gateway/api/create
DEV_PARTNER_CODE=MOMO
DEV_ACCESS_KEY=F8BBA842ECF85
# ... (các keys khác)
```

**⚠️ Lưu ý:** Test credentials chỉ dùng cho development. Production cần đăng ký tại https://business.momo.vn

---

## 📂 Cấu Trúc Dự Án

```
ivymoda_mvc/
│
├── 📁 app/                          # Application logic
│   ├── 📁 controllers/              # Controllers (MVC)
│   │   ├── 📁 admin/                # Admin panel controllers
│   │   │   ├── ProductController.php
│   │   │   ├── OrderController.php
│   │   │   └── ...
│   │   └── 📁 frontend/             # Frontend controllers
│   │       ├── HomeController.php
│   │       ├── ProductController.php
│   │       └── CartController.php
│   │
│   ├── 📁 models/                   # Models (Database)
│   │   ├── ProductModel.php
│   │   ├── UserModel.php
│   │   └── OrderModel.php
│   │
│   ├── 📁 views/                    # Views (HTML/PHP)
│   │   ├── 📁 admin/                # Admin templates
│   │   ├── 📁 frontend/             # Frontend templates
│   │   └── 📁 shared/               # Shared components
│   │
│   ├── 📁 core/                     # Framework core
│   │   ├── App.php                  # Bootstrap
│   │   ├── Router.php               # URL routing
│   │   ├── Controller.php           # Base controller
│   │   ├── Model.php                # Base model
│   │   └── Database.php             # DB connection
│   │
│   ├── 📁 helpers/                  # Helper utilities
│   │   ├── EmailHelper.php          # PHPMailer wrapper
│   │   ├── SessionHelper.php        # Session management
│   │   └── GeminiHelper.php         # AI chatbot
│   │
│   └── 📁 services/                 # Business logic services
│       └── GeminiService.php
│
├── 📁 config/                       # Configuration files
│   ├── config.php                   # App config
│   ├── database.php                 # DB config
│   └── gemini.php                   # AI config
│
├── 📁 public/                       # Public web root (Document Root)
│   ├── index.php                    # Entry point
│   ├── .htaccess                    # Apache rewrite rules
│   │
│   ├── 📁 assets/                   # Static assets
│   │   ├── 📁 css/                  # Stylesheets
│   │   ├── 📁 js/                   # JavaScript
│   │   ├── 📁 images/               # Images
│   │   └── 📁 uploads/              # User uploads (products, avatars)
│   │
│   ├── 📁 ajax/                     # AJAX endpoints
│   │   ├── cart_ajax.php
│   │   └── chatbot_ajax.php
│   │
│   └── 📁 payment/                  # Payment callbacks
│       ├── momoReturn.php
│       └── momoNotify.php
│
├── 📁 vendor/                       # Composer dependencies
│   ├── autoload.php
│   └── phpmailer/
│
├── 📁 logs/                         # Application logs
├── 📁 docs/                         # Documentation
│
├── .env                             # Environment variables (DO NOT COMMIT)
├── .env.example                     # Environment template
├── composer.json                    # PHP dependencies
├── ivymoda_final.sql                # Database schema
├── setup.bat                        # Windows setup script
└── README.md                        # This file
```

### 🔑 Kiến Trúc MVC Flow

```
REQUEST: http://ivymoda.local/product/detail/5
              ↓
┌─────────────────────────────────────────────┐
│ 1. public/index.php (Entry Point)          │
│    - Load autoloader                        │
│    - Initialize App                         │
└─────────────────────────────────────────────┘
              ↓
┌─────────────────────────────────────────────┐
│ 2. app/core/Router.php                      │
│    - Parse URL: ['product', 'detail', '5']  │
│    - Load ProductController                 │
└─────────────────────────────────────────────┘
              ↓
┌─────────────────────────────────────────────┐
│ 3. app/controllers/frontend/               │
│    ProductController.php                    │
│    - Call detail(5) method                  │
└─────────────────────────────────────────────┘
              ↓
┌─────────────────────────────────────────────┐
│ 4. app/models/ProductModel.php              │
│    - Query database for product ID 5        │
│    - Return product data                    │
└─────────────────────────────────────────────┘
              ↓
┌─────────────────────────────────────────────┐
│ 5. app/views/frontend/product/detail.php   │
│    - Render HTML with product data          │
└─────────────────────────────────────────────┘
              ↓
        RESPONSE (HTML)
```

---

## 🎮 Sử Dụng

### Truy Cập Ứng Dụng

```
Frontend (Khách hàng):  http://localhost:8000
Admin Panel:            http://localhost:8000/admin
```

### Tài Khoản Demo

#### 👤 Admin Account
```
URL:      http://localhost:8000/admin
Email:    admin@ivymoda.com
Password: admin123
```

#### 👥 Customer Account
```
URL:      http://localhost:8000
Email:    customer@example.com
Password: customer123
```

### Chức Năng Chính

#### Frontend
1. **Trang Chủ**: `/` - Hiển thị sản phẩm mới, slider, khuyến mãi
2. **Sản Phẩm**: `/product/detail/{id}` - Chi tiết sản phẩm
3. **Danh Mục**: `/category/{id}` - Lọc theo danh mục
4. **Giỏ Hàng**: `/cart` - Quản lý giỏ hàng
5. **Thanh Toán**: `/checkout` - Đặt hàng & thanh toán
6. **Tài Khoản**: `/user/profile` - Quản lý profile
7. **Chatbot**: Widget góc phải màn hình

#### Admin Panel
1. **Dashboard**: `/admin` - Thống kê tổng quan
2. **Sản Phẩm**: `/admin/product` - CRUD products
3. **Đơn Hàng**: `/admin/order` - Quản lý orders
4. **Khách Hàng**: `/admin/user` - Quản lý users
5. **Khuyến Mãi**: `/admin/promotion` - Discount codes
6. **Báo Cáo**: `/admin/report` - Sales reports
7. **Chatbot FAQ**: `/admin/chatbot` - Quản lý Q&A

---

## 🧪 Testing

### Test Database Connection

```bash
php -r "require 'vendor/autoload.php'; require 'config/database.php'; echo 'OK';"
```

### Test Email Sending

```php
// Tạo file test-email.php trong root
<?php
require 'vendor/autoload.php';
require 'app/helpers/EmailHelper.php';

$result = EmailHelper::send(
    'your-email@gmail.com',
    'Test Email',
    '<h1>Email đang hoạt động!</h1>'
);

echo $result ? 'Email sent successfully!' : 'Email failed!';
```

### Test Chatbot API

```bash
curl -X POST http://localhost:8000/ajax/chatbot_ajax.php \
  -H "Content-Type: application/json" \
  -d '{"message": "Xin chào"}'
```

---

## 🐛 Troubleshooting

### ❌ Lỗi "Database connection failed"

**Nguyên nhân:** Sai thông tin database trong `.env`

**Giải pháp:**
```bash
# Kiểm tra MySQL đang chạy
# Windows: Mở XAMPP Control Panel → Start MySQL
# Linux: sudo systemctl start mysql

# Test connection
mysql -u root -p -e "SHOW DATABASES;"

# Kiểm tra file .env
cat .env | grep DB_
```

---

### ❌ Lỗi "404 Not Found" hoặc CSS không load

**Nguyên nhân:** Sai `BASE_URL` trong `.env`

**Giải pháp:**
```bash
# Chỉnh BASE_URL trong .env
BASE_URL=http://localhost:8000/   # PHP built-in server
# hoặc
BASE_URL=http://localhost/ivymoda/ivymoda_mvc/public/   # XAMPP
```

---

### ❌ Lỗi "Class not found"

**Nguyên nhân:** Composer chưa cài hoặc autoload chưa update

**Giải pháp:**
```bash
composer install
composer dump-autoload
```

---

### ❌ Lỗi "Permission denied" upload ảnh

**Nguyên nhân:** Thư mục uploads không có quyền ghi

**Giải pháp (Linux/Mac):**
```bash
chmod -R 755 public/assets/uploads
chown -R www-data:www-data public/assets/uploads  # Ubuntu/Debian
```

**Giải pháp (Windows):**
- Right-click folder `uploads` → Properties → Security
- Add quyền **Full Control** cho user hiện tại

---

### ❌ Email không gửi được

**Nguyên nhân:** Sai SMTP config hoặc Gmail chặn

**Giải pháp:**
```bash
# 1. Enable "Less secure app access" (Gmail cũ)
# 2. Hoặc dùng App Password (Gmail mới):
#    https://myaccount.google.com/apppasswords

# 3. Kiểm tra .env
SMTP_USERNAME=your-real-email@gmail.com
SMTP_PASSWORD=your-16-char-app-password
SMTP_PORT=587
SMTP_SECURE=tls
```

---

### ❌ Chatbot không hoạt động

**Nguyên nhân:** Thiếu Gemini API key hoặc key không hợp lệ

**Giải pháp:**
```bash
# Lấy API key mới
# https://makersuite.google.com/app/apikey

# Cập nhật .env
GEMINI_API_KEY=your-new-api-key

# Test API
curl https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=YOUR_KEY \
  -H 'Content-Type: application/json' \
  -d '{"contents":[{"parts":[{"text":"Hello"}]}]}'
```

---

### ❌ Apache không start (Port 80 bị chiếm)

**Giải pháp (Windows):**
```batch
# Kiểm tra process chiếm port 80
netstat -ano | findstr :80

# Kill process (thay PID)
taskkill /PID <PID> /F

# Hoặc đổi port Apache
# Sửa httpd.conf: Listen 8080
```

---

## 📝 Development Notes

### Thêm Controller Mới

```php
// 1. Tạo file: app/controllers/frontend/TestController.php
<?php
class TestController extends Controller {
    public function index() {
        $this->view('test/index', ['title' => 'Test Page']);
    }
}

// 2. Tạo view: app/views/frontend/test/index.php
<h1><?= $title ?></h1>

// 3. Truy cập: http://localhost:8000/test
```

### Thêm Model Mới

```php
// app/models/TestModel.php
<?php
class TestModel extends Model {
    protected $table = 'test_table';
    
    public function getAll() {
        return $this->db->query("SELECT * FROM {$this->table}")->fetchAll();
    }
}
```

### AJAX Endpoint Pattern

```php
// public/ajax/test_ajax.php
<?php
session_start();
require_once '../../vendor/autoload.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'get_data':
        echo json_encode(['success' => true, 'data' => []]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
```

---

## 🤝 Contributing

Nếu bạn muốn đóng góp:

1. Fork repository
2. Tạo feature branch: `git checkout -b feature/AmazingFeature`
3. Commit changes: `git commit -m 'Add some AmazingFeature'`
4. Push to branch: `git push origin feature/AmazingFeature`
5. Open Pull Request

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

## 👨‍💻 Author

**Quang Tus**
- GitHub: [@quangtus](https://github.com/quangtus)
- Project Link: [https://github.com/quangtus/ivymoda](https://github.com/quangtus/ivymoda)

---

## 🙏 Acknowledgments

- [PHPMailer](https://github.com/PHPMailer/PHPMailer) - Email library
- [Google Gemini AI](https://ai.google.dev/) - Chatbot intelligence
- [MoMo Payment](https://developers.momo.vn/) - Payment gateway
- [Bootstrap](https://getbootstrap.com/) - UI framework

---

## 📞 Support

Nếu gặp vấn đề:

1. Check [Troubleshooting](#-troubleshooting) section
2. Search [Issues](https://github.com/quangtus/ivymoda/issues)
3. Open new issue với:
   - PHP version: `php -v`
   - MySQL version: `mysql --version`
   - Error logs: `logs/error.log`
   - Screenshot (nếu có)

---

**⭐ Nếu project này hữu ích, hãy cho một star trên GitHub! ⭐**