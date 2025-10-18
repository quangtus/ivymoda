# Hệ thống đánh giá sản phẩm - IVY moda

## Tổng quan
Hệ thống đánh giá sản phẩm cho phép khách hàng đánh giá sản phẩm đã mua, hiển thị điểm trung bình và quản lý đánh giá từ phía admin.

## Tính năng chính

### 1. Đánh giá sản phẩm (Frontend)
- **Điều kiện**: Chỉ khách hàng đã mua hàng và đơn hàng đã hoàn thành mới được đánh giá
- **Giới hạn**: Mỗi sản phẩm chỉ được đánh giá 1 lần/đơn hàng
- **Nội dung**: Điểm sao (1-5), nhận xét chi tiết
- **Xác thực**: Hiển thị badge "Đã mua hàng" cho đánh giá từ khách hàng thực

### 2. Hiển thị đánh giá
- **Trang sản phẩm**: Hiển thị điểm trung bình, phân phối sao, đánh giá gần đây
- **Trang riêng**: Xem tất cả đánh giá của sản phẩm
- **Phản hồi admin**: Hiển thị phản hồi từ admin cho từng đánh giá

### 3. Quản lý admin
- **Duyệt đánh giá**: Hiện/ẩn đánh giá
- **Phản hồi**: Thêm/sửa phản hồi cho đánh giá
- **Xóa**: Xóa đánh giá không phù hợp
- **Thống kê**: Xem tổng quan đánh giá

## Cấu trúc Database

### Bảng `tbl_product_review`
```sql
- review_id: ID đánh giá
- sanpham_id: ID sản phẩm
- user_id: ID người dùng
- order_id: ID đơn hàng (để xác thực mua hàng)
- rating: Điểm đánh giá (1-5)
- comment: Nội dung đánh giá
- is_verified_purchase: Xác thực mua hàng
- status: Trạng thái hiển thị (1: hiển thị, 0: ẩn)
- admin_reply: Phản hồi từ admin
- created_at: Ngày tạo
- updated_at: Ngày cập nhật
```

## Routes

### Frontend
- `GET /review/add/{orderId}/{productId}` - Form đánh giá
- `POST /review/submit` - Xử lý đánh giá
- `GET /review/productReviews/{productId}` - Xem tất cả đánh giá sản phẩm

### Admin
- `GET /admin/review` - Danh sách đánh giá
- `GET /admin/review/viewDetail/{reviewId}` - Chi tiết đánh giá
- `POST /admin/review/updateStatus/{reviewId}` - Cập nhật trạng thái
- `POST /admin/review/reply/{reviewId}` - Thêm phản hồi
- `POST /admin/review/delete/{reviewId}` - Xóa đánh giá

## Files đã tạo

### Models
- `app/models/ReviewModel.php` - Xử lý dữ liệu đánh giá

### Controllers
- `app/controllers/frontend/ReviewController.php` - Controller frontend
- `app/controllers/admin/ReviewController.php` - Controller admin

### Views
- `app/views/frontend/review/add.php` - Form đánh giá
- `app/views/frontend/review/product_reviews.php` - Hiển thị đánh giá
- `app/views/admin/review/index.php` - Quản lý đánh giá admin
- `app/views/admin/review/view.php` - Chi tiết đánh giá admin

### Tích hợp
- Cập nhật `app/views/frontend/user/order_detail.php` - Thêm nút đánh giá
- Cập nhật `app/views/frontend/product/detail.php` - Hiển thị đánh giá
- Cập nhật `app/views/shared/admin/sidebar.php` - Menu admin

## Cách sử dụng

### 1. Khách hàng đánh giá
1. Đăng nhập và vào "Tài khoản của tôi"
2. Chọn "Lịch sử đơn hàng"
3. Chọn đơn hàng đã hoàn thành
4. Nhấn "Đánh giá" cho sản phẩm muốn đánh giá
5. Điền form và gửi đánh giá

### 2. Xem đánh giá
- Trên trang chi tiết sản phẩm: Xem điểm trung bình và đánh giá gần đây
- Nhấn "Xem tất cả đánh giá" để xem đầy đủ

### 3. Admin quản lý
1. Đăng nhập admin
2. Vào menu "Đánh giá"
3. Chọn "Tất cả đánh giá" hoặc lọc theo trạng thái
4. Thực hiện các thao tác: duyệt, ẩn, phản hồi, xóa

## Business Rules

1. **Chỉ đánh giá sản phẩm đã mua**: Kiểm tra qua `order_id`
2. **Mỗi sản phẩm 1 lần/đơn hàng**: Kiểm tra duplicate
3. **Chỉ đánh giá đơn hàng hoàn thành**: `order_status = 2`
4. **Cập nhật điểm trung bình**: Tự động tính toán khi có đánh giá mới
5. **Xác thực mua hàng**: Hiển thị badge cho đánh giá từ khách hàng thực

## Security

- Kiểm tra quyền truy cập cho mọi action
- Validate dữ liệu đầu vào
- XSS protection với `htmlspecialchars()`
- CSRF protection (có thể thêm token nếu cần)

## Performance

- Sử dụng View `view_product_with_rating` để tối ưu query
- Pagination cho danh sách đánh giá
- Index database cho các trường thường query

## Tương lai

- Thêm upload ảnh đánh giá
- Thêm like/dislike đánh giá
- Thêm báo cáo đánh giá spam
- Tích hợp email thông báo đánh giá mới
