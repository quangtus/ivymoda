<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<section class="cart-section">
    <div class="cart-container">
        <div class="cart-top-wrap">
            <div class="cart-top">
                <div class="cart-top-item chosen">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="cart-top-item">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="cart-top-item">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="cart-container">
        <div class="cart-content row">
            <div class="cart-content-left">
                <h2>Giỏ hàng của bạn <?= count($cartItems) ?> Sản Phẩm</h2>
                
                <!-- Thông báo -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
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
                    <table>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Chiết khấu</th>
                            <th>Số lượng</th>
                            <th>Tổng tiền</th>
                        </tr>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <img src="<?= BASE_URL . 'assets/uploads/' . $item->sanpham_anh ?>" 
                                             alt="<?= htmlspecialchars($item->sanpham_tieude) ?>" 
                                             onerror="this.src='<?= BASE_URL ?>assets/images/no-image.jpg'">
                                        <div class="product-details">
                                            <p class="product-name"><?= htmlspecialchars($item->sanpham_tieude) ?></p>
                                            <p class="product-variant">Màu sắc: <?= htmlspecialchars($item->color_ten) ?></p>
                                            <p class="product-variant">Size: <?= htmlspecialchars($item->size_ten) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                    $discountPercent = 0;
                                    if (isset($item->sanpham_gia_goc) && $item->sanpham_gia_goc > 0 && $item->sanpham_gia_goc > $item->gia_hien_tai) {
                                        $discountPercent = round((($item->sanpham_gia_goc - $item->gia_hien_tai) / $item->sanpham_gia_goc) * 100);
                                    }
                                    ?>
                                    <?php if ($discountPercent > 0): ?>
                                        <span class="discount-badge">-<?= $discountPercent ?>%</span>
                                    <?php else: ?>
                                        <span class="text-muted">Không giảm giá</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="quantity-controls">
                                        <form method="POST" action="<?= BASE_URL ?>cart/update" class="quantity-form">
                                            <input type="hidden" name="cart_id" value="<?= $item->cart_id ?>">
                                            <button type="button" class="quantity-btn decrease" data-cart-id="<?= $item->cart_id ?>">-</button>
                                            <input type="number" 
                                                   name="quantity" 
                                                   value="<?= $item->quantity ?>" 
                                                   min="1" 
                                                   max="<?= $item->ton_kho ?>"
                                                   class="quantity-input"
                                                   data-cart-id="<?= $item->cart_id ?>"
                                                   data-product-id="<?= $item->cart_id ?>">
                                            <button type="button" class="quantity-btn increase" data-cart-id="<?= $item->cart_id ?>">+</button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <p class="item-total"><?= number_format($item->gia_hien_tai * $item->quantity, 0, ',', '.') ?> ₫</p>
                                </td>
                                <td>
                                    <form method="POST" action="<?= BASE_URL ?>cart/remove" class="remove-form">
                                        <input type="hidden" name="cart_id" value="<?= $item->cart_id ?>">
                                        <button type="button" class="remove-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    
                    <div class="continue-shopping">
                        <a href="<?= BASE_URL ?>" class="btn-continue">
                            ← Tiếp tục mua hàng
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="cart-content-right">
                <table>
                    <tr>
                        <th colspan="2">TỔNG TIỀN GIỎ HÀNG</th>
                    </tr>
                    <tr>
                        <td>TỔNG SẢN PHẨM</td>
                        <td><?= count($cartItems) ?></td>
                    </tr>
                    <tr>
                        <td>TỔNG TIỀN HÀNG</td>
                        <td><p><?= number_format($totalAmount, 0, ',', '.') ?> ₫</p></td>
                    </tr>
                    <tr>
                        <td>THÀNH TIỀN</td>
                        <td><p><?= number_format($totalAmount, 0, ',', '.') ?> ₫</p></td>
                    </tr>
                    <tr>
                        <td>TẠM TÍNH</td>
                        <td><p style="color: black; font-weight: bold;"><?= number_format($totalAmount, 0, ',', '.') ?> ₫</p></td>
                    </tr>
                </table>
                <!-- Mã giảm giá -->
                <div class="discount-section">
                    <h5>Mã giảm giá</h5>
                    <div class="discount-form">
                        <input type="text" id="discountCode" placeholder="Nhập mã giảm giá" class="discount-input">
                        <button type="button" id="applyDiscount" class="btn-apply-discount">ÁP DỤNG</button>
                    </div>
                    <div id="discountResult" class="discount-result"></div>
                </div>
                
                <div class="cart-content-right-text">
                    <p>Phí vận chuyển và thuế sẽ được tính khi thanh toán</p>
                    <p style="color: red;">Mua thêm <span style="font-weight: bold; font-size: 18px;">110.000<sup>₫</sup></span> để được miễn phí SHIP</p>
                </div>
                <div class="cart-content-right-button">
                    <a href="<?= BASE_URL ?>" class="btn-continue-shopping">TIẾP TỤC MUA SẮM</a>
                    <a href="<?= BASE_URL ?>checkout" class="btn-checkout" id="btnCheckout">THANH TOÁN</a>
                </div>
                <div class="cart-content-right-login">
                    <p>TÀI KHOẢN IVY</p>
                    <p>Hãy <a href="<?= BASE_URL ?>auth/login">Đăng nhập</a> để nhận nhiều ưu đãi hấp dẫn</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Checkout button
    const checkoutBtn = document.getElementById('btnCheckout');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function(){
            window.location.href = '<?= BASE_URL ?>checkout';
        });
    }
    
    // Debug: Log cart items
    console.log('Cart page loaded, items count:', document.querySelectorAll('.quantity-input').length);
    
    // Debug: Log cart IDs
    document.querySelectorAll('input[name="cart_id"]').forEach((input, index) => {
        console.log(`Cart ID ${index}:`, input.value);
    });
    
    // Force visibility for quantity inputs
    document.querySelectorAll('.quantity-input').forEach((input, index) => {
        console.log(`Quantity input ${index}:`, {
            display: window.getComputedStyle(input).display,
            visibility: window.getComputedStyle(input).visibility,
            opacity: window.getComputedStyle(input).opacity,
            width: window.getComputedStyle(input).width,
            height: window.getComputedStyle(input).height
        });
        
        // Force styles
        input.style.display = 'block';
        input.style.visibility = 'visible';
        input.style.opacity = '1';
        input.style.background = 'white';
        input.style.color = '#333';
        input.style.border = '1px solid #ddd';
        input.style.padding = '5px';
        input.style.textAlign = 'center';
        input.style.width = '60px';
        input.style.height = '30px';
        input.style.lineHeight = '1.2';
        input.style.fontSize = '14px';
        input.style.fontWeight = '500';
        input.style.minHeight = '30px';
        
        // Add debug class
        input.classList.add('debug-quantity');
    });
    
    // Check for CSS conflicts
    const styleSheets = document.styleSheets;
    console.log('Loaded stylesheets:', styleSheets.length);
    for (let i = 0; i < styleSheets.length; i++) {
        try {
            const rules = styleSheets[i].cssRules || styleSheets[i].rules;
            console.log(`Stylesheet ${i}:`, styleSheets[i].href, 'Rules:', rules.length);
        } catch (e) {
            console.log(`Stylesheet ${i}:`, styleSheets[i].href, 'Cannot access rules');
        }
    }
    
    // Xử lý quantity controls
    // Xử lý nút tăng/giảm số lượng
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const cartId = this.dataset.cartId;
            if (!cartId) {
                console.error('Missing cart_id in button');
                return;
            }
            
            const input = document.querySelector(`input[data-cart-id="${cartId}"]`);
            if (!input) {
                console.error('Input not found for cart_id:', cartId);
                return;
            }
            
            let quantity = parseInt(input.value) || 1;
            
            if (this.classList.contains('increase')) {
                quantity = Math.min(quantity + 1, parseInt(input.max) || 99);
            } else if (this.classList.contains('decrease')) {
                quantity = Math.max(quantity - 1, 1);
            }
            
            input.value = quantity;
            
            // CHỈ sử dụng AJAX, KHÔNG dùng form submit
            if (window.cartManager) {
                window.cartManager.updateCartItem(cartId, quantity);
            } else {
                console.error('CartManager not available');
            }
        });
    });
    
    // Xử lý thay đổi số lượng trực tiếp
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.dataset.cartId;
            let quantity = parseInt(this.value) || 1;
            
            if (quantity < 1) {
                quantity = 1;
                this.value = 1;
            } else if (quantity > (parseInt(this.max) || 99)) {
                quantity = parseInt(this.max) || 99;
                this.value = quantity;
            }
            
            // CHỈ sử dụng AJAX, KHÔNG dùng form submit
            if (window.cartManager && cartId) {
                window.cartManager.updateCartItem(cartId, quantity);
            } else {
                console.error('CartManager not available for cartId:', cartId);
            }
        });
    });
    
    // Xử lý nút xóa sản phẩm
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            if (!form) {
                console.error('Remove form not found');
                return;
            }
            
            const cartId = form.querySelector('input[name="cart_id"]').value;
            if (!cartId) {
                console.error('Cart ID not found');
                return;
            }
            
            // Debug logging
            console.log('Remove button clicked, cart_id:', cartId);
            
            // Sử dụng AJAX thay vì form submit
            if (window.cartManager) {
                window.cartManager.removeFromCart(cartId);
            } else {
                // Fallback: Submit form nếu cartManager không có
                if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    console.log('Submitting remove form for cart_id:', cartId);
                    form.submit();
                }
            }
        });
    });
    
    // Xử lý mã giảm giá
    const discountCodeInput = document.getElementById('discountCode');
    const applyDiscountBtn = document.getElementById('applyDiscount');
    const discountResult = document.getElementById('discountResult');
    
    applyDiscountBtn.addEventListener('click', function() {
        const code = discountCodeInput.value.trim();
        
        if (!code) {
            showDiscountResult('Vui lòng nhập mã giảm giá', 'error');
            return;
        }
        
        // Disable button để tránh double-click
        this.disabled = true;
        this.textContent = 'Đang xử lý...';
        
        fetch('<?= BASE_URL ?>discount/validate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `code=${encodeURIComponent(code)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showDiscountResult(`✅ ${data.message}`, 'success');
                updateCartTotal(data.discount.final_total);
                discountCodeInput.value = '';
            } else {
                showDiscountResult(`❌ ${data.message}`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showDiscountResult('❌ Lỗi kết nối. Vui lòng thử lại.', 'error');
        })
        .finally(() => {
            this.disabled = false;
            this.textContent = 'ÁP DỤNG';
        });
    });
    
    // Xử lý Enter key trong input mã giảm giá
    discountCodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyDiscountBtn.click();
        }
    });
    
    function showDiscountResult(message, type) {
        discountResult.innerHTML = `<div class="alert alert-${type === 'success' ? 'success' : 'danger'}">${message}</div>`;
        
        // Auto hide sau 5 giây
        setTimeout(() => {
            discountResult.innerHTML = '';
        }, 5000);
    }
    
    function updateCartTotal(newTotal) {
        // Cập nhật tổng tiền hiển thị
        const totalElements = document.querySelectorAll('.cart-total');
        totalElements.forEach(el => {
            el.textContent = new Intl.NumberFormat('vi-VN').format(newTotal) + ' ₫';
        });
    }
});
</script>

<!-- CSS đã được tách ra file cart-page.css -->

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/cart-index.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/cart-page.css">

<!-- Load Cart JavaScript -->
<script src="<?= BASE_URL ?>assets/js/cart.js"></script>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>