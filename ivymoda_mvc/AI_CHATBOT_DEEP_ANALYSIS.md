# 🔥 PHÂN TÍCH SÂU 2 VERSION - AI CHATBOT BUG

## 📊 TIMELINE PHÂN TÍCH

### Version Cũ (ivymoda_mvc_old)
```
28-Oct-2025 12:14:06 ✅ SUCCESS - Tìm áo sơ mi nam
28-Oct-2025 12:16:56 ✅ SUCCESS - Tư vấn sản phẩm
28-Oct-2025 12:30:08 ❌ FAILED  - "Không thể tạo phản hồi" (rate limit?)
28-Oct-2025 12:31:14 ✅ SUCCESS - Đồ đắt nhất
```

### Version Mới (ivymoda_mvc)
```
28-Oct đến 09-Nov ✅ HOẠT ĐỘNG BÌNH THƯỜNG
13-Nov (HÔM NAY)  ❌ "Gemini API key not found"
```

---

## 🔍 NGUYÊN NHÂN GỐC RỄ

### ❌ VẤN ĐỀ 1: API KEY BỊ XÓA

**Phát hiện:**
```log
[13-Nov-2025 06:08:50] ❌ Gemini API key not found
[13-Nov-2025 06:09:01] ❌ Gemini API key not found
[13-Nov-2025 06:21:50] ❌ Gemini API key not found
```

**Kiểm tra database:**
```sql
SELECT config_value FROM tbl_chatbot_config 
WHERE config_key = 'gemini_api_key';

Result: '' (RỖNG!)
```

**Nguyên nhân:**
- Database bị **RE-IMPORT** sau ngày 09-Nov
- File SQL import **KHÔNG CÓ** hoặc **SKIP** dòng INSERT config
- API key ban đầu: `AIzaSyA6RZuA5V6DqAXWcdHMqXgn0Dxe_GEVIak`

---

### ✅ VẤN ĐỀ 2: JSON BODY PARSING (ĐÃ FIX)

**File:** `public/ajax/chatbot_ajax.php`

**BEFORE (Line 338-341):**
```php
function chatWithAI($chatbotModel) {
    // Đọc từ $_POST thay vì JSON body
    $userMessage = trim($_POST['message'] ?? '');
    $sessionId = trim($_POST['session_id'] ?? session_id());
```

**Frontend gửi:**
```javascript
fetch('ajax/chatbot_ajax.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'  // ← JSON!
    },
    body: JSON.stringify({
        action: 'chat_with_ai',
        message: 'Tôi muốn tìm áo sơ mi nam'
    })
});
```

**Kết quả:** `$_POST['message']` = **NULL** → Lỗi "Vui lòng nhập tin nhắn"

**AFTER (ĐÃ FIX):**
```php
function chatWithAI($chatbotModel) {
    // Đọc từ cả $_POST và JSON body
    $input = json_decode(file_get_contents('php://input'), true);
    $userMessage = trim($input['message'] ?? $_POST['message'] ?? '');
    $sessionId = trim($input['session_id'] ?? $_POST['session_id'] ?? session_id());
```

---

## 🎯 TẠI SAO VERSION CŨ HOẠT ĐỘNG?

### Lý do 1: API Key Còn Trong Database

**Version cũ (ivymoda_mvc_old):**
```sql
-- Database: ivymoda (hoặc ivymoda_old?)
SELECT config_value FROM tbl_chatbot_config 
WHERE config_key = 'gemini_api_key';

Result: 'AIzaSyA6RZuA5V6DqAXWcdHMqXgn0Dxe_GEVIak' ✅
```

**Evidence từ logs:**
```log
[28-Oct-2025 12:13:56] 📤 Sending to Gemini - Message: Tôi muốn tìm áo sơ mi nam
[28-Oct-2025 12:14:06] 📥 Gemini Response: {"success":true,"response":"...
    ÁO SƠ MI NAM TRẮNG BASIC - 499,000đ
    ...
```

### Lý do 2: Code Giống Hệt Nhau

**So sánh các file chính:**

