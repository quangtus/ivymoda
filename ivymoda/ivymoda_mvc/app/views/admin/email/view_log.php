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
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/email/logs">Log Email</a></li>
                        <li class="breadcrumb-item active">Xem nội dung Email</li>
                    </ol>
                </div>
                <h4 class="page-title">Xem nội dung Email</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Thông tin Email</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td><?= $data['log']->log_id ?? $data['log']['log_id'] ?? '' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Người nhận:</strong></td>
                                    <td><?= htmlspecialchars($data['log']->recipient ?? $data['log']['recipient'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tiêu đề:</strong></td>
                                    <td><?= htmlspecialchars($data['log']->subject ?? $data['log']['subject'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        <?php 
                                        $status = $data['log']->status ?? $data['log']['status'] ?? '';
                                        if ($status == 'sent'): 
                                        ?>
                                            <span class="badge bg-success">Đã gửi</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Thất bại</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Thời gian:</strong></td>
                                    <td><?= date('d/m/Y H:i:s', strtotime($data['log']->sent_at ?? $data['log']['sent_at'] ?? '')) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Thao tác</h5>
                            <div class="d-grid gap-2">
                                <a href="<?= BASE_URL ?>admin/email/logs" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Quay lại
                                </a>
                                <button type="button" class="btn btn-outline-primary" onclick="printEmail()">
                                    <i class="fas fa-print me-2"></i>
                                    In Email
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="copyEmailContent()">
                                    <i class="fas fa-copy me-2"></i>
                                    Copy nội dung
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5>Nội dung Email</h5>
                    <div class="border p-3 bg-light" id="emailContent">
                        <?= $data['log']->body ?? $data['log']['body'] ?? '' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printEmail() {
    const content = document.getElementById('emailContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>In Email - IVY Moda</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .email-header { border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 20px; }
                .email-content { line-height: 1.6; }
            </style>
        </head>
        <body>
            <div class="email-header">
                <h2>IVY Moda - Email Content</h2>
                <p><strong>Người nhận:</strong> <?= htmlspecialchars($data['log']->recipient ?? $data['log']['recipient'] ?? '') ?></p>
                <p><strong>Tiêu đề:</strong> <?= htmlspecialchars($data['log']->subject ?? $data['log']['subject'] ?? '') ?></p>
                <p><strong>Thời gian:</strong> <?= date('d/m/Y H:i:s', strtotime($data['log']->sent_at ?? $data['log']['sent_at'] ?? '')) ?></p>
            </div>
            <div class="email-content">
                ${content}
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function copyEmailContent() {
    const content = document.getElementById('emailContent').innerText;
    navigator.clipboard.writeText(content).then(function() {
        alert('Đã copy nội dung email vào clipboard');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Không thể copy nội dung email');
    });
}
</script>

<?php require_once __DIR__ . '/../../shared/admin/footer.php'; ?>