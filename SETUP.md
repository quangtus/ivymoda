# Hướng dẫn cài đặt nhanh IvyModa

## Bước 1: Cài đặt Node.js
Đảm bảo bạn đã cài đặt Node.js (phiên bản 14 trở lên).
Kiểm tra: `node --version`

## Bước 2: Clone repository
```bash
git clone https://github.com/quangtus/ivymoda.git
cd ivymoda
```

## Bước 3: Cài đặt dependencies
```bash
npm install
```

## Bước 4: Cấu hình API Key
1. Copy file `.env.example` thành `.env`:
```bash
cp .env.example .env
```

2. Lấy Gemini API Key MIỄN PHÍ:
   - Truy cập: https://makersuite.google.com/app/apikey
   - Đăng nhập bằng tài khoản Google
   - Click "Create API Key"
   - Copy API key

3. Mở file `.env` và thay thế:
```env
GEMINI_API_KEY=paste_your_api_key_here
SESSION_SECRET=ivymoda-secret-key-change-this
PORT=3000
```

## Bước 5: Chạy ứng dụng
```bash
npm start
```

## Bước 6: Truy cập website
Mở trình duyệt và vào: **http://localhost:3000**

---

## Lưu ý:
- Chatbot sẽ hoạt động với chế độ fallback nếu không có API key
- Để chatbot AI hoạt động đầy đủ, cần cấu hình GEMINI_API_KEY
- API key của Gemini là MIỄN PHÍ và không cần thẻ tín dụng

## Tính năng:
✅ Xem và lọc sản phẩm theo danh mục  
✅ Xem chi tiết sản phẩm và chọn size  
✅ Thêm sản phẩm vào giỏ hàng  
✅ Quản lý hồ sơ cá nhân (tên, sở thích)  
✅ Chatbot AI tư vấn mua sắm cá nhân hóa  

## Hỗ trợ:
Nếu gặp vấn đề, vui lòng mở Issue trên GitHub.
