<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<div class="container" style="padding: 20px 0;">
    <div class="row">
        <div class="col-12">
            <h2 class="page-title">Thanh toán</h2>
            
            <!-- Thông báo lỗi -->
            <?php if (isset($_SESSION['checkout_errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($_SESSION['checkout_errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['checkout_errors']); ?>
            <?php endif; ?>
            
            <form method="POST" action="<?= BASE_URL ?>checkout/process">
                <div class="row">
                    <!-- Thông tin giao hàng -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Thông tin giao hàng</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="full_name">Họ và tên *</label>
                                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                                   value="<?= htmlspecialchars($user->user_hoten ?? '') ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="phone">Số điện thoại *</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?= htmlspecialchars($user->user_sdt ?? '') ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="customer_address">Địa chỉ giao hàng đầy đủ *</label>
                                    <textarea class="form-control" id="customer_address" name="customer_address" 
                                              rows="3" required placeholder="Ví dụ: 123 Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP.HCM"><?= htmlspecialchars($user->address ?? '') ?></textarea>
                                    <small class="form-text text-muted">Vui lòng nhập đầy đủ: Số nhà, Tên đường, Phường/Xã, Quận/Huyện, Tỉnh/TP</small>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="notes">Ghi chú</label>
                                    <textarea class="form-control" id="notes" name="notes" 
                                              rows="2" placeholder="Ghi chú thêm cho đơn hàng..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Phương thức thanh toán -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Phương thức thanh toán</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="cod" value="cod" checked>
                                        <label class="form-check-label" for="cod">
                                            <i class="fas fa-money-bill-wave me-2"></i>
                                            Thanh toán khi nhận hàng (COD)
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="bank_transfer" value="bank_transfer">
                                        <label class="form-check-label" for="bank_transfer">
                                            <i class="fas fa-university me-2"></i>
                                            Chuyển khoản ngân hàng
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="momo" value="momo">
                                        <label class="form-check-label" for="momo">
                                            <i class="fas fa-mobile-alt me-2"></i>
                                            Ví điện tử MoMo
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tóm tắt đơn hàng -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Tóm tắt đơn hàng</h5>
                            </div>
                            <div class="card-body">
                                <!-- Danh sách sản phẩm -->
                                <div class="checkout-items mb-3">
                                    <?php foreach ($cartItems as $item): ?>
                                        <div class="checkout-item d-flex align-items-center mb-2">
                                            <img src="<?= BASE_URL . 'assets/uploads/' . $item['image'] ?>" 
                                                 alt="<?= htmlspecialchars($item['name']) ?>" 
                                                 class="checkout-item-image me-2"
                                                 onerror="this.src='<?= BASE_URL ?>assets/images/placeholder.jpg'">
                                            <div class="flex-grow-1">
                                                <div class="checkout-item-name">
                                                    <?= htmlspecialchars(mb_substr($item['name'], 0, 30)) ?>
                                                    <?= mb_strlen($item['name']) > 30 ? '...' : '' ?>
                                                </div>
                                                <div class="checkout-item-meta">
                                                    <small class="text-muted">
                                                        Số lượng: <?= $item['quantity'] ?> × <?= number_format($item['price'], 0, ',', '.') ?>₫
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="checkout-item-total">
                                                <strong><?= number_format($item['total'], 0, ',', '.') ?>₫</strong>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <hr>
                                
                                <!-- Tổng tiền -->
                                <div class="checkout-summary">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tạm tính:</span>
                                        <span><?= number_format($totalAmount, 0, ',', '.') ?>₫</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Phí vận chuyển:</span>
                                        <span>Miễn phí</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Tổng cộng:</strong>
                                        <strong class="text-primary fs-5"><?= number_format($totalAmount, 0, ',', '.') ?>₫</strong>
                                    </div>
                                </div>
                                
                                <!-- Nút thanh toán -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Đặt hàng
                                    </button>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Bằng việc đặt hàng, bạn đồng ý với 
                                        <a href="#" class="text-decoration-none">điều khoản sử dụng</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.checkout-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.checkout-item:last-child {
    border-bottom: none;
}

.checkout-item-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 0.25rem;
}

.checkout-item-name {
    font-size: 0.9rem;
    font-weight: 500;
    line-height: 1.3;
}

.checkout-item-meta {
    font-size: 0.8rem;
}

.checkout-item-total {
    font-size: 0.9rem;
    text-align: right;
}

.checkout-summary {
    font-size: 0.9rem;
}

.form-group label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.form-check-label {
    cursor: pointer;
    font-weight: 500;
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
}

@media (max-width: 768px) {
    .checkout-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .checkout-item-image {
        width: 40px;
        height: 40px;
        margin-bottom: 0.5rem;
    }
    
    .checkout-item-total {
        text-align: left;
        margin-top: 0.5rem;
    }
}
</style>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>
