<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<section class="delivery-section">
    <div class="delivery-container">
        <div class="delivery-top-wrap">
            <div class="delivery-top">
                <div class="delivery-top-item active">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="delivery-top-item active">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="delivery-top-item">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="delivery-container">
        <div class="delivery-content row">
            <div class="delivery-content-left">
                <h2>Thông tin giao hàng</h2>
                
                <!-- Thông báo -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <form method="POST" action="<?= BASE_URL ?>checkout/processDelivery" id="deliveryForm">
                    <div class="form-group">
                        <label for="customer_name">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" 
                               id="customer_name" 
                               name="customer_name" 
                               value="<?= htmlspecialchars($user->fullname ?? '') ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_phone">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="tel" 
                               id="customer_phone" 
                               name="customer_phone" 
                               value="<?= htmlspecialchars($user->phone ?? '') ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_email">Email</label>
                        <input type="email" 
                               id="customer_email" 
                               name="customer_email" 
                               value="<?= htmlspecialchars($user->email ?? '') ?>" 
                               readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_address">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                        <textarea id="customer_address" 
                                  name="customer_address" 
                                  rows="3" 
                                  placeholder="Nhập địa chỉ đầy đủ (số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố)"
                                  required><?= htmlspecialchars($user->address ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping_method">Phương thức giao hàng</label>
                        <select id="shipping_method" name="shipping_method">
                            <option value="Standard">Giao hàng tiêu chuẩn (2-3 ngày)</option>
                            <option value="Express">Giao hàng nhanh (1 ngày)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Ghi chú đơn hàng</label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="2" 
                                  placeholder="Ghi chú thêm cho đơn hàng (không bắt buộc)"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Phương thức thanh toán <span class="text-danger">*</span></label>
                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="COD" checked>
                                <span class="payment-label">
                                    <i class="fas fa-money-bill-wave"></i>
                                    Thanh toán khi nhận hàng (COD)
                                </span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="momo">
                                <span class="payment-label">
                                    <i class="fas fa-mobile-alt"></i>
                                    Thanh toán qua MoMo
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="<?= BASE_URL ?>cart" class="btn btn-secondary">← Quay lại giỏ hàng</a>
                        <button type="submit" class="btn btn-primary">Tiếp tục thanh toán →</button>
                    </div>
                </form>
            </div>
            
            <div class="delivery-content-right">
                <h3>Đơn hàng của bạn</h3>
                <div class="order-summary">
                    <table>
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <img src="<?= BASE_URL ?>assets/images/products/<?= htmlspecialchars($item->sanpham_anh) ?>" 
                                             alt="<?= htmlspecialchars($item->sanpham_tieude) ?>" 
                                             class="product-image">
                                        <div class="product-details">
                                            <h4><?= htmlspecialchars($item->sanpham_tieude) ?></h4>
                                            <p>Màu: <?= htmlspecialchars($item->color_ten) ?> | Size: <?= htmlspecialchars($item->size_ten) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $item->quantity ?></td>
                                <td><?= number_format($item->gia_hien_tai * $item->quantity, 0, ',', '.') ?> ₫</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="2"><strong>Tổng cộng:</strong></td>
                                <td><strong><?= number_format($totalAmount, 0, ',', '.') ?> ₫</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/delivery.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('deliveryForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin bắt buộc');
            }
        });
    }
});
</script>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>
