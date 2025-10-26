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
                        <li class="breadcrumb-item active">Template Email</li>
                    </ol>
                </div>
                <h4 class="page-title">Quản lý Template Email</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">Danh sách Template</h4>
                        <a href="<?= BASE_URL ?>admin/email/templates/add" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Thêm Template
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Tên Template</th>
                                    <th>Loại</th>
                                    <th>Tiêu Đề</th>
                                    <th>Ngày Tạo</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['templates'])): ?>
                                    <?php foreach ($data['templates'] as $template): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($template->template_name) ?></strong>
                                            </td>
                                            <td>
                                                <?php if ($template->type): ?>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($template->type) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark">Chung</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($template->subject) ?></td>
                                            <td><?= isset($template->created_at) ? date('d/m/Y', strtotime($template->created_at)) : date('d/m/Y') ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= BASE_URL ?>admin/email/templates/edit/<?= $template->template_id ?>" 
                                                       class="btn btn-outline-primary" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>admin/email/templates/delete/<?= $template->template_id ?>" 
                                                       class="btn btn-outline-danger" title="Xóa"
                                                       onclick="return confirm('Bạn có chắc muốn xóa template này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-file-alt fa-3x mb-3"></i>
                                                <p>Chưa có template nào</p>
                                                <a href="<?= BASE_URL ?>admin/email/templates/add" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>
                                                    Thêm Template Đầu Tiên
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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

<?php require_once __DIR__ . '/../../shared/admin/footer.php'; ?>
