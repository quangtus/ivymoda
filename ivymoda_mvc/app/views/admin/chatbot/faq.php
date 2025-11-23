<?php
/**
 * Admin Chatbot FAQ Management - UC3.48
 * Trang quản lý FAQ cho chatbot
 */
?>

<?php include_once __DIR__ . '/../../shared/admin/header.php'; ?>
<?php include_once __DIR__ . '/../../shared/admin/sidebar.php'; ?>

<div class="chatbot-admin-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-robot"></i>
            Quản lý FAQ
        </h1>
        <div class="page-actions">
            <a href="<?php echo BASE_URL; ?>admin/chatbot/addFaq" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm FAQ mới
            </a>
        </div>
    </div>

    <!-- FAQ List -->
    <div class="faq-table-container">
        <div class="card">
            <div class="card-header">
                <h5>Danh sách FAQ</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($faqs)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Câu hỏi</th>
                                    <th>Danh mục</th>
                                    <th>Thứ tự</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($faqs as $faq): ?>
                                    <tr>
                                        <td><?php echo $faq->faq_id; ?></td>
                                        <td>
                                            <div class="faq-question">
                                                <?php echo htmlspecialchars(substr($faq->question, 0, 50)); ?>
                                                <?php if (strlen($faq->question) > 50): ?>
                                                    <span class="text-muted">...</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($faq->category); ?></span>
                                        </td>
                                        <td><?php echo $faq->display_order; ?></td>
                                        <td>
                                            <span class="badge <?php echo $faq->status ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $faq->status ? 'Hoạt động' : 'Không hoạt động'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($faq->created_at)); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        onclick="viewFaq(<?php echo $faq->faq_id; ?>)"
                                                        title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="<?php echo BASE_URL; ?>admin/chatbot/editFaq/<?php echo $faq->faq_id; ?>" 
                                                   class="btn btn-sm btn-outline-warning" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-<?php echo $faq->status ? 'danger' : 'success'; ?>" 
                                                        onclick="toggleStatus(<?php echo $faq->faq_id; ?>, <?php echo $faq->status; ?>)"
                                                        title="<?php echo $faq->status ? 'Vô hiệu hóa' : 'Kích hoạt'; ?>">
                                                    <i class="fas fa-<?php echo $faq->status ? 'times' : 'check'; ?>"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteFaq(<?php echo $faq->faq_id; ?>)"
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                        <h4>Chưa có FAQ nào</h4>
                        <p class="text-muted">Hãy thêm FAQ đầu tiên để bắt đầu sử dụng chatbot.</p>
                        <a href="<?php echo BASE_URL; ?>admin/chatbot/addFaq" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm FAQ đầu tiên
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Detail Modal -->
<div class="modal fade" id="faqDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="faqDetailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// View FAQ details
function viewFaq(faqId) {
    fetch(`<?php echo BASE_URL; ?>ajax/chatbot_ajax.php?action=get_faq_by_id&id=${faqId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const faq = data.faq;
                document.getElementById('faqDetailContent').innerHTML = `
                    <div class="faq-detail">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Câu hỏi:</label>
                            <p class="form-control-plaintext">${faq.question}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Câu trả lời:</label>
                            <div class="form-control-plaintext">${faq.answer}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Danh mục:</label>
                                <p class="form-control-plaintext">${faq.category}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Thứ tự:</label>
                                <p class="form-control-plaintext">${faq.display_order}</p>
                            </div>
                        </div>
                        ${faq.help_link ? `
                            <div class="mb-3">
                                <label class="form-label fw-bold">Link hướng dẫn:</label>
                                <p class="form-control-plaintext">
                                    <a href="${faq.help_link}" target="_blank">${faq.help_link}</a>
                                </p>
                            </div>
                        ` : ''}
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('faqDetailModal')).show();
            } else {
                alert('Không thể tải chi tiết FAQ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối');
        });
}

// Toggle FAQ status
function toggleStatus(faqId, currentStatus) {
    if (confirm(`Bạn có chắc chắn muốn ${currentStatus ? 'vô hiệu hóa' : 'kích hoạt'} FAQ này?`)) {
        window.location.href = `<?php echo BASE_URL; ?>admin/chatbot/toggleFaqStatus/${faqId}`;
    }
}

// Delete FAQ
function deleteFaq(faqId) {
    if (confirm('Bạn có chắc chắn muốn xóa FAQ này? Hành động này không thể hoàn tác.')) {
        if (confirm('Xác nhận xóa FAQ?')) {
            window.location.href = `<?php echo BASE_URL; ?>admin/chatbot/deleteFaq/${faqId}`;
        }
    }
}
</script>

<?php include_once __DIR__ . '/../../shared/admin/footer.php'; ?>
