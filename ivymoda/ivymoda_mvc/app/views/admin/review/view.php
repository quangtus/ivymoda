<?php require_once ROOT_PATH . 'app/views/shared/admin/header.php'; ?>

<div class="admin-container">
    <div class="admin-header">
        <h1>Chi tiết đánh giá</h1>
        <div class="header-actions">
            <a href="<?= BASE_URL ?>admin/review/index" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>

    <div class="review-detail">
        <div class="review-info">
            <div class="info-section">
                <h3>Thông tin đánh giá</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>ID đánh giá:</label>
                        <span><?= $review->review_id ?></span>
                    </div>
                    <div class="info-item">
                        <label>Ngày tạo:</label>
                        <span><?= date('d/m/Y H:i:s', strtotime($review->created_at)) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Trạng thái:</label>
                        <span class="status-badge status-<?= $review->status ? 'active' : 'inactive' ?>">
                            <?= $review->status ? 'Hiển thị' : 'Ẩn' ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Đã mua hàng:</label>
                        <span class="verified-badge">
                            <?= $review->is_verified_purchase ? '✓ Có' : '✗ Không' ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h3>Thông tin sản phẩm</h3>
                <div class="product-detail">
                    <strong><?= htmlspecialchars($review->sanpham_tieude) ?></strong>
                    <?php if ($review->order_code): ?>
                        <br><small>Đơn hàng: <?= htmlspecialchars($review->order_code) ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="info-section">
                <h3>Thông tin khách hàng</h3>
                <div class="customer-detail">
                    <strong><?= htmlspecialchars($review->fullname) ?></strong>
                    <br><small><?= htmlspecialchars($review->email) ?></small>
                </div>
            </div>
        </div>

        <div class="review-content">
            <div class="rating-section">
                <h3>Đánh giá</h3>
                <div class="rating-display">
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= $review->rating ? 'filled' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-text"><?= $review->rating ?>/5 sao</span>
                </div>
            </div>

            <div class="comment-section">
                <h3>Nội dung đánh giá</h3>
                <div class="comment-content">
                    <?= nl2br(htmlspecialchars($review->comment)) ?>
                    
                    <?php if (!empty($review->review_images)): ?>
                        <?php 
                        $images = json_decode($review->review_images, true);
                        if (is_array($images) && !empty($images)):
                        ?>
                        <div class="review-images-section">
                            <h4>Ảnh đánh giá:</h4>
                            <div class="review-images-grid">
                                <?php foreach ($images as $image): ?>
                                    <div class="review-image-item">
                                        <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($image) ?>" 
                                             alt="Ảnh đánh giá" 
                                             onclick="openImageModal('<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($image) ?>')">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($review->admin_reply): ?>
            <div class="admin-reply-section">
                <h3>Phản hồi của admin</h3>
                <div class="admin-reply-content">
                    <?= nl2br(htmlspecialchars($review->admin_reply)) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="review-actions">
            <h3>Thao tác</h3>
            <div class="action-buttons">
                <form method="POST" action="<?= BASE_URL ?>admin/review/updateStatus/<?= $review->review_id ?>" 
                      style="display: inline-block;">
                    <input type="hidden" name="status" value="<?= $review->status ? 0 : 1 ?>">
                    <button type="submit" class="btn <?= $review->status ? 'btn-warning' : 'btn-success' ?>">
                        <?= $review->status ? 'Ẩn đánh giá' : 'Hiện đánh giá' ?>
                    </button>
                </form>

                <button type="button" class="btn btn-primary" 
                        onclick="showReplyModal('<?= htmlspecialchars($review->admin_reply ?? '') ?>')">
                    <?= $review->admin_reply ? 'Sửa phản hồi' : 'Thêm phản hồi' ?>
                </button>

                <form method="POST" action="<?= BASE_URL ?>admin/review/delete/<?= $review->review_id ?>" 
                      style="display: inline-block;" 
                      onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này?')">
                    <button type="submit" class="btn btn-danger">Xóa đánh giá</button>
                </form>
            </div>
        </div>
    </div>
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
            <h3><?= $review->admin_reply ? 'Sửa phản hồi' : 'Thêm phản hồi' ?></h3>
            <span class="close" onclick="closeReplyModal()">&times;</span>
        </div>
        <form method="POST" action="<?= BASE_URL ?>admin/review/reply/<?= $review->review_id ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label>Nội dung phản hồi:</label>
                    <textarea name="admin_reply" id="admin_reply" rows="4" 
                              placeholder="Nhập phản hồi của bạn..."><?= htmlspecialchars($review->admin_reply ?? '') ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeReplyModal()">Hủy</button>
                <button type="submit" class="btn btn-primary"><?= $review->admin_reply ? 'Cập nhật' : 'Gửi' ?> phản hồi</button>
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

.review-detail {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.review-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    height: fit-content;
}

.review-content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.info-section {
    margin-bottom: 25px;
}

.info-section h3 {
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #ddd;
}

.info-grid {
    display: grid;
    gap: 10px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 600;
    color: #333;
    margin-right: 10px;
}

.info-item span {
    color: #666;
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

.verified-badge {
    color: #28a745;
    font-weight: 600;
}

.product-detail,
.customer-detail {
    color: #333;
    line-height: 1.6;
}

.product-detail strong,
.customer-detail strong {
    display: block;
    margin-bottom: 5px;
}

.product-detail small,
.customer-detail small {
    color: #666;
    font-size: 14px;
}

.rating-section,
.comment-section,
.admin-reply-section {
    margin-bottom: 25px;
}

.rating-section h3,
.comment-section h3,
.admin-reply-section h3 {
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #ddd;
}

.rating-display {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stars {
    display: flex;
    gap: 2px;
}

.star {
    font-size: 24px;
    color: #ddd;
}

.star.filled {
    color: #ffc107;
}

.rating-text {
    color: #333;
    font-weight: 600;
    font-size: 16px;
}

.comment-content,
.admin-reply-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #007bff;
    color: #333;
    line-height: 1.6;
}

.admin-reply-content {
    border-left-color: #28a745;
}

.review-images-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.review-images-section h4 {
    color: #333;
    margin-bottom: 15px;
    font-size: 16px;
}

.review-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 15px;
}

.review-image-item {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.review-image-item:hover {
    border-color: #007bff;
    transform: scale(1.05);
}

.review-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.review-actions {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    margin-top: 20px;
}

.review-actions h3 {
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #ddd;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
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

@media (max-width: 768px) {
    .review-detail {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>

<script>
function showReplyModal(currentReply) {
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

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
