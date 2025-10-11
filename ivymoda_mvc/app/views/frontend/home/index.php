<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\frontend\home\index.php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php';
?>

<!-- Slider -->
<section class="slider">
    <div class="aspect-ratio-169">
        <img src="<?= BASE_URL ?>assets/images/slide1.jpg" alt="">
        <img src="<?= BASE_URL ?>assets/images/slide2.jpg" alt="">
        <img src="<?= BASE_URL ?>assets/images/slide3.jpg" alt="">
    </div>
    <div class="dot-container">
        <div class="dot active"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</section>

<!-- Sản phẩm -->
<section class="featured-products py-5">
    <div class="container">
        <div class="section-header">
            <div class="section-title-wrapper">
                <h2 class="section-title">Sản phẩm của chúng tôi</h2>
                <p class="section-subtitle">Khám phá bộ sưu tập thời trang IVY moda</p>
            </div>
            <a href="<?= BASE_URL ?>product" class="view-all-btn">
                Xem tất cả <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="products-grid-balanced">
            <?php if(isset($featuredProducts) && count($featuredProducts) > 0): ?>
                <?php foreach($featuredProducts as $product): ?>
                    <div class="product-component">
                        <div class="product-card">
                            <div class="product-image-wrapper">
                                <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>" class="product-link">
                                    <?php 
                                    $imageToShow = $product->first_image ?? $product->sanpham_anh ?? '';
                                    if(!empty($imageToShow)): 
                                    ?>
                                        <img src="<?= BASE_URL ?>assets/uploads/<?= $imageToShow ?>" 
                                             class="product-image" 
                                             alt="<?= htmlspecialchars($product->sanpham_tieude) ?>"
                                             loading="lazy"
                                             onerror="this.src='<?= BASE_URL ?>assets/images/no-image.svg'">
                                    <?php else: ?>
                                        <img src="<?= BASE_URL ?>assets/images/no-image.svg" 
                                             class="product-image" 
                                             alt="No image"
                                             loading="lazy">
                                    <?php endif; ?>
                                </a>
                                
                                <!-- Badge giảm giá -->
                                <?php if(isset($product->sanpham_gia_goc) && $product->sanpham_gia_goc > $product->sanpham_gia): ?>
                                    <div class="product-discount-badge">
                                        <span class="discount-percent">-<?= $product->sanpham_giam_gia ?? 0 ?>%</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-content">
                                <h3 class="product-title">
                                    <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>" class="product-title-link">
                                        <?= htmlspecialchars(mb_substr($product->sanpham_tieude, 0, 45)) ?>
                                        <?= mb_strlen($product->sanpham_tieude) > 45 ? '...' : '' ?>
                                    </a>
                                </h3>
                                
                                <div class="product-category">
                                    <span class="category-tag"><?= htmlspecialchars($product->danhmuc_ten ?? 'N/A') ?></span>
                                    <?php if(!empty($product->loaisanpham_ten)): ?>
                                        <span class="category-tag"><?= htmlspecialchars($product->loaisanpham_ten) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-price-section">
                                    <div class="price-container">
                                        <span class="current-price">
                                            <?= number_format($product->sanpham_gia, 0, ',', '.') ?>đ
                                        </span>
                                        <?php if(isset($product->sanpham_gia_goc) && $product->sanpham_gia_goc > $product->sanpham_gia): ?>
                                            <span class="original-price">
                                                <?= number_format($product->sanpham_gia_goc, 0, ',', '.') ?>đ
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="product-actions">
                                    <button class="add-to-cart-btn" onclick="addToCart(<?= $product->sanpham_id ?>)">
                                        <i class="fas fa-shopping-cart"></i>
                                        <span>Thêm vào giỏ</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <div class="no-products-content">
                        <i class="fas fa-box-open"></i>
                        <h4>Không có sản phẩm nào</h4>
                        <p>Hiện tại chưa có sản phẩm nổi bật nào để hiển thị.</p>
                        <a href="<?= BASE_URL ?>product" class="btn btn-primary">Xem tất cả sản phẩm</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* CSS cho trang chủ - Layout sản phẩm đẹp */
