# 🤖 Chatbot FAQ System - UC3.48

## Tổng quan

Hệ thống Chatbot FAQ được phát triển theo Use Case 3.48 - "Chatbot hướng dẫn sử dụng hệ thống (FAQ)". Đây là một widget tương tác cho phép khách hàng tìm kiếm và xem các câu hỏi thường gặp ngay trên website.

## ✨ Tính năng chính

### 🎯 Cho Khách hàng
- **Widget tương tác**: Chatbot xuất hiện ở góc dưới bên phải
- **Tìm kiếm FAQ**: Tìm kiếm câu hỏi theo từ khóa
- **Lọc theo danh mục**: Xem FAQ theo từng danh mục
- **Giao diện responsive**: Hoạt động tốt trên desktop và mobile
- **Tích hợp HTML**: Hỗ trợ link và format trong câu trả lời

### 🔧 Cho Admin
- **Quản lý FAQ**: Thêm, sửa, xóa FAQ
- **Phân loại**: Tạo danh mục FAQ
- **Sắp xếp**: Thiết lập thứ tự hiển thị
- **Trạng thái**: Bật/tắt FAQ
- **Thống kê**: Xem số liệu FAQ

## 🏗️ Kiến trúc hệ thống

### Database Schema
```sql
-- Bảng FAQ chính
CREATE TABLE `tbl_chatbot_faq` (
  `faq_id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `help_link` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`faq_id`)
);
```

### File Structure
```
ivymoda_mvc/
├── app/
│   ├── controllers/
│   │   ├── admin/ChatbotController.php      # Admin quản lý FAQ
│   │   └── frontend/ChatbotController.php  # Frontend API
│   ├── models/
│   │   └── ChatbotFaqModel.php             # Model xử lý FAQ
│   └── views/
│       ├── admin/chatbot/                   # Admin views
│       └── frontend/chatbot/                # Frontend views
├── public/
│   ├── ajax/chatbot_ajax.php               # AJAX endpoint
│   ├── assets/
│   │   ├── css/chatbot.css                 # Chatbot styles
│   │   └── js/chatbot.js                   # Chatbot widget
│   └── test-chatbot.html                   # Test page
└── CHATBOT_FAQ_GUIDE.md                    # Documentation
```

## 🚀 Cài đặt và sử dụng

### 1. Database Setup
```sql
-- Import database schema từ ivymoda_final.sql
-- Bảng tbl_chatbot_faq đã được tạo sẵn với dữ liệu mẫu
```

### 2. Admin Panel
Truy cập: `http://localhost/ivymoda/ivymoda_mvc/public/admin/chatbot`

**Quản lý FAQ:**
- ✅ Xem danh sách FAQ
- ✅ Thêm FAQ mới
- ✅ Sửa FAQ
- ✅ Xóa FAQ
- ✅ Bật/tắt trạng thái
- ✅ Lọc theo danh mục
- ✅ Tìm kiếm FAQ

### 3. Frontend Widget
Widget tự động xuất hiện trên tất cả trang frontend.

**Tính năng:**
- 🎯 Click để mở/đóng chatbot
- 🔍 Tìm kiếm FAQ theo từ khóa
- 📁 Lọc theo danh mục
- 📱 Responsive design
- ⚡ AJAX loading

## 📋 API Endpoints

### 1. Lấy danh sách FAQ
```
GET /ajax/chatbot_ajax.php?action=get_faqs
GET /ajax/chatbot_ajax.php?action=get_faqs&category=Đăng ký
```

**Response:**
```json
{
  "success": true,
  "faqs": [
    {
      "id": 1,
      "question": "Làm thế nào để đăng ký tài khoản?",
      "answer": "<p>Để đăng ký tài khoản...</p>",
      "category": "Đăng ký & Đăng nhập",
      "display_order": 1,
      "help_link": null
    }
  ],
  "total": 10
}
```

### 2. Tìm kiếm FAQ
```
GET /ajax/chatbot_ajax.php?action=search_faqs&keyword=đăng ký
```

### 3. Lấy FAQ theo ID
```
GET /ajax/chatbot_ajax.php?action=get_faq_by_id&id=1
```

### 4. Lấy danh mục
```
GET /ajax/chatbot_ajax.php?action=get_categories
```

## 🎨 Customization

### CSS Variables
```css
:root {
  --chatbot-primary: #667eea;
  --chatbot-secondary: #764ba2;
  --chatbot-text: #333;
  --chatbot-bg: #f8f9fa;
}
```

