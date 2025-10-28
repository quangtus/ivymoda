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
                        <li class="breadcrumb-item active">Sửa Template</li>
                    </ol>
                </div>
                <h4 class="page-title">Sửa Template Email</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>admin/email/templates/edit/<?= $data['template']->template_id ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="template_name" class="form-label">Tên Template <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="template_name" name="template_name" 
                                           value="<?= htmlspecialchars($data['template']->template_name) ?>" required>
                                    <div class="form-text">Tên duy nhất để nhận diện template</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Loại Template</label>
                                    <select class="form-select" id="type" name="type">
                                        <option value="">Chọn loại...</option>
                                        <option value="registration" <?= $data['template']->type == 'registration' ? 'selected' : '' ?>>Đăng ký tài khoản</option>
                                        <option value="order" <?= $data['template']->type == 'order' ? 'selected' : '' ?>>Xác nhận đơn hàng</option>
                                        <option value="password_reset" <?= $data['template']->type == 'password_reset' ? 'selected' : '' ?>>Đặt lại mật khẩu</option>
                                        <option value="promotion" <?= $data['template']->type == 'promotion' ? 'selected' : '' ?>>Khuyến mãi</option>
                                        <option value="notification" <?= $data['template']->type == 'notification' ? 'selected' : '' ?>>Thông báo chung</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Tiêu đề Email <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject" 
                                   value="<?= htmlspecialchars($data['template']->subject) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Nội dung Email <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="body" name="body" rows="15" 
                                      placeholder="Nhập nội dung HTML của email..." required><?= htmlspecialchars($data['template']->body) ?></textarea>
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
                                    Cập nhật Template
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview template -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Preview Template</h4>
                    <div class="border p-3 bg-light" id="previewContent">
                        <!-- Preview will be inserted here -->
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-info" onclick="updatePreview()">
                            <i class="fas fa-eye me-2"></i>
                            Cập nhật Preview
                        </button>
                    </div>
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
    // Initial preview
    updatePreview();
    
    // Update preview when content changes
    document.getElementById('body').addEventListener('input', function() {
        // Debounce the preview update
        clearTimeout(this.previewTimeout);
        this.previewTimeout = setTimeout(updatePreview, 1000);
    });
});

function updatePreview() {
    const body = document.getElementById('body').value;
    const previewContent = document.getElementById('previewContent');
    
    if (body.trim()) {
        // Replace variables with sample data for preview
        const previewBody = body
            .replace(/{customer_name}/g, 'Nguyễn Văn A')
            .replace(/{username}/g, 'nguyenvana')
            .replace(/{email}/g, 'nguyenvana@email.com')
            .replace(/{order_code}/g, 'ORD001')
            .replace(/{order_total}/g, '500.000 ₫')
            .replace(/{order_date}/g, '18/10/2025 16:40')
            .replace(/{activation_link}/g, '#')
            .replace(/{reset_link}/g, '#');
        
        previewContent.innerHTML = previewBody;
    } else {
        previewContent.innerHTML = '<p class="text-muted">Nhập nội dung template để xem preview</p>';
    }
}
</script>

<?php require_once __DIR__ . '/../../shared/admin/footer.php'; ?>