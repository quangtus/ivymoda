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
                        <li class="breadcrumb-item active">Log Email</li>
                    </ol>
                </div>
                <h4 class="page-title">Log Email</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">Lịch sử gửi email</h4>
                        <div class="text-muted">
                            Tổng cộng: <strong><?= $data['totalLogs'] ?></strong> email
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Người nhận</th>
                                    <th>Tiêu đề</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($data['logs'])): ?>
                                    <?php foreach ($data['logs'] as $log): ?>
                                        <tr>
                                            <td><?= $log->log_id ?? $log['log_id'] ?? '' ?></td>
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
                                            <td><?= date('d/m/Y H:i:s', strtotime($log->sent_at ?? $log['sent_at'] ?? '')) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-info" onclick="viewEmailContent(<?= $log->log_id ?? $log['log_id'] ?? '' ?>)">
                                                    <i class="fas fa-eye"></i> Xem
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Chưa có email nào được gửi</td>
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

<!-- Modal xem nội dung email -->
<div class="modal fade" id="emailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nội dung Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="emailContent"></div>
            </div>
        </div>
    </div>
</div>

<script>
function viewEmailContent(logId) {
    // Redirect to view log page instead of AJAX
    window.location.href = `<?= BASE_URL ?>admin/email/viewLog/${logId}`;
}
</script>

<?php require_once __DIR__ . '/../../shared/admin/footer.php'; ?>
