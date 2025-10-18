<?php require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; ?>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/frontend-components.css">

<section class="product-reviews-section">
    <div class="container">
        <div class="reviews-header">
            <h1>Đánh giá sản phẩm</h1>
            <a href="<?= BASE_URL ?>product/detail/<?= is_object($product) ? $product->sanpham_id : $product['sanpham_id'] ?>" class="back-link">
                ← Quay lại sản phẩm
            </a>
        </div>

        <div class="product-summary">
            <div class="product-info">
                <img src="<?= BASE_URL ?>assets/uploads/<?= is_object($product) ? $product->sanpham_anh : $product['sanpham_anh'] ?>" 
                     alt="<?= htmlspecialchars(is_object($product) ? $product->sanpham_tieude : $product['sanpham_tieude']) ?>">
                <div class="product-details">
                    <h2><?= htmlspecialchars(is_object($product) ? $product->sanpham_tieude : $product['sanpham_tieude']) ?></h2>
                    <p class="product-code">Mã: <?= htmlspecialchars(is_object($product) ? $product->sanpham_ma : $product['sanpham_ma']) ?></p>
                </div>
            </div>
        </div>

        <?php if ($ratingStats && $ratingStats->total_reviews > 0): ?>
        <div class="rating-overview">
            <div class="rating-summary">
                <div class="average-rating">
                    <span class="rating-number"><?= number_format($ratingStats->avg_rating, 1) ?></span>
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= $ratingStats->avg_rating ? 'filled' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>
                    <p class="rating-text">Dựa trên <?= $ratingStats->total_reviews ?> đánh giá</p>
                </div>
            </div>

            <div class="rating-breakdown">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <?php 
                    $count = 0;
                    $property = $i . '_star';
                    if (isset($ratingStats->$property)) {
                        $count = $ratingStats->$property;
                    }
                    $percentage = $ratingStats->total_reviews > 0 ? ($count / $ratingStats->total_reviews) * 100 : 0;
                    ?>
                    <div class="rating-bar">
                        <span class="rating-label"><?= $i ?> sao</span>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: <?= $percentage ?>%"></div>
                        </div>
                        <span class="rating-count"><?= $count ?></span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="no-reviews">
            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
        </div>
        <?php endif; ?>

        <div class="reviews-list">
            <h3>Đánh giá từ khách hàng</h3>
            
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <div class="reviewer-name">
                                <?= htmlspecialchars($review->fullname) ?>
                                <?php if ($review->is_verified_purchase): ?>
                                    <span class="verified-badge">✓ Đã mua hàng</span>
                                <?php endif; ?>
                            </div>
                            <div class="review-date">
                                <?= date('d/m/Y H:i', strtotime($review->created_at)) ?>
                            </div>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star <?= $i <= $review->rating ? 'filled' : '' ?>">★</span>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="review-content">
                        <p><?= nl2br(htmlspecialchars($review->comment)) ?></p>
                        
                        <?php if (!empty($review->review_images)): ?>
                            <?php 
                            $images = json_decode($review->review_images, true);
                            if (is_array($images) && !empty($images)):
                            ?>
                            <div class="review-images">
                                <?php foreach ($images as $image): ?>
                                    <div class="review-image-item">
                                        <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($image) ?>" 
                                             alt="Ảnh đánh giá" 
                                             onclick="openImageModal('<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($image) ?>')">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($review->admin_reply): ?>
                    <div class="admin-reply">
                        <div class="reply-header">
                            <strong>Phản hồi từ IVY moda:</strong>
                        </div>
                        <div class="reply-content">
                            <?= nl2br(htmlspecialchars($review->admin_reply)) ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-reviews">
                    <p>Chưa có đánh giá nào.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Image Modal -->
<div id="imageModal" class="image-modal">
    <div class="modal-content">
        <span class="close" onclick="closeImageModal()">&times;</span>
        <img id="modalImage" src="" alt="Ảnh đánh giá">
    </div>
</div>

<style>
.product-reviews-section {
    padding: 20px 0;
    min-height: 80vh;
}

.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.reviews-header h1 {
    color: #333;
    margin: 0;
}

.back-link {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
}

.back-link:hover {
    text-decoration: underline;
}

.product-summary {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.product-info {
    display: flex;
    align-items: center;
}

.product-info img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 20px;
}

.product-details h2 {
    color: #333;
    margin: 0 0 10px 0;
    font-size: 20px;
}

.product-code {
    color: #666;
    margin: 0;
    font-size: 14px;
}

.rating-overview {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    margin-bottom: 30px;
}

.rating-summary {
    text-align: center;
}

.average-rating {
    margin-bottom: 20px;
}

.rating-number {
    font-size: 48px;
    font-weight: bold;
    color: #333;
    display: block;
}

.stars {
    margin: 10px 0;
}

.star {
    font-size: 24px;
    color: #ddd;
    margin-right: 2px;
}

.star.filled {
    color: #ffc107;
}

.rating-text {
    color: #666;
    margin: 0;
}

.rating-breakdown {
    padding-left: 20px;
}

.rating-bar {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.rating-label {
    width: 60px;
    font-size: 14px;
    color: #666;
}

.bar-container {
    flex: 1;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    margin: 0 10px;
    overflow: hidden;
}

.bar-fill {
    height: 100%;
    background: #ffc107;
    transition: width 0.3s ease;
}

.rating-count {
    width: 30px;
    text-align: right;
    font-size: 14px;
    color: #666;
}

.reviews-list h3 {
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.review-item {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.reviewer-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.verified-badge {
    background: #28a745;
    color: white;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 3px;
    margin-left: 8px;
}

.review-date {
    color: #666;
    font-size: 14px;
}

.review-rating .star {
    font-size: 16px;
    color: #ddd;
    margin-right: 1px;
}

.review-rating .star.filled {
    color: #ffc107;
}

.review-content p {
    color: #333;
    line-height: 1.6;
    margin: 0;
}

.admin-reply {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 15px;
    margin-top: 15px;
    border-radius: 0 4px 4px 0;
}

.reply-header {
    color: #007bff;
    font-weight: 600;
    margin-bottom: 8px;
}

.reply-content {
    color: #333;
    line-height: 1.6;
}

.review-images {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.review-image-item {
    width: 80px;
    height: 80px;
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

.no-reviews {
    text-align: center;
    padding: 40px;
    color: #666;
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
    .rating-overview {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .rating-breakdown {
        padding-left: 0;
    }
    
    .product-info {
        flex-direction: column;
        text-align: center;
    }
    
    .product-info img {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .review-header {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<script>
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    modal.style.display = 'block';
    modalImg.src = imageSrc;
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
}

// Close modal when clicking outside the image
window.onclick = function(event) {
    const modal = document.getElementById('imageModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
</script>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
