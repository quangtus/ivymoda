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
</style>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
