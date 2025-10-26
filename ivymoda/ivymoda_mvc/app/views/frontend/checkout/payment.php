<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<section class="payment-section">
    <div class="payment-container">
        <div class="payment-top-wrap">
            <div class="payment-top">
                <div class="payment-top-item active">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="payment-top-item active">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="payment-top-item active">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="payment-container">
        <div class="payment-content row">
            <div class="payment-content-left">
                <h2>Xác nhận thanh toán</h2>
                
                <!-- Thông báo -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <div class="payment-info">
                    <h3>Thông tin đơn hàng</h3>
                    <div class="info-item">
                        <label>Họ tên:</label>
                        <span><?= htmlspecialchars($_SESSION['delivery_info']['customer_name'] ?? $user->fullname ?? '') ?></span>
                    </div>
                    <div class="info-item">
                        <label>Số điện thoại:</label>
                        <span><?= htmlspecialchars($_SESSION['delivery_info']['customer_phone'] ?? $user->phone ?? '') ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><?= htmlspecialchars($user->email ?? '') ?></span>
                    </div>
                    <div class="info-item">
                        <label>Địa chỉ giao hàng:</label>
                        <span><?= htmlspecialchars($_SESSION['delivery_info']['customer_address'] ?? $user->address ?? '') ?></span>
                    </div>
                    <div class="info-item">
                        <label>Phương thức giao hàng:</label>
                        <span><?= htmlspecialchars($_SESSION['delivery_info']['shipping_method'] ?? 'Standard') ?></span>
                    </div>
                </div>
                
                <div class="payment-methods">
                    <h3>Phương thức thanh toán</h3>
                    <div class="payment-method-display">
                        <?php 
                        $paymentMethod = $_SESSION['delivery_info']['payment_method'] ?? 'COD';
                        $paymentMethodText = $paymentMethod === 'momo' ? 'Thanh toán qua MoMo' : 'Thanh toán khi nhận hàng (COD)';
                        $paymentIcon = $paymentMethod === 'momo' ? 'fas fa-mobile-alt' : 'fas fa-money-bill-wave';
                        ?>
                        <div class="payment-method-selected">
                            <i class="<?= $paymentIcon ?>"></i>
                            <span><?= htmlspecialchars($paymentMethodText) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="payment-actions">
                    <a href="<?= BASE_URL ?>checkout/delivery" class="btn btn-secondary">← Quay lại</a>
                    <button type="button" class="btn btn-primary" id="confirmPayment">Xác nhận đặt hàng</button>
                </div>
            </div>
            
            <div class="payment-content-right">
                <h3>Tóm tắt đơn hàng</h3>
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
                            <tr>
                                <td colspan="2"><strong>Tổng tiền hàng:</strong></td>
                                <td><strong><?= number_format($totalAmount, 0, ',', '.') ?> ₫</strong></td>
                            </tr>
                            <?php if (isset($_SESSION['applied_discount'])): ?>
                                <?php $discount = $_SESSION['applied_discount']; ?>
                                <tr style="color:#28a745;">
                                    <td colspan="2"><strong>Giảm giá (<?= htmlspecialchars($discount['code']) ?>):</strong></td>
                                    <td><strong style="color:#28a745;">-<?= number_format($discount['discount_value'], 0, ',', '.') ?> ₫</strong></td>
                                </tr>
                                <tr class="total-row">
                                    <td colspan="2"><strong>Thành tiền:</strong></td>
                                    <td><strong><?= number_format($discount['final_total'], 0, ',', '.') ?> ₫</strong></td>
                                </tr>
                            <?php else: ?>
                                <tr class="total-row">
                                    <td colspan="2"><strong>Thành tiền:</strong></td>
                                    <td><strong><?= number_format($totalAmount, 0, ',', '.') ?> ₫</strong></td>
                                </tr>
                            <?php endif; ?>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/delivery.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/payment.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmPayment');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            const paymentMethod = '<?= htmlspecialchars($_SESSION['delivery_info']['payment_method'] ?? 'COD') ?>';
            const confirmMessage = paymentMethod === 'momo' 
                ? 'Bạn có chắc chắn muốn thanh toán qua MoMo?' 
                : 'Bạn có chắc chắn muốn đặt hàng?';
                
            if (confirm(confirmMessage)) {
                // Submit form to process payment
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>checkout/process';
                
                // Add form data
                const fields = {
                    'customer_name': '<?= htmlspecialchars($_SESSION['delivery_info']['customer_name'] ?? $user->fullname ?? '') ?>',
                    'customer_phone': '<?= htmlspecialchars($_SESSION['delivery_info']['customer_phone'] ?? $user->phone ?? '') ?>',
                    'customer_email': '<?= htmlspecialchars($user->email ?? '') ?>',
                    'customer_address': '<?= htmlspecialchars($_SESSION['delivery_info']['customer_address'] ?? $user->address ?? '') ?>',
                    'payment_method': paymentMethod,
                    'shipping_method': '<?= htmlspecialchars($_SESSION['delivery_info']['shipping_method'] ?? 'Standard') ?>',
                    'notes': '<?= htmlspecialchars($_SESSION['delivery_info']['notes'] ?? '') ?>'
                };
                
                Object.keys(fields).forEach(key => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = fields[key];
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
});
</script>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>
