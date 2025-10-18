<?php require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; ?>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/frontend-components.css">

<section class="review-add-section">
    <div class="container">
        <div class="review-header">
            <h1>Đánh giá sản phẩm</h1>
            <p class="breadcrumb">
                <a href="<?= BASE_URL ?>user/profile">Tài khoản</a> > 
                <a href="<?= BASE_URL ?>user/orderDetail/<?= $orderId ?>">Chi tiết đơn hàng</a> > 
                Đánh giá sản phẩm
            </p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="review-content">
            <div class="product-info">
                <h3>Thông tin sản phẩm</h3>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?= BASE_URL ?>assets/uploads/<?= is_object($product) ? $product->sanpham_anh : $product['sanpham_anh'] ?>" 
                             alt="<?= htmlspecialchars(is_object($product) ? $product->sanpham_tieude : $product['sanpham_tieude']) ?>">
                    </div>
                    <div class="product-details">
                        <h4><?= htmlspecialchars(is_object($product) ? $product->sanpham_tieude : $product['sanpham_tieude']) ?></h4>
                        <p class="product-code">Mã: <?= htmlspecialchars(is_object($product) ? $product->sanpham_ma : $product['sanpham_ma']) ?></p>
                        <p class="product-price"><?= number_format(is_object($product) ? $product->sanpham_gia : $product['sanpham_gia']) ?>đ</p>
                    </div>
                </div>
            </div>

            <div class="review-form-container">
                <h3>Đánh giá của bạn</h3>
                <form action="<?= BASE_URL ?>review/submit" method="POST" class="review-form" enctype="multipart/form-data">
                    <input type="hidden" name="order_id" value="<?= $orderId ?>">
                    <input type="hidden" name="product_id" value="<?= $productId ?>">
                    
                    <div class="form-group">
                        <label>Đánh giá của bạn *</label>
                        <div class="rating-input">
                            <input type="radio" name="rating" value="5" id="star5" required>
                            <label for="star5" class="star">★</label>
                            
                            <input type="radio" name="rating" value="4" id="star4">
                            <label for="star4" class="star">★</label>
                            
                            <input type="radio" name="rating" value="3" id="star3">
                            <label for="star3" class="star">★</label>
                            
                            <input type="radio" name="rating" value="2" id="star2">
                            <label for="star2" class="star">★</label>
                            
                            <input type="radio" name="rating" value="1" id="star1">
                            <label for="star1" class="star">★</label>
                        </div>
                        <div class="rating-labels">
                            <span class="rating-text">Chọn số sao để đánh giá</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comment">Nhận xét chi tiết *</label>
                        <textarea name="comment" id="comment" rows="5" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="review_images">Ảnh đánh giá (tùy chọn)</label>
                        <div class="image-upload-container">
                            <input type="file" name="review_images[]" id="review_images" multiple accept="image/*" class="image-input">
                            <div class="upload-area" onclick="document.getElementById('review_images').click()">
                                <div class="upload-icon">📷</div>
                                <p>Nhấn để chọn ảnh hoặc kéo thả ảnh vào đây</p>
                                <small>Tối đa 5 ảnh, mỗi ảnh tối đa 5MB</small>
                            </div>
                            <div class="image-preview-container" id="imagePreviewContainer"></div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="history.back()">Hủy</button>
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.review-add-section {
    padding: 40px 0 20px 0;
    min-height: 80vh;
    margin-top: 20px;
}

.review-header {
    margin-bottom: 30px;
}

.review-header h1 {
    color: #333;
    margin-bottom: 10px;
}

.breadcrumb {
    color: #666;
    font-size: 14px;
}

.breadcrumb a {
    color: #007bff;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.review-content {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.product-info h3,
.review-form-container h3 {
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #007bff;
}

.product-card {
    display: flex;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.product-image {
    width: 120px;
    height: 120px;
    margin-right: 20px;
    flex-shrink: 0;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.product-details h4 {
    color: #333;
    margin-bottom: 10px;
    font-size: 18px;
}

.product-code {
    color: #666;
    font-size: 14px;
    margin-bottom: 5px;
}

.product-price {
    color: #e74c3c;
    font-weight: bold;
    font-size: 16px;
}

.review-form {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    margin-bottom: 10px;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input .star {
    font-size: 30px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
    margin-right: 5px;
}

.rating-input input[type="radio"]:checked ~ .star,
.rating-input .star:hover,
.rating-input .star:hover ~ .star {
    color: #ffc107;
}

.rating-labels {
    font-size: 14px;
    color: #666;
}

.rating-text {
    font-style: italic;
}

.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    resize: vertical;
    min-height: 120px;
}

.form-group textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.form-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    margin-top: 30px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Image Upload Styles */
.image-upload-container {
    margin-top: 10px;
}

.image-input {
    display: none;
}

.upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fafafa;
}

.upload-area:hover {
    border-color: #007bff;
    background: #f0f8ff;
}

.upload-area.dragover {
    border-color: #007bff;
    background: #e3f2fd;
}

.upload-icon {
    font-size: 48px;
    margin-bottom: 15px;
    color: #666;
}

.upload-area p {
    margin: 10px 0;
    color: #333;
    font-weight: 500;
}

.upload-area small {
    color: #666;
    font-size: 12px;
}

.image-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.image-preview {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #e9ecef;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-preview .remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-preview .remove-btn:hover {
    background: #c82333;
}

@media (max-width: 768px) {
    .review-content {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .product-card {
        flex-direction: column;
        text-align: center;
    }
    
    .product-image {
        width: 100%;
        height: 200px;
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .image-preview-container {
        justify-content: center;
    }
}
</style>

<script>
let selectedImages = [];

document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('review_images');
    const uploadArea = document.querySelector('.upload-area');
    const previewContainer = document.getElementById('imagePreviewContainer');

    // File input change event
    fileInput.addEventListener('change', handleFileSelect);

    // Drag and drop events
    uploadArea.addEventListener('dragover', handleDragOver);
    uploadArea.addEventListener('dragleave', handleDragLeave);
    uploadArea.addEventListener('drop', handleDrop);

    function handleFileSelect(e) {
        const files = Array.from(e.target.files);
        processFiles(files);
    }

    function handleDragOver(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    }

    function handleDragLeave(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    }

    function handleDrop(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        processFiles(files);
    }

    function processFiles(files) {
        // Validate file count
        if (selectedImages.length + files.length > 5) {
            alert('Tối đa 5 ảnh được phép');
            return;
        }

        files.forEach(file => {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Chỉ được phép upload ảnh');
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Kích thước ảnh không được vượt quá 5MB');
                return;
            }

            // Add to selected images
            selectedImages.push(file);
            createImagePreview(file);
        });

        // Update file input
        updateFileInput();
    }

    function createImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('div');
            preview.className = 'image-preview';
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-btn" onclick="removeImage(${selectedImages.length - 1})">×</button>
            `;
            previewContainer.appendChild(preview);
        };
        reader.readAsDataURL(file);
    }

    function removeImage(index) {
        selectedImages.splice(index, 1);
        updateFileInput();
        updatePreviews();
    }

    function updateFileInput() {
        const dt = new DataTransfer();
        selectedImages.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function updatePreviews() {
        previewContainer.innerHTML = '';
        selectedImages.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('div');
                preview.className = 'image-preview';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-btn" onclick="removeImage(${index})">×</button>
                `;
                previewContainer.appendChild(preview);
            };
            reader.readAsDataURL(file);
        });
    }

    // Make removeImage globally available
    window.removeImage = removeImage;
});
</script>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
