<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php';

if (!$product) {
    echo '<div class="container"><h3>Không tìm thấy sản phẩm</h3></div>';
    require_once ROOT_PATH . 'app/views/shared/frontend/footer.php';
    exit;
}

// Function để tính màu chữ tương phản
function getContrastColor($hexColor) {
    // Kiểm tra null hoặc empty
    if (empty($hexColor) || $hexColor === null) {
        return '#333';
    }
    
    // Loại bỏ # nếu có
    $hexColor = ltrim($hexColor, '#');
    
    // Nếu không phải hex color hợp lệ, trả về màu mặc định
    if (!preg_match('/^[0-9A-Fa-f]{6}$/', $hexColor)) {
        return '#333';
    }
    
    // Chuyển đổi hex sang RGB
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));
    
    // Tính độ sáng (luminance)
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    
    // Trả về màu trắng nếu nền tối, màu đen nếu nền sáng
    return $luminance > 0.5 ? '#333' : '#fff';
}
?>
<section class="product-detail-section">
    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>home">Trang chủ</a></li>
                <span class="">&#8594;</span>
                <li class="breadcrumb-item" aria-current="page">Tất cả sản phẩm</li>
                <span class="">&#8594;</span>
                <li class="breadcrumb-item" aria-current="page">Chi tiết sản phẩm</li>
                <!-- <li class="breadcrumb-item active" aria-current="page">Tất cả sản phẩm</li> -->
            </ol>
        </nav>
    <div class="container product-detail-container mt-4">
        <div class="row">
            <!-- Ảnh sản phẩm -->
            <div class="col-lg-6 col-md-6">
                <div class="product-images">
                    <div class="main-image mb-3">
                        <?php 
                        // Lấy ảnh primary hoặc ảnh đầu tiên
                        $mainImage = null;
                        if (!empty($product->imagesByColor)) {
                            foreach($product->imagesByColor as $colorImages) {
                                foreach($colorImages as $img) {
                                    if ($img->is_primary) {
                                        $mainImage = $img;
                                        break 2;
                                    }
                                }
                            }
                            // Nếu không có primary, lấy ảnh đầu tiên
                            if (!$mainImage) {
                                $firstGroup = reset($product->imagesByColor);
                                $mainImage = $firstGroup ? $firstGroup[0] : null;
                            }
                        }
                        $imagePath = $mainImage ? $mainImage->anh_path : ($product->sanpham_anh ?? 'no-image.svg');
                        ?>
                        <img src="<?= BASE_URL ?>assets/uploads/<?= $imagePath ?>" 
                            alt="<?= htmlspecialchars($product->sanpham_tieude) ?>" 
                            class="img-fluid main-product-image"
                            id="mainProductImage"
                            onerror="this.src='<?= BASE_URL ?>assets/images/no-image.svg'"
                            style="max-height: 600px; width: 100%; object-fit: contain; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </div>
                    
                    <div class="product-thumbnails">
                        <?php 
                        if (!empty($product->imagesByColor)):
                            foreach($product->imagesByColor as $colorId => $colorImages): 
                                foreach($colorImages as $image):
                        ?>
                            <img src="<?= BASE_URL ?>assets/uploads/<?= $image->anh_path ?>" 
                                alt="<?= htmlspecialchars($product->sanpham_tieude) ?>" 
                                class="product-thumbnail <?= $image->is_primary ? 'active' : '' ?>"
                                data-color-id="<?= $colorId ?>"
                                onclick="changeMainImage(this)">
                        <?php 
                                endforeach; 
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="col-lg-6 col-md-6">
                <div class="product-info">
                    <h1 class="product-title" style="font-size: 28px; font-weight: 600; margin-bottom: 15px;"><?= htmlspecialchars($product->sanpham_tieude) ?></h1>
                    
                    <div class="product-sku mb-3">
                        <span class="text-muted">Mã sản phẩm: <strong style="color: #333;"><?= htmlspecialchars($product->sanpham_ma) ?></strong></span>
                    </div>

                    <div class="product-price mb-4">
                        <span class="price-current"><?= number_format($product->sanpham_gia, 0, ',', '.') ?>đ</span>
                        <?php if(isset($product->sanpham_gia_goc) && $product->sanpham_gia_goc > $product->sanpham_gia): ?>
                            <span class="price-old"><?= number_format($product->sanpham_gia_goc, 0, ',', '.') ?>đ</span>
                            <span class="price-discount">-<?= round((($product->sanpham_gia_goc - $product->sanpham_gia) / $product->sanpham_gia_goc) * 100) ?>%</span>
                        <?php endif; ?>
                    </div>

                    <!-- Chọn màu -->
                    <?php if(!empty($product->linkedColors) && !empty($product->imagesByColor)): ?>
                    <div class="product-options mb-4">
                        <label class="font-weight-bold d-block mb-3" style="font-size: 16px;">Chọn màu:</label>
                        <div class="color-selection">
                            <?php 
                            $displayedColors = [];
                            $isFirst = true;
                            foreach($product->linkedColors as $color): 
                                // Tránh trùng màu
                                if (in_array($color->color_id, $displayedColors)) continue;
                                $displayedColors[] = $color->color_id;
                                
                                // Kiểm tra màu có ảnh không
                                $colorImages = isset($product->imagesByColor[$color->color_id]) ? $product->imagesByColor[$color->color_id] : [];
                                $hasImages = !empty($colorImages);
                                
                                // Debug: Kiểm tra dữ liệu màu
                                // echo "<!-- Debug: color_id=" . $color->color_id . ", color_ten=" . $color->color_ten . ", color_ma=" . ($color->color_ma ?? 'NULL') . " -->";
                            ?>
                                <button type="button" 
                                        class="color-option <?= $isFirst ? 'active' : '' ?>" 
                                        data-color-id="<?= $color->color_id ?>"
                                        data-color-name="<?= htmlspecialchars($color->color_ten) ?>"
                                        <?= !$hasImages ? 'disabled title="Không có ảnh"' : '' ?>
                                        onclick="selectColor(this, <?= $color->color_id ?>)"
                                        style="background-color: <?= htmlspecialchars($color->color_ma ?? '#ccc') ?>; color: <?= getContrastColor($color->color_ma ?? '#ccc') ?>;">
                                    <?= htmlspecialchars($color->color_ten) ?>
                                </button>
                            <?php 
                                if ($isFirst) $isFirst = false;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- *** THÊM MỚI: Chọn size *** -->
                    <div class="product-options mb-4" id="size-selection-container" style="display: none;">
                        <label class="font-weight-bold d-block mb-3" style="font-size: 16px;">Chọn size:</label>
                        <div id="size-selection" class="size-selection">
                            <p class="text-muted">Vui lòng chọn màu trước...</p>
                        </div>
                    </div>

                    <!-- Thêm vào giỏ hàng -->
                    <!-- <div class="product-actions mb-4">
                        <button type="buton" class="btn btn-primary btn-lg btn-add-to-cart" onclick="addToCart(<?= $product->sanpham_id ?>)" >
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                        </button>
                    </div> -->

                    <div class="mb-4">
                        <button type="buton" class="btn btn-primary btn-lg btn-add-to-cart" onclick="addToCart(<?= $product->sanpham_id ?>)" >
                            <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                        </button>
                    </div>

                    <!-- Mô tả ngắn -->
                    <?php if(!empty($product->sanpham_chitiet)): ?>
                    <div class="product-description mt-4">
                        <h5 style="font-size: 18px; font-weight: 600; margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">Mô tả sản phẩm</h5>
                        <div><?= nl2br(htmlspecialchars($product->sanpham_chitiet)) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="product-reviews-section">
    <div class="container">
        <div class="reviews-header">
            <h2>Đánh giá sản phẩm</h2>
            <a href="<?= BASE_URL ?>review/productReviews/<?= $product->sanpham_id ?>" class="view-all-reviews">
                Xem tất cả đánh giá
            </a>
        </div>

        <?php 
        // Lấy thống kê đánh giá
        $reviewModel = new ReviewModel();
        $ratingStats = $reviewModel->getProductRatingStats($product->sanpham_id);
        $recentReviews = $reviewModel->getProductReviews($product->sanpham_id, 1, 3, 0);
        
        ?>

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
                    // Truy cập thuộc tính đúng cách
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

        <div class="recent-reviews">
            <h3>Đánh giá gần đây</h3>
            <?php if (!empty($recentReviews)): ?>
                <?php foreach ($recentReviews as $review): ?>
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
                                <?= date('d/m/Y', strtotime($review->created_at)) ?>
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
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="no-reviews">
            <p>Chưa có đánh giá nào cho sản phẩm này.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
