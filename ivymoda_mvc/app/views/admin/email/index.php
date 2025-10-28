<?php require_once __DIR__ . '/../../shared/admin/header.php'; ?>

<?php require_once __DIR__ . '/../../shared/admin/sidebar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Trang chủ</a></li>
                        <li class="breadcrumb-item active">Quản lý Email</li>
                    </ol>
                </div>
                <h4 class="page-title">Dashboard Email</h4>
            </div>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-primary-subtle text-primary">
                                <i class="fas fa-envelope fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1"><?= $data['stats']['total_emails'] ?></h5>
                            <p class="text-muted mb-0">Tổng Email</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success-subtle text-success">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1"><?= $data['stats']['sent_emails'] ?></h5>
                            <p class="text-muted mb-0">Đã Gửi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-danger-subtle text-danger">
                                <i class="fas fa-times-circle fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1"><?= $data['stats']['failed_emails'] ?></h5>
                            <p class="text-muted mb-0">Thất Bại</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info-subtle text-info">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-1"><?= $data['stats']['unique_recipients'] ?></h5>
                            <p class="text-muted mb-0">Người Nhận</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu chức năng -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="<?= BASE_URL ?>admin/email/templates" class="btn btn-outline-primary w-100">
                                <i class="fas fa-file-alt me-2"></i>
                                Quản lý Template
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= BASE_URL ?>admin/email/send-promotion" class="btn btn-outline-success w-100">
                                <i class="fas fa-bullhorn me-2"></i>
                                Gửi Email Khuyến Mãi
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= BASE_URL ?>admin/email/logs" class="btn btn-outline-info w-100">
                                <i class="fas fa-history me-2"></i>
                                Xem Log Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log email gần đây -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">Email Gần Đây</h4>
                        <a href="<?= BASE_URL ?>admin/email/logs" class="btn btn-sm btn-outline-primary">
                            Xem tất cả
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Người nhận</th>
                                    <th>Tiêu đề</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['recentLogs'])): ?>
                                    <?php foreach ($data['recentLogs'] as $log): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($log->recipient ?? $log['recipient'] ?? '') ?></td>
                                            <td><?= htmlspecialchars($log->subject ?? $log['subject'] ?? '') ?></td>
                                            <td>
                                                <?php 
                                                $status = $log->status ?? $log['status'] ?? '';
                                                if ($status == 'sent'): 
                                                ?>
                                                    <span class="badge bg-success">Đã gửi</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Thất bại</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($log->sent_at ?? $log['sent_at'] ?? '')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Chưa có email nào</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cấu hình nhanh -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Cấu Hình Nhanh</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="<?= BASE_URL ?>admin/email/smtp-config" class="btn btn-outline-secondary w-100 mb-2">
                                <i class="fas fa-cog me-2"></i>
                                Cấu hình SMTP
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="<?= BASE_URL ?>admin/email/promotion-logs" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-chart-line me-2"></i>
                                Log Email Khuyến Mãi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../shared/admin/footer.php'; ?>