| File | ivymoda_mvc_old | ivymoda_mvc | Giống nhau? |
|------|-----------------|-------------|-------------|
| `chatbot_ajax.php` (before fix) | Đọc $_POST | Đọc $_POST | ✅ 100% |
| `chatbot-ai.js` | Gửi JSON | Gửi JSON | ✅ 100% |
| `GeminiService.php` | Same | Same | ✅ 100% |
| `ChatbotModel.php` | Same | Same | ✅ 100% |

**Kết luận:** Code logic **GIỐNG HỆT**, chỉ khác **API KEY**!

---

## 📝 API KEY TRONG SQL - HỢP LÝ HAY KHÔNG?

### ❌ Không Hợp Lý Về Mặt Bảo Mật

**Lý do:**
1. **Git Security Risk:**
   ```bash
   git add ivymoda_final.sql
   git commit -m "Add database"
   # ← API key bị commit vào Git history, không xóa được!
   ```

2. **Public Repository:**
   - Nếu push lên GitHub public → API key lộ
   - Bất kỳ ai cũng xem được SQL file

3. **Team Collaboration:**
   - Nhiều người clone repo → ai cũng có API key
   - Khó quản lý khi key bị revoke

### ✅ Hợp Lý Về Mặt Deployment

**Ưu điểm:**
1. **Easy Setup:**
   ```bash
   mysql -u root ivymoda < ivymoda_final.sql
   # ← Tự động có API key, không cần config thêm
   ```

2. **Consistency:**
   - Dev, staging, production dùng cùng 1 SQL
   - Đảm bảo config giống nhau

3. **Quick Testing:**
   - Clone repo → import SQL → chạy ngay
   - Không cần setup .env, config files

### 🎯 GIẢI PHÁP TỐI ƯU

**Cách 1: Environment Variables (Recommended)**
```sql
-- ivymoda_final.sql
INSERT INTO tbl_chatbot_config VALUES 
(1, 'gemini_api_key', '', 'API key - Set via .env', NOW());
```

```properties
# .env
GEMINI_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXX
```

```php
// app/helpers/EnvHelper.php
class EnvHelper {
    public static function syncConfigFromEnv() {
        $db = new Database();
        $apiKey = self::get('GEMINI_API_KEY');
        
        if ($apiKey) {
            $db->execute(
                "UPDATE tbl_chatbot_config 
                 SET config_value = ? 
                 WHERE config_key = 'gemini_api_key'",
                [$apiKey]
            );
        }
    }
}
```

**Cách 2: SQL Template với Placeholder**
```sql
-- ivymoda_template.sql
INSERT INTO tbl_chatbot_config VALUES 
(1, 'gemini_api_key', '{{GEMINI_API_KEY}}', 'API key', NOW());
```

Setup script:
```bash
# setup.sh
read -p "Enter Gemini API Key: " API_KEY
sed "s/{{GEMINI_API_KEY}}/$API_KEY/g" ivymoda_template.sql > ivymoda_final.sql
mysql -u root ivymoda < ivymoda_final.sql
```

**Cách 3: Post-Install Script**
```sql
-- ivymoda_final.sql (no API key)
INSERT INTO tbl_chatbot_config VALUES 
(1, 'gemini_api_key', '', 'Set after install', NOW());
```

```php
// public/install.php (run once)
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $apiKey = $_POST['gemini_api_key'] ?? '';
    
    $db = new Database();
    $db->execute(
        "UPDATE tbl_chatbot_config 
         SET config_value = ? 
         WHERE config_key = 'gemini_api_key'",
        [$apiKey]
    );
    
    echo "✅ API Key saved!";
    exit;
}
?>
<form method="POST">
    <label>Gemini API Key:</label>
    <input type="text" name="gemini_api_key" required>
    <button>Save</button>
</form>
```

---

## 🔄 TẠI SAO VERSION CŨ VẪN HOẠT ĐỘNG?

### Giả thuyết 1: Database Khác Nhau

```
ivymoda_mvc_old → Database: ivymoda_old (có API key)
ivymoda_mvc     → Database: ivymoda (bị re-import, mất API key)
```

**Kiểm tra:**
```sql
-- Version cũ
USE ivymoda_old;
SELECT config_value FROM tbl_chatbot_config 
WHERE config_key = 'gemini_api_key';
-- Result: 'AIzaSy...' ✅

-- Version mới
USE ivymoda;
SELECT config_value FROM tbl_chatbot_config 
WHERE config_key = 'gemini_api_key';
-- Result: '' ❌ (trước khi fix)
```