.featured-products {
    background-color: #f8f9fa;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    position: relative;
    display: inline-block;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: linear-gradient(45deg, #007bff, #28a745);
    border-radius: 2px;
}

.product-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    border: 1px solid #e9ecef;
    position: relative;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #007bff;
}


/* ===== LAYOUT CÂN ĐỐI VÀ HÀI HÒA ===== */

/* Section Header */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f8f9fa;
}

.section-title-wrapper {
    flex: 1;
}

.section-title {
    font-size: 2.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(45deg, #007bff, #28a745);
    border-radius: 2px;
}

.section-subtitle {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
    font-weight: 400;
}

.view-all-btn {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.view-all-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    color: white;
    text-decoration: none;
}

/* Grid Layout Cân Đối */
.products-grid-balanced {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    width: 100%;
    box-sizing: border-box;
    padding: 0;
}

/* Product Component */
.product-component {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.product-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    overflow: hidden;
    border: 1px solid #f1f3f4;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    border-color: #007bff;
}

/* Image Wrapper */
.product-image-wrapper {
    position: relative;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: #f8f9fa;
    border-radius: 16px 16px 0 0;
}

.product-link {
    display: block;
    width: 100%;
    height: 100%;
    text-decoration: none;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
    border-radius: 16px 16px 0 0;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

/* Badges */
.product-discount-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 3;
}

.discount-percent {
    background: linear-gradient(45deg, #dc3545, #c82333);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.product-status-badges {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 3;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.featured {
    background: linear-gradient(45deg, #ffc107, #e0a800);
    color: #212529;
}

.status-badge.bestseller {
    background: linear-gradient(45deg, #28a745, #1e7e34);
    color: white;
}

.status-badge.new {
    background: linear-gradient(45deg, #17a2b8, #138496);
    color: white;
}

/* Quick Actions */
.product-quick-actions {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 4;
    display: flex;
    gap: 12px;
}

.product-card:hover .product-quick-actions {
    opacity: 1;
}

.quick-action-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.95);
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.quick-action-btn:hover {
    background: #007bff;
    color: white;
    transform: scale(1.1);
}

/* Product Content */
.product-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.product-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.4;
    color: #2c3e50;
}

.product-title-link {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-title-link:hover {
    color: #007bff;
    text-decoration: none;
}

.product-category {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.category-tag {
    background: #e9ecef;
    color: #6c757d;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.product-price-section {
    margin: 0.5rem 0;
}

.price-container {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.current-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #dc3545;
}

.original-price {
    font-size: 1rem;
    color: #6c757d;
    text-decoration: line-through;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 8px;
}

.stars-container {
    display: flex;
    gap: 2px;
}

.stars-container i {
    color: #ffc107;
    font-size: 0.9rem;
}

.rating-count {
    color: #6c757d;
    font-size: 0.85rem;
}

.product-actions {
    margin-top: auto;
    padding-top: 1rem;
}

.add-to-cart-btn {
    width: 100%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}

.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    background: linear-gradient(45deg, #0056b3, #004085);
}

/* No Products State */
.no-products {
    grid-column: 1 / -1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 4rem 2rem;
}

.no-products-content {
    text-align: center;
    color: #6c757d;
}

.no-products-content i {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    opacity: 0.5;
}

.no-products-content h4 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #495057;
}

.no-products-content p {
    margin-bottom: 2rem;
    font-size: 1rem;
}

/* ===== RESPONSIVE DESIGN ===== */

/* Large Desktop */
@media (min-width: 1400px) {
    .products-grid-balanced {
        grid-template-columns: repeat(5, 1fr);
        gap: 2.5rem;
    }
    
    .section-title {
        font-size: 2.5rem;
    }
}

/* Desktop */
@media (max-width: 1200px) {
    .products-grid-balanced {
        grid-template-columns: repeat(4, 1fr);
        gap: 1.8rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
}

/* Tablet */
@media (max-width: 992px) {
    .products-grid-balanced {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .view-all-btn {
        align-self: flex-end;
    }
    
    .product-content {
        padding: 1.25rem;
    }
}

/* Mobile Large */
@media (max-width: 768px) {
    .products-grid-balanced {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.2rem;
    }
    
    .section-title {
        font-size: 1.8rem;
    }
    
    .section-subtitle {
        font-size: 0.9rem;
    }
    
    .product-content {
        padding: 1rem;
        gap: 0.5rem;
    }
    
    .product-title {
        font-size: 1rem;
    }
    
    .current-price {
        font-size: 1.2rem;
    }
    
    .add-to-cart-btn {
        padding: 10px 16px;
        font-size: 0.9rem;
    }
}

/* Mobile Small */
@media (max-width: 576px) {
    .products-grid-balanced {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .section-header {
        text-align: center;
    }
    
    .section-title {
        font-size: 1.6rem;
    }
    
    .view-all-btn {
        align-self: center;
        padding: 10px 20px;
        font-size: 0.9rem;
    }
    
    .product-quick-actions {
        opacity: 1;
        position: static;
        transform: none;
        margin-top: 1rem;
        justify-content: center;
        gap: 1rem;
    }
    
    .quick-action-btn {
        width: 40px;
        height: 40px;
    }
    
    .product-content {
        padding: 1rem 0.75rem;
    }
    
    .product-title {
        font-size: 0.95rem;
        line-height: 1.3;
    }
    
    .current-price {
        font-size: 1.1rem;
    }
    
    .add-to-cart-btn {
        padding: 8px 14px;
        font-size: 0.85rem;
    }
}

/* Extra Small Mobile */
@media (max-width: 400px) {
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .section-title {
        font-size: 1.4rem;
    }
    
    .product-content {
        padding: 0.75rem 0.5rem;
    }
    
    .product-title {
        font-size: 0.9rem;
    }
    
    .current-price {
        font-size: 1rem;
    }
    
    .add-to-cart-btn {
        padding: 6px 12px;
        font-size: 0.8rem;
    }
}

/* Tắt toàn bộ animation để dự án đơn giản, dễ học */
</style>

<script>
// JavaScript cho trang chủ
function addToCart(productId) {
    // AJAX call để thêm vào giỏ hàng
    fetch('<?= BASE_URL ?>ajax/cart_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=add&product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hiển thị thông báo thành công
            showNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
            // Cập nhật số lượng giỏ hàng trên header
            updateCartCount();
        } else {
            showNotification('Có lỗi xảy ra: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng', 'error');
    });
}

function addToWishlist(productId) {
    // AJAX call để thêm vào danh sách yêu thích
    fetch('<?= BASE_URL ?>ajax/wishlist_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=add&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Đã thêm vào danh sách yêu thích!', 'success');
        } else {
            showNotification('Có lỗi xảy ra: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi thêm vào yêu thích', 'error');
    });
}

function quickView(productId) {
    // Mở modal xem nhanh sản phẩm
    window.open('<?= BASE_URL ?>product/detail/' + productId, '_blank');
}

function showNotification(message, type) {
    // Tạo thông báo toast
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    document.body.appendChild(toast);
    
    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function updateCartCount() {
    // Cập nhật số lượng giỏ hàng trên header
    fetch('<?= BASE_URL ?>ajax/cart_ajax.php?action=count')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.count;
                cartCount.style.display = data.count > 0 ? 'inline' : 'none';
            }
        }
    })
    .catch(error => console.error('Error updating cart count:', error));
}

// Khởi tạo khi trang load
document.addEventListener('DOMContentLoaded', function() {
    // Giữ mọi thứ đơn giản: không dùng IntersectionObserver/animation
});
</script>

<?php
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php';
?>