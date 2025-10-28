<?php require_once __DIR__ . '/../../shared/admin/header.php'; ?>

<?php require_once __DIR__ . '/../../shared/admin/sidebar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/email">Quản lý Email</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/email/templates">Template Email</a></li>
                        <li class="breadcrumb-item active">Thêm Template</li>
                    </ol>
                </div>
                <h4 class="page-title">Thêm Template Email</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>admin/email/templates/add">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="template_name" class="form-label">Tên Template <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="template_name" name="template_name" 
                                           value="<?= htmlspecialchars($data['template_name']) ?>"
                                           placeholder="VD: registration_confirmation" required>
                                    <div class="form-text">Tên duy nhất để nhận diện template</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Loại Template</label>
                                    <select class="form-select" id="type" name="type">
                                        <option value="">Chọn loại...</option>
                                        <option value="registration" <?= $data['type'] == 'registration' ? 'selected' : '' ?>>Đăng ký tài khoản</option>
                                        <option value="order" <?= $data['type'] == 'order' ? 'selected' : '' ?>>Xác nhận đơn hàng</option>
                                        <option value="password_reset" <?= $data['type'] == 'password_reset' ? 'selected' : '' ?>>Đặt lại mật khẩu</option>
                                        <option value="promotion" <?= $data['type'] == 'promotion' ? 'selected' : '' ?>>Khuyến mãi</option>
                                        <option value="notification" <?= $data['type'] == 'notification' ? 'selected' : '' ?>>Thông báo chung</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Tiêu đề Email <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="<?= htmlspecialchars($data['subject']) ?>"
                                   placeholder="VD: Chào mừng đến với IVY Moda" required>
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Nội dung Email <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="body" name="body" rows="15" 
                                      placeholder="Nhập nội dung HTML của email..." required><?= htmlspecialchars($data['body']) ?></textarea>
                            <div class="form-text">Sử dụng HTML để định dạng email. Có thể sử dụng các biến như {customer_name}, {username}, etc.</div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <a href="<?= BASE_URL ?>admin/email/templates" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Lưu Template
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hướng dẫn sử dụng -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Hướng Dẫn Sử Dụng Template</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Biến có thể sử dụng:</h6>
                            <ul class="list-unstyled">
                                <li><code>{customer_name}</code> - Tên khách hàng</li>
                                <li><code>{username}</code> - Tên đăng nhập</li>
                                <li><code>{email}</code> - Email khách hàng</li>
                                <li><code>{order_code}</code> - Mã đơn hàng</li>
                                <li><code>{order_total}</code> - Tổng tiền đơn hàng</li>
                                <li><code>{order_date}</code> - Ngày đặt hàng</li>
                                <li><code>{activation_link}</code> - Link kích hoạt tài khoản</li>
                                <li><code>{reset_link}</code> - Link đặt lại mật khẩu</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Loại template:</h6>
                            <ul class="list-unstyled">
                                <li><span class="badge bg-primary me-2">registration</span> - Email đăng ký</li>
                                <li><span class="badge bg-success me-2">order</span> - Email xác nhận đơn hàng</li>
                                <li><span class="badge bg-warning me-2">password_reset</span> - Email đặt lại mật khẩu</li>
                                <li><span class="badge bg-info me-2">promotion</span> - Email khuyến mãi</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-fill subject based on template name
    const templateNameInput = document.getElementById('template_name');
    const subjectInput = document.getElementById('subject');
    const typeSelect = document.getElementById('type');
    const bodyTextarea = document.getElementById('body');

    templateNameInput.addEventListener('input', function() {
        if (this.value && !subjectInput.value) {
            const name = this.value.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            subjectInput.value = name + ' - IVY Moda';
        }
    });

    // Auto-fill body based on type
    typeSelect.addEventListener('change', function() {
        if (this.value && !bodyTextarea.value) {
            let template = '';
            switch(this.value) {
                case 'registration':
                    template = `<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .button { display: inline-block; background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Chào mừng đến với IVY Moda!</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{username}</strong>,</p>
            <p>Cảm ơn bạn đã đăng ký tài khoản tại IVY Moda. Để kích hoạt tài khoản, vui lòng click vào link bên dưới:</p>
            <p style="text-align: center;">
                <a href="{activation_link}" class="button">Kích hoạt tài khoản</a>
            </p>
            <p>Link này có hiệu lực trong 24 giờ.</p>
        </div>
        <div class="footer">
            <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>`;
                    break;
                case 'order':
                    template = `<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .order-info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Đơn hàng của bạn đã được xác nhận!</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{customer_name}</strong>,</p>
            <p>Cảm ơn bạn đã mua sắm tại IVY Moda. Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>
            <div class="order-info">
                <h3>Thông tin đơn hàng</h3>
                <p><strong>Mã đơn hàng:</strong> #{order_code}</p>
                <p><strong>Ngày đặt:</strong> {order_date}</p>
                <p><strong>Tổng tiền:</strong> {order_total}</p>
            </div>
        </div>
        <div class="footer">
            <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>`;
                    break;
                case 'promotion':
                    template = `<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #e74c3c; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .promotion-box { background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; border: 2px solid #e74c3c; }
        .button { display: inline-block; background-color: #e74c3c; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
        .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎉 {promotion_title}</h2>
        </div>
        <div class="content">
            <p>Xin chào <strong>{customer_name}</strong>,</p>
            <div class="promotion-box">
                <h3>Chương trình khuyến mãi đặc biệt dành riêng cho bạn!</h3>
                {content}
            </div>
            <p style="text-align: center;">
                <a href="#" class="button">MUA NGAY</a>
            </p>
        </div>
        <div class="footer">
            <p>© 2025 IVY Moda. Tất cả quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>`;
                    break;
            }
            if (template) {
                bodyTextarea.value = template;
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../shared/admin/footer.php'; ?>