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
            Quản lý FAQ Chatbot
        </h1>
        <div class="page-actions">
            <a href="<?php echo BASE_URL; ?>admin/chatbot/add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm FAQ mới
            </a>
            <a href="<?php echo BASE_URL; ?>admin/chatbot/config" class="btn btn-outline-secondary">
                <i class="fas fa-cog"></i> Cấu hình
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-number"><?php echo $stats->total_faqs ?? 0; ?></div>
            <div class="stat-label">Tổng FAQ</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon active">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number"><?php echo $stats->active_faqs ?? 0; ?></div>
            <div class="stat-label">Đang hoạt động</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon inactive">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-number"><?php echo $stats->inactive_faqs ?? 0; ?></div>
            <div class="stat-label">Không hoạt động</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon categories">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stat-number"><?php echo $stats->total_categories ?? 0; ?></div>
            <div class="stat-label">Danh mục</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <div class="filter-title">Bộ lọc</div>
        <form method="GET" action="<?php echo BASE_URL; ?>admin/chatbot">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="category">Danh mục:</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category->category); ?>" 
                                    <?php echo ($filters['category'] === $category->category) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->category); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="status">Trạng thái:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" <?php echo ($filters['status'] === '1') ? 'selected' : ''; ?>>Hoạt động</option>
                        <option value="0" <?php echo ($filters['status'] === '0') ? 'selected' : ''; ?>>Không hoạt động</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="search">Tìm kiếm:</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Tìm kiếm câu hỏi hoặc câu trả lời..." 
                           value="<?php echo htmlspecialchars($filters['search']); ?>">
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="<?php echo BASE_URL; ?>admin/chatbot" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- FAQ List -->
    <div class="faq-table-container">
        <div class="card-header">
            <h5>Danh sách FAQ</h5>
        </div>
        <div class="card-body">
                    <?php if (!empty($faqs)): ?>
                        <div class="table-responsive">
                            <table class="faq-table">
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
                                                <span class="faq-category"><?php echo htmlspecialchars($faq->category); ?></span>
                                            </td>
                                            <td><?php echo $faq->display_order; ?></td>
                                            <td>
                                                <span class="faq-status <?php echo $faq->status ? 'active' : 'inactive'; ?>">
                                                    <?php echo $faq->status ? 'Hoạt động' : 'Không hoạt động'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($faq->created_at)); ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button type="button" class="btn-action btn-view" 
                                                            onclick="viewFaq(<?php echo $faq->faq_id; ?>)"
                                                            title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i> Xem
                                                    </button>
                                                    <a href="<?php echo BASE_URL; ?>admin/chatbot/edit/<?php echo $faq->faq_id; ?>" 
                                                       class="btn-action btn-edit" title="Sửa">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </a>
                                                    <button type="button" class="btn-action btn-toggle" 
                                                            onclick="toggleStatus(<?php echo $faq->faq_id; ?>, <?php echo $faq->status; ?>)"
                                                            title="<?php echo $faq->status ? 'Vô hiệu hóa' : 'Kích hoạt'; ?>">
                                                        <i class="fas fa-<?php echo $faq->status ? 'times' : 'check'; ?>"></i> <?php echo $faq->status ? 'Tắt' : 'Bật'; ?>
                                                    </button>
                                                    <button type="button" class="btn-action btn-delete" 
                                                            onclick="deleteFaq(<?php echo $faq->faq_id; ?>)"
                                                            title="Xóa">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($pagination['total_pages'] > 1): ?>
                            <nav aria-label="FAQ pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($pagination['current_page'] > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&category=<?php echo $filters['category']; ?>&status=<?php echo $filters['status']; ?>&search=<?php echo urlencode($filters['search']); ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                        <li class="page-item <?php echo ($i == $pagination['current_page']) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $filters['category']; ?>&status=<?php echo $filters['status']; ?>&search=<?php echo urlencode($filters['search']); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&category=<?php echo $filters['category']; ?>&status=<?php echo $filters['status']; ?>&search=<?php echo urlencode($filters['search']); ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-question-circle"></i>
                            <h3>Chưa có FAQ nào</h3>
                            <p>Hãy thêm FAQ đầu tiên để bắt đầu sử dụng chatbot.</p>
                            <a href="<?php echo BASE_URL; ?>admin/chatbot/add" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm FAQ đầu tiên
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
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

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.page-title {
    margin: 0;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-actions {
    display: flex;
    gap: 10px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #6c757d;
}

.stat-icon.active {
    background: #d4edda;
    color: #155724;
}

.stat-icon.inactive {
    background: #f8d7da;
    color: #721c24;
}

.stat-content h3 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.stat-content p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.faq-question-preview {
    max-width: 300px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state i {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state h4 {
    margin-bottom: 10px;
    color: #333;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

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
        fetch(`<?php echo BASE_URL; ?>admin/chatbot/toggleStatus/${faqId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Không thể cập nhật trạng thái');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối');
        });
    }
}

// Delete FAQ
function deleteFaq(faqId) {
    if (confirm('Bạn có chắc chắn muốn xóa FAQ này? Hành động này không thể hoàn tác.')) {
        if (confirm('Xác nhận xóa FAQ?')) {
            window.location.href = `<?php echo BASE_URL; ?>admin/chatbot/delete/${faqId}`;
        }
    }
}
</script>

<?php include_once __DIR__ . '/../../shared/admin/footer.php'; ?>