// Global variables
let selectedVariantId = null;
let currentProductId = <?= $product->sanpham_id ?>;
let currentColorId = null;

function changeMainImage(thumbnail) {
    const mainImg = document.getElementById('mainProductImage');
    mainImg.src = thumbnail.src;
    
    // Remove active class from all thumbnails
    document.querySelectorAll('.product-thumbnail').forEach(t => t.classList.remove('active'));
    thumbnail.classList.add('active');
}

function selectColor(button, colorId) {
    // Remove active class from all color buttons
    document.querySelectorAll('.color-option').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    
    // Store current color
    currentColorId = colorId;
    
    // Filter thumbnails by color
    const thumbnails = document.querySelectorAll('.product-thumbnail');
    thumbnails.forEach(thumb => {
        const thumbColorId = parseInt(thumb.getAttribute('data-color-id'));
        if (thumbColorId === colorId) {
            thumb.style.display = 'inline-block';
        } else {
            thumb.style.display = 'none';
        }
    });
    
    // Set first visible thumbnail as main image
    const firstVisible = Array.from(thumbnails).find(t => t.style.display !== 'none');
    if (firstVisible) {
        changeMainImage(firstVisible);
    }
    
    // Load sizes for selected color
    loadSizesByColor(currentProductId, colorId);
}

