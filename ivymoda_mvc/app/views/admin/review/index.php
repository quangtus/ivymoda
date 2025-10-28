<?php require_once ROOT_PATH . 'app/views/shared/admin/header.php'; ?>

<?php require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php'; ?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="admin-container">
    <div class="admin-header">
        <h1>Quản lý đánh giá sản phẩm</h1>
        <div class="header-actions">
            <a href="<?= BASE_URL ?>admin/dashboard" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <!-- Filter -->
    <div class="filter-section">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label>Trạng thái:</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <option value="1" <?= $currentStatus === 1 ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= $currentStatus === 0 ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Reviews Table -->
    <div class="table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sản phẩm</th>
                    <th>Khách hàng</th>
                    <th>Đánh giá</th>
                    <th>Nội dung</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?= $review->review_id ?></td>
                        <td>
                            <div class="product-info">
                                <strong><?= htmlspecialchars($review->sanpham_tieude) ?></strong>
                                <?php if ($review->order_code): ?>
                                    <br><small>Đơn: <?= htmlspecialchars($review->order_code) ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div class="customer-info">
                                <strong><?= htmlspecialchars($review->fullname) ?></strong>
                                <br><small><?= htmlspecialchars($review->email) ?></small>
                            </div>
                        </td>
                        <td>
                            <div class="rating-display">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= $i <= $review->rating ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                                <span class="rating-number">(<?= $review->rating ?>/5)</span>
                            </div>
                        </td>
                        <td>
                            <div class="comment-preview">
                                <?= htmlspecialchars(substr($review->comment, 0, 100)) ?>
                                <?php if (strlen($review->comment) > 100): ?>
                                    <span class="text-muted">...</span>
                                <?php endif; ?>
                                
                                <?php if (!empty($review->review_images)): ?>
                                    <?php 
                                    $images = json_decode($review->review_images, true);
                                    if (is_array($images) && !empty($images)):
                                    ?>
                                    <div class="review-images-admin">
                                        <?php foreach (array_slice($images, 0, 3) as $image): ?>
                                            <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($image) ?>" 
                                                 alt="Ảnh đánh giá" 
                                                 class="admin-review-thumb"
                                                 onclick="openImageModal('<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($image) ?>')">
                                        <?php endforeach; ?>
                                        <?php if (count($images) > 3): ?>
                                            <span class="more-images">+<?= count($images) - 3 ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?= $review->status ? 'active' : 'inactive' ?>">
                                <?= $review->status ? 'Hiển thị' : 'Ẩn' ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($review->created_at)) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?= BASE_URL ?>admin/review/viewDetail/<?= $review->review_id ?>" 
                                   class="btn btn-sm btn-info">Xem</a>
                                
                                <form method="POST" action="<?= BASE_URL ?>admin/review/updateStatus/<?= $review->review_id ?>" 
                                      style="display: inline-block;">
                                    <input type="hidden" name="status" value="<?= $review->status ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-sm <?= $review->status ? 'btn-warning' : 'btn-success' ?>">
                                        <?= $review->status ? 'Ẩn' : 'Hiện' ?>
                                    </button>
                                </form>
                                
                                <button type="button" class="btn btn-sm btn-primary" 
                                        onclick="showReplyModal(<?= $review->review_id ?>, '<?= htmlspecialchars($review->admin_reply ?? '') ?>')">
                                    Phản hồi
                                </button>
                                
                                <form method="POST" action="<?= BASE_URL ?>admin/review/delete/<?= $review->review_id ?>" 
                                      style="display: inline-block;" 
                                      onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Không có đánh giá nào</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&status=<?= $currentStatus ?>" 
                   class="pagination-link <?= $i == $currentPage ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Image Modal -->
<div id="imageModal" class="image-modal">
    <div class="modal-content">
        <span class="close" onclick="closeImageModal()">&times;</span>
        <img id="modalImage" src="" alt="Ảnh đánh giá">
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Phản hồi đánh giá</h3>
            <span class="close" onclick="closeReplyModal()">&times;</span>
        </div>
        <form method="POST" id="replyForm">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nội dung phản hồi:</label>
                    <textarea name="admin_reply" id="admin_reply" rows="4" 
                              placeholder="Nhập phản hồi của bạn..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeReplyModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
            </div>
        </form>
    </div>
</div>

<style>
.admin-container {
    padding: 20px;
}

.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.admin-header h1 {
    color: #333;
    margin: 0;
}

.filter-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.filter-form {
    display: flex;
    gap: 20px;
    align-items: center;
}

.filter-group label {
    font-weight: 600;
    margin-right: 10px;
}

.filter-group select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.table-container {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.product-info strong {
    color: #333;
    display: block;
    margin-bottom: 5px;
}

.product-info small {
    color: #666;
    font-size: 12px;
}

.customer-info strong {
    color: #333;
    display: block;
    margin-bottom: 5px;
}

.customer-info small {
    color: #666;
    font-size: 12px;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 5px;
}

.rating-display .star {
    color: #ddd;
    font-size: 16px;
}

.rating-display .star.filled {
    color: #ffc107;
}

.rating-number {
    color: #666;
    font-size: 14px;
}

.comment-preview {
    max-width: 200px;
    color: #333;
    line-height: 1.4;
}

.review-images-admin {
    display: flex;
    gap: 5px;
    margin-top: 8px;
    flex-wrap: wrap;
}

.admin-review-thumb {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
    cursor: pointer;
    border: 1px solid #ddd;
    transition: all 0.2s ease;
}

.admin-review-thumb:hover {
    border-color: #007bff;
    transform: scale(1.1);
}

.more-images {
    background: #007bff;
    color: white;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.status-active {
    background: #d4edda;
    color: #155724;
}

.status-badge.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 11px;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 20px;
}

.pagination-link {
    padding: 8px 12px;
    border: 1px solid #ddd;
    color: #007bff;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.2s;
}

.pagination-link:hover,
.pagination-link.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    margin: 10% auto;
    padding: 0;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
}

.modal-header h3 {
    margin: 0;
    color: #333;
}

.close {
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 20px;
    border-top: 1px solid #e9ecef;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    resize: vertical;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Image Modal Styles */
.image-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.9);
}

.image-modal .modal-content {
    position: relative;
    margin: auto;
    padding: 0;
    width: 90%;
    max-width: 800px;
    top: 50%;
    transform: translateY(-50%);
}

.image-modal .modal-content img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}

.image-modal .close {
    position: absolute;
    top: -40px;
    right: 0;
    color: #fff;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.image-modal .close:hover {
    color: #ccc;
}
</style>

<script>
function showReplyModal(reviewId, currentReply) {
    document.getElementById('replyForm').action = '<?= BASE_URL ?>admin/review/reply/' + reviewId;
    document.getElementById('admin_reply').value = currentReply;
    document.getElementById('replyModal').style.display = 'block';
}

function closeReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
}

function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modal.style.display = 'block';
    modalImg.src = imageSrc;
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const replyModal = document.getElementById('replyModal');
    const imageModal = document.getElementById('imageModal');
    if (event.target == replyModal) {
        replyModal.style.display = 'none';
    }
    if (event.target == imageModal) {
        imageModal.style.display = 'none';
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeReplyModal();
        closeImageModal();
    }
});
</script>

        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
