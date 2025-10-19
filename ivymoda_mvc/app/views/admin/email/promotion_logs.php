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
                        <li class="breadcrumb-item active">Log Email Khuyến Mãi</li>
                    </ol>
                </div>
                <h4 class="page-title">Log Email Khuyến Mãi</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">Lịch sử gửi email khuyến mãi</h4>
                        <div class="text-muted">
                            Tổng cộng: <strong><?= $data['totalLogs'] ?></strong> email
                        </div>
                    </div>

                    <!-- Thống kê -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-success"><?= $data['stats']['sent'] ?></h3>
                                <p class="text-muted">Đã gửi</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-danger"><?= $data['stats']['failed'] ?></h3>
                                <p class="text-muted">Thất bại</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-warning"><?= $data['stats']['pending'] ?></h3>
                                <p class="text-muted">Đang chờ</p>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tiêu đề khuyến mãi</th>
                                    <th>Người nhận</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                    <th>Lỗi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['logs'])): ?>
                                    <?php foreach ($data['logs'] as $log): ?>
                                        <tr>
                                            <td><?= $log->log_id ?? $log['log_id'] ?? '' ?></td>
                                            <td><?= htmlspecialchars($log->promotion_title ?? $log['promotion_title'] ?? '') ?></td>
                                            <td>
                                                <?= htmlspecialchars($log->recipient_email ?? $log['recipient_email'] ?? '') ?>
                                                <?php if ($log->fullname ?? $log['fullname']): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($log->fullname ?? $log['fullname']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $status = $log->status ?? $log['status'] ?? '';
                                                if ($status == 'sent'): 
                                                ?>
                                                    <span class="badge bg-success">Đã gửi</span>
                                                <?php elseif ($status == 'failed'): ?>
                                                    <span class="badge bg-danger">Thất bại</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Đang chờ</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i:s', strtotime($log->sent_at ?? $log['sent_at'] ?? '')) ?></td>
                                            <td>
                                                <?php if ($log->error_message ?? $log['error_message']): ?>
                                                    <small class="text-danger"><?= htmlspecialchars($log->error_message ?? $log['error_message']) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Chưa có email khuyến mãi nào được gửi</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($data['totalPages'] > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $data['totalPages']; $i++): ?>
                                    <li class="page-item <?= $i == $data['currentPage'] ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../shared/admin/footer.php'; ?>