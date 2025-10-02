<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<div class="container" style="padding: 20px 0;">
    <div class="row">
        <div class="col-12">
            <h2 class="page-title">Giỏ hàng của bạn</h2>
            
            <!-- Thông báo -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (empty($cartItems)): ?>
                <!-- Giỏ hàng trống -->
                <div class="empty-cart text-center py-5">
                    <div class="empty-cart-icon mb-4">
                        <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc;"></i>
                    </div>
                    <h4>Giỏ hàng của bạn đang trống</h4>
                    <p class="text-muted mb-4">Hãy thêm một số sản phẩm vào giỏ hàng để bắt đầu mua sắm</p>
                    <a href="<?= BASE_URL ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                    </a>
                </div>
            <?php else: ?>
                <!-- Danh sách sản phẩm trong giỏ hàng -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="cart-items">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item card mb-3" data-product-id="<?= $item['product_id'] ?>">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <!-- Hình ảnh sản phẩm -->
                                            <div class="col-md-2">
                                                <img src="<?= BASE_URL . 'assets/uploads/' . $item['image'] ?>" 
                                                     alt="<?= htmlspecialchars($item['name']) ?>" 
                                                     class="img-fluid rounded cart-item-image"
                                                     onerror="this.src='<?= BASE_URL ?>assets/images/placeholder.jpg'">
                                            </div>
                                            
                                            <!-- Thông tin sản phẩm -->
                                            <div class="col-md-4">
                                                <h5 class="cart-item-name mb-1">
                                                    <a href="<?= BASE_URL ?>product/detail/<?= $item['product_id'] ?>" 
                                                       class="text-decoration-none text-dark">
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </a>
                                                </h5>
                                                <?php if (!empty($item['category'])): ?>
                                                    <small class="text-muted"><?= htmlspecialchars($item['category']) ?></small>
                                                <?php endif; ?>
                                                <div class="cart-item-price mt-2">
                                                    <span class="price"><?= number_format($item['price'], 0, ',', '.') ?> ₫</span>
                                                </div>
                                            </div>
                                            
                                            <!-- Số lượng -->
                                            <div class="col-md-3">
                                                <div class="quantity-controls d-flex align-items-center">
                                                    <form method="POST" action="<?= BASE_URL ?>cart/update" class="d-inline">
                                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                        <div class="input-group" style="width: 120px;">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" 
                                                                    data-action="decrease" data-product-id="<?= $item['product_id'] ?>">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            <input type="number" 
                                                                   name="quantity" 
                                                                   value="<?= $item['quantity'] ?>" 
                                                                   min="1" 
                                                                   max="99"
                                                                   class="form-control text-center quantity-input"
                                                                   data-product-id="<?= $item['product_id'] ?>">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm quantity-btn" 
                                                                    data-action="increase" data-product-id="<?= $item['product_id'] ?>">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            
                                            <!-- Tổng tiền và nút xóa -->
                                            <div class="col-md-3 text-end">
                                                <div class="cart-item-total mb-2">
                                                    <strong class="text-primary"><?= number_format($item['total'], 0, ',', '.') ?> ₫</strong>
                                                </div>
                                                <form method="POST" action="<?= BASE_URL ?>cart/remove" class="d-inline">
                                                    <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                            onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Nút xóa tất cả -->
                        <div class="text-end mb-4">
                            <form method="POST" action="<?= BASE_URL ?>cart/clear" class="d-inline">
                                <button type="submit" class="btn btn-outline-danger" 
                                        onclick="return confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')">
                                    <i class="fas fa-trash-alt me-2"></i>Xóa tất cả
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Tổng kết giỏ hàng -->
                    <div class="col-lg-4">
                        <div class="cart-summary card">
                            <div class="card-header">
                                <h5 class="mb-0">Tổng kết đơn hàng</h5>
                            </div>
                            <div class="card-body">
                                <div class="summary-row d-flex justify-content-between mb-2">
                                    <span>Số lượng sản phẩm:</span>
                                    <span><?= count($cartItems) ?></span>
                                </div>
                                <div class="summary-row d-flex justify-content-between mb-2">
                                    <span>Tổng số lượng:</span>
                                    <span><?= $cartCount ?></span>
                                </div>
                                <hr>
                                <div class="summary-total d-flex justify-content-between mb-3">
                                    <strong>Tổng tiền:</strong>
                                    <strong class="text-primary fs-5"><?= number_format($totalAmount, 0, ',', '.') ?> ₫</strong>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="<?= BASE_URL ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                                    </a>
                                    <a href="<?= BASE_URL ?>checkout" class="btn btn-primary btn-lg">
                                        <i class="fas fa-credit-card me-2"></i>Thanh toán
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.cart-item {
    transition: all 0.3s ease;
}

.cart-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.cart-item-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
}

.quantity-controls .input-group {
    border-radius: 0.375rem;
}

.quantity-btn {
    border-radius: 0;
}

.quantity-btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.quantity-btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

.quantity-input {
    border-left: 0;
    border-right: 0;
}

.cart-summary {
    position: sticky;
    top: 20px;
}

.summary-row {
    font-size: 0.9rem;
}

.empty-cart {
    background: #f8f9fa;
    border-radius: 0.5rem;
    margin: 2rem 0;
}

.page-title {
    color: #333;
    margin-bottom: 2rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .cart-item .row > div {
        margin-bottom: 1rem;
    }
    
    .cart-item .col-md-3.text-end {
        text-align: left !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý nút tăng/giảm số lượng
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            const productId = this.dataset.productId;
            const input = document.querySelector(`input[data-product-id="${productId}"]`);
            let quantity = parseInt(input.value);
            
            if (action === 'increase') {
                quantity = Math.min(quantity + 1, 99);
            } else if (action === 'decrease') {
                quantity = Math.max(quantity - 1, 1);
            }
            
            input.value = quantity;
            
            // Tự động submit form để cập nhật
            const form = input.closest('form');
            form.submit();
        });
    });
    
    // Xử lý thay đổi số lượng trực tiếp
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const quantity = parseInt(this.value);
            if (quantity < 1) {
                this.value = 1;
            } else if (quantity > 99) {
                this.value = 99;
            }
            
            // Tự động submit form để cập nhật
            const form = this.closest('form');
            form.submit();
        });
    });
});
</script>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>
