-- Sample FAQ data for chatbot - UC3.48
-- Insert sample FAQ data into tbl_chatbot_faq table

INSERT INTO tbl_chatbot_faq (question, answer, category, display_order, status, help_link, created_by) VALUES 
('Làm thế nào để đăng ký tài khoản?', 'Bạn có thể đăng ký tài khoản bằng cách:\n1. Nhấn vào nút "Đăng ký" ở góc trên bên phải\n2. Điền đầy đủ thông tin cá nhân\n3. Xác nhận email để kích hoạt tài khoản\n4. Đăng nhập và bắt đầu mua sắm', 'Đăng ký & Đăng nhập', 1, 1, NULL, 1),

('Tôi quên mật khẩu, phải làm sao?', 'Để khôi phục mật khẩu:\n1. Nhấn "Quên mật khẩu" trên trang đăng nhập\n2. Nhập email đã đăng ký\n3. Kiểm tra email và nhấn link khôi phục\n4. Tạo mật khẩu mới', 'Đăng ký & Đăng nhập', 2, 1, NULL, 1),

('Làm sao để đặt hàng?', 'Quy trình đặt hàng:\n1. Chọn sản phẩm và thêm vào giỏ hàng\n2. Kiểm tra giỏ hàng và nhấn "Thanh toán"\n3. Điền thông tin giao hàng\n4. Chọn phương thức thanh toán\n5. Xác nhận đơn hàng', 'Đặt hàng', 1, 1, NULL, 1),

('Có những phương thức thanh toán nào?', 'Chúng tôi hỗ trợ:\n- Thanh toán khi nhận hàng (COD)\n- Chuyển khoản ngân hàng\n- Ví điện tử MoMo\n- Thẻ tín dụng/ghi nợ', 'Thanh toán', 1, 1, NULL, 1),

('Thời gian giao hàng là bao lâu?', 'Thời gian giao hàng:\n- Nội thành: 1-2 ngày làm việc\n- Tỉnh thành khác: 3-5 ngày làm việc\n- Vùng sâu vùng xa: 5-7 ngày làm việc\n\nMiễn phí ship cho đơn hàng từ 500.000đ', 'Đơn hàng', 1, 1, NULL, 1),

('Có thể đổi trả sản phẩm không?', 'Chính sách đổi trả:\n- Đổi size trong 7 ngày\n- Trả hàng trong 30 ngày (chưa sử dụng)\n- Miễn phí đổi trả\n- Liên hệ hotline để được hỗ trợ', 'Chính sách', 1, 1, NULL, 1),

('Làm sao để theo dõi đơn hàng?', 'Theo dõi đơn hàng:\n1. Đăng nhập tài khoản\n2. Vào "Đơn hàng của tôi"\n3. Xem trạng thái đơn hàng\n4. Nhận thông báo qua email/SMS', 'Đơn hàng', 2, 1, NULL, 1),

('Có chương trình khuyến mãi nào không?', 'Các chương trình khuyến mãi:\n- Giảm giá 10% cho khách hàng mới\n- Mua 2 tặng 1 vào cuối tuần\n- Giảm giá 20% cho đơn hàng từ 1 triệu\n- Tích điểm đổi quà', 'Khuyến mãi', 1, 1, NULL, 1),

('Làm sao để chọn size phù hợp?', 'Hướng dẫn chọn size:\n1. Đo vòng ngực, eo, hông\n2. So sánh với bảng size của chúng tôi\n3. Nếu giữa 2 size, chọn size lớn hơn\n4. Liên hệ tư vấn nếu cần hỗ trợ', 'Sản phẩm', 1, 1, NULL, 1),

('Chất liệu sản phẩm có an toàn không?', 'Tất cả sản phẩm của chúng tôi:\n- Được kiểm định chất lượng\n- Sử dụng chất liệu an toàn\n- Không gây kích ứng da\n- Có chứng nhận chất lượng', 'Sản phẩm', 2, 1, NULL, 1),

('Có hỗ trợ khách hàng 24/7 không?', 'Dịch vụ hỗ trợ:\n- Hotline: 1900-xxxx (8:00-22:00)\n- Email: support@ivymoda.com\n- Chat trực tuyến: 24/7\n- Fanpage Facebook: IVY Moda Official', 'Hỗ trợ', 1, 1, NULL, 1),

('Làm sao để liên hệ với chúng tôi?', 'Các cách liên hệ:\n- Hotline: 1900-xxxx\n- Email: info@ivymoda.com\n- Địa chỉ: 123 Đường ABC, Quận 1, TP.HCM\n- Fanpage: facebook.com/ivymoda', 'Hỗ trợ', 2, 1, NULL, 1);
