<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php';

if (!$product) {
    echo '<div class="container"><h3>Không tìm thấy sản phẩm</h3></div>';
    require_once ROOT_PATH . 'app/views/shared/frontend/footer.php';
    exit;
}
?>

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
                        ?>
                            <button type="button" 
                                    class="color-option <?= $isFirst ? 'active' : '' ?>" 
                                    data-color-id="<?= $color->color_id ?>"
                                    data-color-name="<?= htmlspecialchars($color->color_ten) ?>"
                                    <?= !$hasImages ? 'disabled title="Không có ảnh"' : '' ?>
                                    onclick="selectColor(this, <?= $color->color_id ?>)"
                                    style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border: 2px solid #ddd; border-radius: 5px; background: white; cursor: pointer; margin: 5px; font-family: 'Segoe UI', Arial, sans-serif; font-size: 14px;">
                                <span class="color-swatch" style="background: <?= htmlspecialchars($color->color_ma ?? '#ccc') ?>; width: 30px; height: 30px; display: inline-block; border-radius: 50%; border: 2px solid #ddd;"></span>
                                <span class="color-name"><?= htmlspecialchars($color->color_ten) ?></span>
                            </button>
                        <?php 
                            if ($isFirst) $isFirst = false;
                        endforeach; 
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Thêm vào giỏ hàng -->
                <div class="product-actions mb-4">
                    <button type="button" class="btn btn-primary btn-lg btn-add-to-cart" onclick="addToCart(<?= $product->sanpham_id ?>)" style="padding: 12px 40px; font-size: 16px; font-weight: 600;">
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

<script>
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
}

// Initialize: show first color's images
window.addEventListener('DOMContentLoaded', function() {
    const firstColorBtn = document.querySelector('.color-option.active');
    if (firstColorBtn) {
        const colorId = parseInt(firstColorBtn.getAttribute('data-color-id'));
        selectColor(firstColorBtn, colorId);
    }
});

function addToCart(productId) {
    const selectedColor = document.querySelector('.color-option.active');
    if (!selectedColor) {
        alert('Vui lòng chọn màu sắc');
        return;
    }
    
    // TODO: Implement add to cart logic
    alert('Thêm vào giỏ hàng thành công!');
}
</script>

<style>
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
}

.color-option {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.color-option:hover:not(:disabled) {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.2);
}

.color-option.active {
    border-color: #007bff;
    background: linear-gradient(135deg, #f0f8ff 0%, #e3f2fd 100%);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
}

.color-option:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: #f5f5f5;
}

.color-swatch {
    box-shadow: 0 2px 4px rgba(0,0,0,0.15);
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
    transition: all 0.3s ease;
}

.btn-add-to-cart:hover {
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
</style>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