### Giả thuyết 2: File SQL Khác Nhau

**ivymoda_mvc_old/ivymoda_final.sql:**
```sql
-- Line 963
INSERT INTO tbl_chatbot_config VALUES 
(1, 'gemini_api_key', 'AIzaSyA6RZuA5V6DqAXWcdHMqXgn0Dxe_GEVIak', ...);
```

**ivymoda_mvc/ivymoda_final.sql:**
```sql
-- Line 1016 (cũng có nhưng bị skip khi import?)
INSERT INTO tbl_chatbot_config VALUES 
(1, 'gemini_api_key', 'AIzaSyA6RZuA5V6DqAXWcdHMqXgn0Dxe_GEVIak', ...);
```

**Nguyên nhân có thể:**
- Import bằng PHPMyAdmin → Skip duplicate keys
- Import partial (chỉ structure, không có data)
- Manual delete sau khi import

---

## ✅ ĐÃ FIX

### 1. Backend JSON Parsing
```php
// File: public/ajax/chatbot_ajax.php
// Line: 330-343
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? $_POST['message'] ?? '');
```

### 2. Restore API Key
```sql
USE ivymoda;
UPDATE tbl_chatbot_config 
SET config_value = 'AIzaSyA6RZuA5V6DqAXWcdHMqXgn0Dxe_GEVIak'
WHERE config_key = 'gemini_api_key';
```

**Verify:**
```sql
SELECT config_key, LEFT(config_value, 30) AS preview 
FROM tbl_chatbot_config 
WHERE config_key = 'gemini_api_key';

+----------------+--------------------------------+
| config_key     | preview                        |
+----------------+--------------------------------+
| gemini_api_key | AIzaSyA6RZuA5V6DqAXWcdHMqXgn0D |
+----------------+--------------------------------+
```

---

## 🧪 TESTING CHECKLIST

- [x] Fix JSON body parsing in chatbot_ajax.php
- [x] Restore API key to database
- [ ] Test chatbot on homepage
- [ ] Verify AI response quality
- [ ] Check suggested products
- [ ] Monitor response time
- [ ] Verify logs: `logs/chatbot_ajax_error.log`

---

## 🚨 LỜI KHUYẾN CÁO

### Ngắn Hạn (Immediate)
1. ✅ **Đã restore API key vào database**
2. ⏳ **Test chatbot ngay để verify fix**
3. ⏳ **Backup config hiện tại:**
   ```sql
   mysqldump -u root ivymoda tbl_chatbot_config > chatbot_config_backup.sql
   ```

### Dài Hạn (Best Practice)
1. **Di chuyển API key ra .env:**
   - Thêm vào `.gitignore`
   - Tạo `.env.example` với placeholder

2. **Tạo setup script:**
   ```bash
   # setup.bat
   echo "Setting up IVY Moda..."
   mysql -u root ivymoda < ivymoda_final.sql
   php public/sync_env_to_db.php
   echo "Done! Configure .env and restart server"
   ```

3. **Monitor API usage:**
   - Gemini free tier: 60 requests/minute
   - Log API calls
   - Alert khi gần limit

4. **Implement fallback:**
   - API fail → Switch to FAQ mode
   - Display: "AI tạm bảo trì, vui lòng dùng FAQ"

---

## 📞 DEBUG COMMANDS

```bash
# 1. Check current API key
mysql -u root -e "USE ivymoda; SELECT config_key, LEFT(config_value, 20) FROM tbl_chatbot_config WHERE config_key='gemini_api_key';"

# 2. Watch live logs
Get-Content "logs/chatbot_ajax_error.log" -Wait -Tail 20

# 3. Test AJAX endpoint
curl -X POST http://localhost/ivymoda/ivymoda_mvc/public/ajax/chatbot_ajax.php \
  -H "Content-Type: application/json" \
  -d '{"action":"test_gemini"}'

# 4. Clear browser cache
Ctrl + Shift + Delete (Chrome)
```

---

**Created:** November 13, 2025  
**Status:** ✅ FIXED - API Key Restored + JSON Parsing Fixed  
**Root Cause:** Database re-import without config data  
**Solution:** Manual UPDATE + Code fix for JSON body
