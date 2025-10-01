# IvyModa - Website Bán Quần Áo Tích Hợp Chatbot

Website thương mại điện tử bán quần áo với trợ lý AI thông minh sử dụng Gemini API, có khả năng cá nhân hóa dựa trên hồ sơ người dùng.

## ✨ Tính năng

### 🛍️ E-commerce
- **Danh mục sản phẩm**: Hiển thị sản phẩm với hình ảnh, giá cả và mô tả
- **Lọc theo danh mục**: Áo sơ mi, áo thun, quần, váy, áo khoác
- **Chi tiết sản phẩm**: Xem thông tin chi tiết và chọn size
- **Giỏ hàng**: Thêm, xóa và quản lý sản phẩm trong giỏ
- **Tính tổng tiền**: Tự động tính tổng giá trị đơn hàng

### 🤖 Chatbot AI
- **Trợ lý mua sắm thông minh**: Sử dụng Gemini API từ Google
- **Cá nhân hóa**: Đọc và phân tích hồ sơ người dùng (sở thích, lịch sử mua hàng)
- **Tư vấn sản phẩm**: Gợi ý sản phẩm phù hợp dựa trên preferences
- **Lịch sử chat**: Lưu trữ và học từ các cuộc trò chuyện trước
- **Trả lời thông minh**: Hiểu ngữ cảnh và cung cấp câu trả lời chính xác

### 👤 Quản lý hồ sơ
- **Thông tin cá nhân**: Lưu tên và sở thích của khách hàng
- **Lịch sử mua hàng**: Tự động lưu các sản phẩm đã mua
- **Preferences**: Ghi nhận phong cách, màu sắc yêu thích
- **Session-based**: Dữ liệu được lưu trong session (có thể mở rộng với database)

## 🚀 Cài đặt

### Yêu cầu
- Node.js (v14 trở lên)
- npm hoặc yarn
- Gemini API Key (miễn phí tại [Google AI Studio](https://makersuite.google.com/app/apikey))

### Các bước cài đặt

1. **Clone repository**
```bash
git clone https://github.com/quangtus/ivymoda.git
cd ivymoda
```

2. **Cài đặt dependencies**
```bash
npm install
```

3. **Cấu hình API Key**

Tạo file `.env` từ template:
```bash
cp .env.example .env
```

Mở file `.env` và thêm Gemini API key của bạn:
```env
GEMINI_API_KEY=your_actual_api_key_here
SESSION_SECRET=your_random_secret_key
PORT=3000
```

**Lấy Gemini API Key:**
- Truy cập https://makersuite.google.com/app/apikey
- Đăng nhập với Google Account
- Tạo API key mới (miễn phí)
- Copy và paste vào file `.env`

4. **Chạy ứng dụng**
```bash
npm start
```

5. **Truy cập website**
Mở trình duyệt và vào: http://localhost:3000

## 📖 Hướng dẫn sử dụng

### Duyệt và mua sản phẩm
1. Xem danh sách sản phẩm trên trang chủ
2. Lọc theo danh mục (Áo sơ mi, Quần, Váy, v.v.)
3. Click vào sản phẩm để xem chi tiết
4. Chọn size và thêm vào giỏ hàng
5. Xem giỏ hàng và thanh toán

### Cài đặt hồ sơ cá nhân
1. Click vào tab "Hồ sơ"
2. Nhập tên của bạn
3. Thêm sở thích (ví dụ: "Áo sơ mi, Màu trắng, Style công sở")
4. Click "Lưu hồ sơ"

### Sử dụng Chatbot
1. Click vào khung chat ở góc dưới phải
2. Gõ câu hỏi hoặc yêu cầu của bạn
3. Chatbot sẽ trả lời dựa trên:
   - Hồ sơ cá nhân của bạn
   - Lịch sử mua hàng
   - Danh mục sản phẩm hiện có
   - Ngữ cảnh cuộc trò chuyện

**Ví dụ câu hỏi:**
- "Tôi muốn mua áo sơ mi cho công sở"
- "Gợi ý sản phẩm phù hợp với tôi"
- "Có áo khoác nào ấm áp không?"
- "Sản phẩm nào đang giảm giá?"

## 🏗️ Cấu trúc dự án

```
ivymoda/
├── server.js              # Express server và API endpoints
├── package.json           # Dependencies và scripts
├── .env.example          # Template cho environment variables
├── .gitignore            # Git ignore file
├── README.md             # Documentation
└── public/               # Frontend files
    ├── index.html        # Main HTML file
    ├── css/
    │   └── style.css     # Styles
    └── js/
        └── app.js        # Frontend JavaScript
```

## 🔧 API Endpoints

### Products
- `GET /api/products` - Lấy danh sách tất cả sản phẩm
- `GET /api/products?category=shirt` - Lọc theo danh mục
- `GET /api/products/:id` - Lấy chi tiết sản phẩm

### User Profile
- `GET /api/user/profile` - Lấy thông tin hồ sơ
- `POST /api/user/profile` - Cập nhật hồ sơ

### Shopping Cart
- `GET /api/cart` - Xem giỏ hàng
- `POST /api/cart/add` - Thêm sản phẩm vào giỏ
- `DELETE /api/cart/:productId` - Xóa sản phẩm khỏi giỏ

### Chatbot
- `POST /api/chat` - Gửi tin nhắn đến chatbot
- `GET /api/chat/history` - Xem lịch sử chat

## 🎨 Tính năng nổi bật

### Cá nhân hóa Chatbot
Chatbot sử dụng thông tin từ hồ sơ người dùng để:
- Gọi tên khách hàng
- Đề xuất sản phẩm phù hợp với sở thích
- Nhớ lịch sử mua hàng và tránh đề xuất trùng lặp
- Điều chỉnh tone và style trả lời

### Session Management
- Sử dụng express-session để lưu trữ:
  - User profile
  - Shopping cart
  - Chat history
- Dữ liệu được giữ trong suốt phiên làm việc

## 🔐 Bảo mật

- API keys được lưu trong `.env` và không commit lên Git
- Session secret để mã hóa session data
- CORS và security headers có thể được thêm vào production

## 🚀 Deployment

### Deploy lên Heroku
```bash
# Login to Heroku
heroku login

# Create app
heroku create your-app-name

# Set environment variables
heroku config:set GEMINI_API_KEY=your_api_key
heroku config:set SESSION_SECRET=your_secret

# Deploy
git push heroku main
```

### Deploy lên Vercel/Netlify
- Thêm environment variables trong dashboard
- Connect repository và deploy

## 📝 TODO / Mở rộng

- [ ] Thêm database (MongoDB/PostgreSQL) để lưu trữ vĩnh viễn
- [ ] Xác thực người dùng (login/register)
- [ ] Thanh toán trực tuyến (VNPay, MoMo)
- [ ] Quản trị admin panel
- [ ] Upload ảnh sản phẩm thực
- [ ] Review và rating sản phẩm
- [ ] Wishlist
- [ ] Order tracking
- [ ] Email notifications
- [ ] Multi-language support

## 🤝 Đóng góp

Mọi đóng góp đều được chào đón! Hãy tạo Pull Request hoặc mở Issue để thảo luận.

## 📄 License

ISC License

## 👨‍💻 Tác giả

Website được phát triển với ❤️ sử dụng:
- Node.js & Express
- Gemini AI API
- Vanilla JavaScript
- Modern CSS

## 📞 Liên hệ

Nếu có câu hỏi hoặc cần hỗ trợ, vui lòng mở Issue trên GitHub.

