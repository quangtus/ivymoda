# 🤖 Chatbot AI System - UC3.47

## Tổng quan

Hệ thống Chatbot AI được phát triển theo Use Case 3.47 - "Chatbot tư vấn sản phẩm với Gemini AI". Đây là một widget tương tác cho phép khách hàng chat với AI để được tư vấn sản phẩm, gợi ý mua sắm và trả lời câu hỏi về sản phẩm.

## ✨ Tính năng chính

### 🎯 Cho Khách hàng
- **Chat với AI**: Tương tác trực tiếp với Gemini AI
- **Tư vấn sản phẩm**: AI phân tích và gợi ý sản phẩm phù hợp
- **Gợi ý thông minh**: Dựa trên sở thích và lịch sử mua sắm
- **Giao diện chat**: Giao diện chat hiện đại, dễ sử dụng
- **Lịch sử hội thoại**: Lưu trữ và hiển thị lịch sử chat
- **Responsive**: Hoạt động tốt trên desktop và mobile

### 🔧 Cho Admin
- **Quản lý cấu hình**: Cấu hình API key, thời gian phản hồi
- **Theo dõi hội thoại**: Xem lịch sử chat của khách hàng
- **Thống kê**: Báo cáo về hiệu suất chatbot
- **Test kết nối**: Kiểm tra kết nối Gemini AI

## 🏗️ Kiến trúc hệ thống

### Database Schema
```sql
-- Bảng lịch sử hội thoại
CREATE TABLE `tbl_chatbot_conversation` (
  `conversation_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) NOT NULL,
  `user_message` text NOT NULL,
  `bot_response` text NOT NULL,
  `context_data` text DEFAULT NULL,
  `suggested_products` text DEFAULT NULL,
  `response_time` int(11) DEFAULT NULL,
  `is_from_faq` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`conversation_id`)
);

-- Bảng cấu hình chatbot
CREATE TABLE `tbl_chatbot_config` (
  `config_id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL,
  `config_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_key` (`config_key`)
);

-- Bảng sở thích người dùng
CREATE TABLE `tbl_user_preferences` (
  `preference_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `favorite_colors` varchar(255) DEFAULT NULL,
  `favorite_categories` varchar(255) DEFAULT NULL,
  `size_preference` varchar(50) DEFAULT NULL,
  `price_range` varchar(100) DEFAULT NULL,
  `skin_tone` varchar(50) DEFAULT NULL,
  `height` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`preference_id`),
  UNIQUE KEY `user_id` (`user_id`)
);
```

### File Structure
```
ivymoda_mvc/
├── app/
│   ├── controllers/
│   │   ├── admin/ChatbotController.php      # Admin quản lý chatbot
│   │   └── frontend/ChatbotController.php  # Frontend API
│   ├── models/
│   │   ├── ChatbotModel.php                # Model xử lý chatbot AI
│   │   └── ChatbotFaqModel.php             # Model xử lý FAQ
│   ├── services/
│   │   └── GeminiService.php               # Service tích hợp Gemini AI
│   └── views/
│       ├── admin/chatbot/                   # Admin views
│       └── frontend/chatbot/                # Frontend views
├── public/
│   ├── ajax/chatbot_ajax.php               # AJAX endpoints
│   ├── assets/js/
│   │   ├── chatbot.js                      # FAQ Chatbot JS
│   │   └── chatbot-ai.js                   # AI Chatbot JS
│   └── test-chatbot-ai.html                # Test page
```

## 🚀 Cài đặt và Cấu hình

### 1. Cấu hình API Key
```sql
-- Cập nhật API key Gemini
UPDATE tbl_chatbot_config 
SET config_value = 'AIzaSyA6RZuA5V6DqAXWcdHMqXgn0Dxe_GEVIak' 
WHERE config_key = 'gemini_api_key';
```

### 2. Cấu hình cơ bản
```sql
-- Các cấu hình mặc định đã được thêm vào database
INSERT INTO tbl_chatbot_config VALUES 
(1, 'gemini_api_key', 'AIzaSyA6RZuA5V6DqAXWcdHMqXgn0Dxe_GEVIak', 'API key của Gemini AI', NOW()),
(2, 'max_products_suggest', '5', 'Số lượng sản phẩm gợi ý tối đa', NOW()),
(3, 'context_max_length', '2000', 'Độ dài context tối đa (ký tự)', NOW()),
(4, 'response_timeout', '3000', 'Thời gian chờ phản hồi tối đa (ms)', NOW()),
(5, 'chatbot_welcome_message', 'Xin chào! Tôi có thể giúp gì cho bạn hôm nay? 😊', 'Lời chào mặc định', NOW()),
(6, 'enable_faq_mode', '1', 'Bật/tắt chế độ FAQ', NOW()),
(7, 'enable_gemini_mode', '1', 'Bật/tắt chế độ Gemini AI', NOW()),
(8, 'chatbot_position', 'bottom-right', 'Vị trí hiển thị chatbot', NOW());
```

## 📱 Sử dụng

### Frontend Integration
```html
<!-- Include trong layout chính -->
<?php include ROOT_PATH . 'app/views/shared/frontend/chatbot.php'; ?>
```

### JavaScript API
```javascript
// Khởi tạo chatbot AI
const chatbotAI = new ChatbotAI({
    position: 'bottom-right',
    autoOpen: false,
    maxMessageLength: 500,
    responseTimeout: 30000,
    baseUrl: window.location.origin + '/ivymoda/ivymoda_mvc/public/'
});