// Load sizes available for selected color
function loadSizesByColor(productId, colorId) {
    const sizeContainer = document.getElementById('size-selection');
    const sizeContainerWrapper = document.getElementById('size-selection-container');
    
    // Show loading
    sizeContainer.innerHTML = '<p class="text-muted"><i class="fas fa-spinner fa-spin"></i> Đang tải...</p>';
    sizeContainerWrapper.style.display = 'block';
    
    // Reset selected variant
    selectedVariantId = null;
    
    fetch(`<?= BASE_URL ?>ajax/get-sizes-by-color.php?product_id=${productId}&color_id=${colorId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                renderSizeButtons(data.data);
            } else {
                sizeContainer.innerHTML = '<p class="text-danger">❌ Không có size nào cho màu này</p>';
            }
        })
        .catch(error => {
            console.error('Error loading sizes:', error);
            sizeContainer.innerHTML = '<p class="text-danger">❌ Lỗi khi tải sizes</p>';
        });
}

// Render size buttons with stock info
function renderSizeButtons(sizes) {
    const sizeContainer = document.getElementById('size-selection');
    
    let html = '<div class="size-buttons-grid" style="display: flex; gap: 10px; flex-wrap: wrap;">';
    
    sizes.forEach(size => {
        const isAvailable = size.trang_thai === 1 && size.ton_kho > 0;
        const btnClass = isAvailable ? 'size-btn-available' : 'size-btn-disabled';
        const disabled = !isAvailable ? 'disabled' : '';
        const stockText = isAvailable ? `Còn ${size.ton_kho}` : 'Hết hàng';
        
        html += `
            <button type="button" 
                    class="size-option ${btnClass}" 
                    data-variant-id="${size.variant_id}"
                    data-size-name="${size.size_ten}"
                    data-stock="${size.ton_kho}"
                    ${disabled}
                    onclick="selectSize(this, ${size.variant_id})"
                    style="padding: 12px 20px; border: 2px solid ${isAvailable ? '#ddd' : '#ccc'}; 
                           border-radius: 5px; background: ${isAvailable ? 'white' : '#f5f5f5'}; 
                           cursor: ${isAvailable ? 'pointer' : 'not-allowed'}; 
                           font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px; min-width: 80px;
                           transition: all 0.3s ease;">
                <div style="font-weight: 600; font-size: 16px;">${size.size_ten}</div>
                <div style="font-size: 11px; color: ${isAvailable ? '#28a745' : '#999'}; margin-top: 2px;">${stockText}</div>
            </button>
        `;
    });
    
    html += '</div>';
    sizeContainer.innerHTML = html;
}

// Select size and store variant_id
function selectSize(button, variantId) {
    // Remove active class from all size buttons
    document.querySelectorAll('.size-option').forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');
    
    // Store selected variant
    selectedVariantId = variantId;
    
    console.log('Selected variant ID:', selectedVariantId);
}

// Initialize: show first color's images
window.addEventListener('DOMContentLoaded', function() {
    const firstColorBtn = document.querySelector('.color-option.active');
    if (firstColorBtn) {
        const colorId = parseInt(firstColorBtn.getAttribute('data-color-id'));
        currentColorId = colorId;
        selectColor(firstColorBtn, colorId);
    }
});

function addToCart(productId) {
    const selectedColor = document.querySelector('.color-option.active');
    if (!selectedColor) {
        alert('⚠️ Vui lòng chọn màu sắc');
        return;
    }
    
    const selectedSize = document.querySelector('.size-option.active');
    if (!selectedSize) {
        alert('⚠️ Vui lòng chọn size');
        return;
    }
    
    if (!selectedVariantId) {
        alert('❌ Lỗi: Không xác định được variant');
        return;
    }
    
    // Disable button để tránh double-click
    const btn = document.querySelector('.btn-add-to-cart');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
    
    // Call AJAX với variant_id
    fetch('<?= BASE_URL ?>ajax/cart_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&variant_id=${selectedVariantId}&quantity=1`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`✅ ${data.message}\n\nTổng giỏ hàng: ${data.cart_count} sản phẩm`);
            
            // Update cart count in header (nếu có)
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(el => {
                el.textContent = data.cart_count;
            });
        } else {
            alert(`❌ ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Lỗi kết nối. Vui lòng thử lại.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng';
    });
}
</script>

<style>

.product-detail-section{
    padding: 100px 0 0;
}

.breadcrumb{
    margin-bottom: 50px;
    display: flex;
    align-items: center;
    list-style: none;
    padding: 0;
}
.breadcrumb li{
    margin: 0 12px;
    font-size: 12px;
}
.product-detail-container {
    padding: 30px 0;
}

.product-thumbnails {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 15px;
}

.product-thumbnail {
    width: 100px;
    height: 100px;
    object-fit: cover;
    cursor: pointer;
    border: 3px solid transparent;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.product-thumbnail:hover {
    border-color: #007bff;
    transform: scale(1.05);
}

.product-thumbnail.active {
    border-color: #007bff;
    box-shadow: 0 0 10px rgba(0,123,255,0.3);
}

.color-selection {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-start;
}

.color-option {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 500;
    margin: 5px;
    vertical-align: top;
    min-width: 80px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.color-option:hover:not(:disabled) {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

.color-option.active {
    border-color: #007bff;
    border-width: 3px;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,123,255,0.4);
}

.color-option:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: #f5f5f5;
}

/* Color swatch styles removed - now using background color directly on button */

/* *** THÊM MỚI: Size selection styles *** */
.size-selection {
    margin-top: 10px;
}

.size-option {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 80px;
    padding: 12px 20px;
    border: 2px solid #ddd;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Segoe UI', Arial, sans-serif;
}

.size-option:hover:not(:disabled) {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

.size-option.active {
    border-color: #007bff;
    background: linear-gradient(135deg, #f0f8ff 0%, #e3f2fd 100%);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    font-weight: bold;
}

.size-option:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f5f5f5;
    border-color: #ccc;
}

.size-btn-available:hover {
    background: linear-gradient(135deg, #f0f8ff 0%, #e3f2fd 100%);
}

.size-btn-disabled {
    text-decoration: line-through;
}

.product-price {
    padding: 15px 0;
    border-top: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
}

.product-price .price-current {
    font-size: 32px;
    font-weight: 700;
    color: #d32f2f;
}

.product-price .price-old {
    font-size: 20px;
    text-decoration: line-through;
    color: #999;
    margin-left: 12px;
}

.product-price .price-discount {
    font-size: 18px;
    color: white;
    background: #d32f2f;
    padding: 4px 12px;
    border-radius: 20px;
    margin-left: 12px;
    font-weight: 600;
}

.btn-add-to-cart {
    border-radius: 8px;
    transition: all 0.5s ease;
    width: 150px;
    height: 40px;
    margin: 20px 0 12px;
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    background-color: #ffff;
    border: 2px solid black;
    cursor: pointer;
}

.btn-add-to-cart:hover {
    background-color: black;
    color: #ffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,123,255,0.4);
}

.product-description {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    line-height: 1.6;
}

.main-product-image {
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .product-thumbnail {
        width: 70px;
        height: 70px;
    }
    
    .product-price .price-current {
        font-size: 24px;
    }
}

/* Reviews Section Styles */
.product-reviews-section {
    padding: 40px 0;
    background: #f8f9fa;
}

.reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.reviews-header h2 {
    color: #333;
    margin: 0;
    font-size: 24px;
}

.view-all-reviews {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    padding: 8px 16px;
    border: 1px solid #007bff;
    border-radius: 4px;
    transition: all 0.2s;
}

.view-all-reviews:hover {
    background: #007bff;
    color: white;
    text-decoration: none;
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

.recent-reviews {
    background: #fff;
    padding: 30px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.recent-reviews h3 {
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.review-item {
    border-bottom: 1px solid #f0f0f0;
    padding: 20px 0;
}

.review-item:last-child {
    border-bottom: none;
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
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

@media (max-width: 768px) {
    .rating-overview {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .rating-breakdown {
        padding-left: 0;
    }
    
    .reviews-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .review-header {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<!-- Image Modal -->
<div id="imageModal" class="image-modal">
    <div class="modal-content">
        <span class="close" onclick="closeImageModal()">&times;</span>
        <img id="modalImage" src="" alt="Ảnh đánh giá">
    </div>
</div>

<style>
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