### JavaScript Configuration
```javascript
// Trong chatbot.js
const chatbotConfig = {
    position: 'bottom-right',    // bottom-right, bottom-left
    autoOpen: false,            // Tự động mở
    maxFaqs: 10,               // Số FAQ tối đa
    searchMinLength: 2,        // Độ dài tối thiểu tìm kiếm
    theme: 'light'             // light, dark, auto
};
```

## 🧪 Testing

### Test Page
Truy cập: `http://localhost/ivymoda/ivymoda_mvc/public/test-chatbot.html`

**Test Cases:**
1. ✅ Admin panel hoạt động
2. ✅ Thêm/sửa/xóa FAQ
3. ✅ Frontend widget hiển thị
4. ✅ Tìm kiếm hoạt động
5. ✅ Lọc danh mục
6. ✅ Responsive design
7. ✅ API endpoints

### Manual Testing
1. **Admin Panel:**
   - Đăng nhập admin
   - Truy cập `/admin/chatbot`
   - Thêm FAQ mới
   - Kiểm tra hiển thị

2. **Frontend Widget:**
   - Truy cập trang chủ
   - Click chatbot button
   - Test tìm kiếm
   - Test lọc danh mục

## 🔧 Troubleshooting

### Lỗi thường gặp

**1. Chatbot không hiển thị**
```javascript
// Kiểm tra BASE_URL
console.log('BASE_URL:', window.BASE_URL);

// Kiểm tra script loading
console.log('Chatbot script loaded:', typeof ChatbotWidget);
```

**2. API không hoạt động**
```bash
# Kiểm tra file tồn tại
ls -la public/ajax/chatbot_ajax.php

# Kiểm tra permissions
chmod 644 public/ajax/chatbot_ajax.php
```

**3. Database connection**
```php
// Kiểm tra trong ChatbotFaqModel.php
$db = new Database();
$result = $db->query("SELECT COUNT(*) FROM tbl_chatbot_faq");
```

### Debug Mode
```javascript
// Bật debug mode
localStorage.setItem('chatbot_debug', 'true');

// Xem logs
console.log('Chatbot debug info:', window.chatbotDebug);
```

## 📊 Performance

### Optimization Tips
1. **Database Indexing:**
   ```sql
   CREATE INDEX idx_faq_status_order ON tbl_chatbot_faq(status, display_order);
   CREATE INDEX idx_faq_category ON tbl_chatbot_faq(category);
   ```

2. **Caching:**
   ```php
   // Cache FAQ data
   $cache_key = 'chatbot_faqs_' . md5($category . $status);
   $faqs = $cache->get($cache_key);
   ```

3. **CDN:**
   ```html
   <!-- Sử dụng CDN cho Font Awesome -->
   <script src="https://kit.fontawesome.com/54f0cb7e4a.js"></script>
   ```

## 🔒 Security

### Input Validation
```php
// Trong ChatbotController.php
$question = trim($_POST['question']);
$answer = trim($_POST['answer']);

// Validate input
if (empty($question) || empty($answer)) {
    throw new Exception('Invalid input');
}
```

### XSS Prevention
```php
// Escape output
echo htmlspecialchars($faq->question);
echo htmlspecialchars($faq->answer);
```

### CSRF Protection
```php
// Thêm CSRF token
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;
```

## 📈 Analytics

### Tracking Events
```javascript
// Track chatbot usage
function trackChatbotEvent(event, data) {
    // Google Analytics
    gtag('event', event, {
        'event_category': 'Chatbot',
        'event_label': data
    });
}
```

### Metrics
- FAQ views
- Search queries
- Category clicks
- User engagement

## 🚀 Future Enhancements

### Planned Features
1. **AI Integration:** Tích hợp Gemini AI
2. **Multi-language:** Hỗ trợ đa ngôn ngữ
3. **Analytics:** Dashboard thống kê
4. **Templates:** FAQ templates
5. **Export/Import:** Backup FAQ

### Roadmap
- [ ] Q1 2025: AI Integration
- [ ] Q2 2025: Multi-language
- [ ] Q3 2025: Advanced Analytics
- [ ] Q4 2025: Mobile App

## 📞 Support

### Contact
- **Email:** support@ivymoda.com
- **Hotline:** 0901234567
- **Documentation:** [CHATBOT_FAQ_GUIDE.md](./CHATBOT_FAQ_GUIDE.md)

### Resources
- [Use Case 3.48](./UC_chatbot.txt)
- [Database Schema](./ivymoda_final.sql)
- [Test Page](./public/test-chatbot.html)

---

**Version:** 1.0.0  
**Last Updated:** <?php echo date('Y-m-d'); ?>  
**Author:** IVY Moda Development Team