// Gửi tin nhắn
chatbotAI.sendMessage('Tôi muốn tìm áo sơ mi nam');

// Mở/đóng chatbot
chatbotAI.open();
chatbotAI.close();
```

### AJAX Endpoints
```javascript
// Chat với AI
fetch('/ivymoda/ivymoda_mvc/public/ajax/chatbot_ajax.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        action: 'chat_with_ai',
        message: 'Tôi muốn tìm áo sơ mi nam',
        session_id: 'unique_session_id'
    })
});

// Test kết nối
fetch('/ivymoda/ivymoda_mvc/public/ajax/chatbot_ajax.php?action=test_gemini');

// Lấy lịch sử hội thoại
fetch('/ivymoda/ivymoda_mvc/public/ajax/chatbot_ajax.php?action=get_conversation_history&session_id=xxx');
```

## 🧪 Testing

### Test Page
Truy cập: `http://localhost/ivymoda/ivymoda_mvc/public/test-chatbot-ai.html`

### Test Cases
1. **Kết nối Gemini AI**: Kiểm tra API key và kết nối
2. **Chat cơ bản**: Gửi tin nhắn và nhận phản hồi
3. **Gợi ý sản phẩm**: Test chức năng gợi ý sản phẩm
4. **Lịch sử hội thoại**: Kiểm tra lưu trữ và hiển thị lịch sử
5. **Responsive**: Test trên mobile và desktop

### Quick Tests
- "Tôi muốn tìm áo sơ mi nam màu trắng"
- "Gợi ý sản phẩm bán chạy"
- "Tư vấn size phù hợp cho tôi"
- "Có những màu sắc nào?"
- "Giá cả như thế nào?"

## 📊 Monitoring

### Admin Dashboard
- Truy cập: `/admin/chatbot/`
- Xem thống kê: Số lượng hội thoại, thời gian phản hồi
- Quản lý cấu hình: API key, timeout, welcome message
- Xem lịch sử: Danh sách hội thoại của khách hàng

### Logs
- Lịch sử hội thoại được lưu trong `tbl_chatbot_conversation`
- Response time được track cho mỗi tin nhắn
- Context data được lưu để debug và cải thiện

## 🔧 Troubleshooting

### Lỗi thường gặp

1. **"Chatbot AI tạm thời không khả dụng"**
   - Kiểm tra API key Gemini
   - Kiểm tra kết nối internet
   - Test kết nối: `/ajax/chatbot_ajax.php?action=test_gemini`

2. **"Không thể kết nối Gemini AI"**
   - Kiểm tra API key có hợp lệ
   - Kiểm tra quota API
   - Kiểm tra firewall/network

3. **Phản hồi chậm**
   - Tăng `response_timeout` trong config
   - Kiểm tra tốc độ internet
   - Giảm `context_max_length`

### Debug Mode
```javascript
// Bật debug mode
localStorage.setItem('chatbot_debug', 'true');

// Xem logs trong console
console.log('Chatbot AI Debug Mode Enabled');
```

## 🚀 Tối ưu hóa

### Performance
- Giới hạn context length để giảm thời gian phản hồi
- Cache popular products để tăng tốc
- Sử dụng session storage cho lịch sử tạm thời

### User Experience
- Gợi ý tin nhắn nhanh
- Hiển thị typing indicator
- Auto-scroll trong chat
- Responsive design

### Security
- Validate input từ người dùng
- Sanitize output từ AI
- Rate limiting cho API calls
- Session management

## 📈 Roadmap

### V2.0 Features
- [ ] Voice input/output
- [ ] Multi-language support
- [ ] Advanced product filtering
- [ ] Integration với recommendation engine
- [ ] Analytics dashboard
- [ ] A/B testing cho responses

### Integration
- [ ] CRM integration
- [ ] Email marketing
- [ ] Social media
- [ ] Mobile app

## 📞 Support

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra logs trong database
2. Test kết nối Gemini AI
3. Kiểm tra console errors
4. Liên hệ support team

---

**Phiên bản**: 1.0  
**Cập nhật cuối**: 2025-01-14  
**Tương thích**: UC3.47, UC3.48
