# 🔧 Hướng dẫn Sửa Lỗi Chatbot UC3.47

## 📋 Tóm tắt Lỗi

**Lỗi chính:** `Unexpected token '+', '+dr />'` - Lỗi JSON encoding

**Nguyên nhân:**
- Dữ liệu từ database chứa ký tự không hợp lệ UTF-8
- Response từ PHP không được sanitize trước khi JSON encode
- Không kiểm soát output buffering đúng cách

---

## 🔍 Các Sửa Chữa Đã Thực Hiện

### 1. **Sửa `public/ajax/chatbot_ajax.php`**

#### a) Thêm Error Handler
```php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    ob_clean();
    error_log("PHP Error: [$errno] $errstr in $errfile on line $errline");
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $errstr], JSON_UNESCAPED_UNICODE);
    exit;
});
```

#### b) Thêm UTF-8 Support
- Tất cả `json_encode()` thêm flag `JSON_UNESCAPED_UNICODE`
- Ví dụ: `json_encode($data, JSON_UNESCAPED_UNICODE)`

#### c) Thêm Sanitize Functions
```php
function sanitizeArray($array)
function sanitizeValue($value)
```

#### d) Sửa Chat Response
```php
// TRƯỚC: Lỗi JSON
echo json_encode([
    'success' => true,
    'response' => $aiResponse['response'],
    'suggested_products' => $context['products'] ?? []
]);

// SAU: JSON hợp lệ với sanitize
$productsToReturn = [];
if (isset($context['products']) && is_array($context['products'])) {
    foreach ($context['products'] as $product) {
        $productsToReturn[] = sanitizeArray($product);
    }
}

echo json_encode([
    'success' => true,
    'response' => $aiResponse['response'],
    'suggested_products' => $productsToReturn,
    'response_time' => $responseTime
], JSON_UNESCAPED_UNICODE);
```

### 2. **Sửa `app/services/GeminiService.php`**

#### a) Thêm Sanitize Method
```php
private function sanitizeString($string) {
    if (!is_string($string)) {
        return $string;
    }
    
    // Remove control characters
    $string = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $string);
    
    // Trim whitespace
    $string = trim($string);
    
    // Convert to valid UTF-8
    if (!mb_check_encoding($string, 'UTF-8')) {
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }
    
    return $string;
}
```

#### b) Sanitize Context Data
- Sanitize tất cả dữ liệu sản phẩm trước khi build prompt
- Sanitize response từ Gemini API

#### c) Sanitize Prompt String
- Bao quanh dữ liệu động với sanitize

---

## 🧪 Cách Test

### Phương pháp 1: Dùng file test HTML mới
```
Mở: http://localhost/ivymoda/ivymoda_mvc/public/test-chatbot-fixed.html
```

**Các test được cung cấp:**
1. ✅ **Test Database** - Kiểm tra kết nối database
2. ✅ **Test Gemini Connection** - Kiểm tra API Gemini
3. ✅ **Test Chat** - Gửi tin nhắn thử
4. ✅ **Test JSON Response** - Kiểm tra format JSON
5. ✅ **Test FAQ Endpoints** - Kiểm tra FAQ APIs

### Phương pháp 2: Dùng cURL/Postman

**Test chat:**
```bash
curl -X POST http://localhost/ivymoda/ivymoda_mvc/public/ajax/chatbot_ajax.php \
  -H "Content-Type: application/json" \
  -d '{
    "action": "chat_with_ai",
    "message": "Tôi muốn tìm áo sơ mi nam màu trắng",
    "session_id": "test_session"
  }'
```

**Response mong đợi:**
```json
{
  "success": true,
  "response": "Tôi có thể giúp bạn tìm áo sơ mi nam màu trắng...",
  "suggested_products": [
    {
      "sanpham_id": 1,
      "sanpham_tieude": "Áo sơ mi nam trắng",
      "sanpham_gia": 250000,
      "danhmuc_ten": "Nam"
    }
  ],
  "response_time": 1234
}
```

---

## 🚨 Kiểm tra JSON Encoding

### Dùng PHP để test:
```php
<?php
$data = ['success' => true, 'message' => 'Tiếng Việt đúng không?'];
echo json_encode($data, JSON_UNESCAPED_UNICODE);

// Output: {"success":true,"message":"Tiếng Việt đúng không?"}
// KHÔNG phải: {"success":true,"message":"\u0110\u0169ng kh\u00f4ng?"}
?>
```

### Kiểm tra trong DevTools Browser:
1. Mở Chrome DevTools (F12)
2. Vào tab **Network**
3. Gửi request chatbot
4. Xem Response - phải là valid JSON với tiếng Việt hiển thị bình thường

---

## ✅ Checklist Sửa Chữa

- [x] Thêm error handler toàn cục
- [x] Sử dụng `JSON_UNESCAPED_UNICODE` cho tất cả `json_encode()`
- [x] Thêm sanitize functions cho string và array
- [x] Sanitize dữ liệu database trước JSON encode
- [x] Sanitize response từ Gemini API
- [x] Kiểm soát output buffering với `ob_clean()`
- [x] Ghi log lỗi chi tiết
- [x] Tạo file test HTML mới với UI tốt hơp

---

## 📊 Cấu Trúc Folder Logs

Đảm bảo folder logs tồn tại:
```
ivymoda_mvc/
└── logs/
    ├── chatbot_ajax_error.log     (Lỗi AJAX)
    ├── app.log                     (Lỗi chung)
    └── ...
```

---

## 🔗 Tài Liệu Liên Quan

- **UC3.47**: Chatbot tư vấn sản phẩm với Gemini AI
- **File Test Cũ**: `public/test-chatbot-fix.html` (thay thế bằng file mới)
- **File Test Mới**: `public/test-chatbot-fixed.html` (đã được tạo)

---

## ⚠️ Lưu Ý Quan Trọng

1. **UTF-8 Encoding**: Luôn đảm bảo files PHP được save với UTF-8 encoding
2. **Database**: Tất cả tables phải có charset `utf8mb4`
3. **HTTP Headers**: `Content-Type: application/json; charset=utf-8` phải set trước khi output
4. **Output Buffering**: Sử dụng `ob_start()` ở đầu và `ob_clean()` trước `echo json_encode()`
5. **Sanitize**: Luôn sanitize dữ liệu từ database trước khi output

---

## 📞 Hỗ Trợ

Nếu gặp lỗi:
1. Kiểm tra `logs/chatbot_ajax_error.log`
2. Mở DevTools → Network → xem Response tab
3. Xem lại error message trong JSON response

**Lỗi phổ biến:**
- `Unexpected token` → JSON format sai, check encoding
- `Method not allowed` → Phải dùng POST cho `chat_with_ai`
- `API key không được cấu hình` → Set API key trong database `tbl_chatbot_config`

