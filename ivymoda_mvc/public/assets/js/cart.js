/**
 * Cart JavaScript Handler
 * Xử lý các chức năng giỏ hàng với AJAX
 */

class CartManager {
    constructor() {
        this.baseUrl = window.location.origin + '/ivymoda/ivymoda_mvc/public/';
        this.ajaxUrl = this.baseUrl + 'ajax/cart_ajax.php';
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.updateCartCount();
    }
    
    bindEvents() {
        // Nút thêm vào giỏ hàng
        document.addEventListener('click', (e) => {
            if (e.target.closest('.add-to-cart-btn')) {
                e.preventDefault();
                this.addToCart(e.target.closest('.add-to-cart-btn'));
            }
            
            // Nút cập nhật số lượng
            if (e.target.closest('.quantity-btn')) {
                e.preventDefault();
                this.updateQuantity(e.target.closest('.quantity-btn'));
            }
            
            // Input số lượng
            if (e.target.classList.contains('quantity-input')) {
                this.handleQuantityInput(e.target);
            }
        });
    }
    
    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    async addToCart(button) {
        const productId = button.dataset.productId;
        const quantity = button.dataset.quantity || 1;
        
        if (!productId) {
            this.showMessage('Lỗi: Không tìm thấy ID sản phẩm', 'error');
            return;
        }
        
        // Disable button để tránh click nhiều lần
        button.disabled = true;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
        
        try {
            const response = await this.makeRequest('POST', {
                action: 'add',
                product_id: productId,
                quantity: quantity
            });
            
            if (response.success) {
                this.showMessage(response.message, 'success');
                this.updateCartCount();
                this.updateCartUI();
            } else {
                this.showMessage(response.message, 'error');
            }
        } catch (error) {
            this.showMessage('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng', 'error');
            console.error('Add to cart error:', error);
        } finally {
            // Restore button
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
    
    /**
     * Cập nhật số lượng sản phẩm
     */
    async updateQuantity(button) {
        const cartId = button.dataset.cartId;
        const input = document.querySelector(`input[data-cart-id="${cartId}"]`);
        
        if (!input) return;
        
        let quantity = parseInt(input.value) || 1;
        
        if (button.classList.contains('increase')) {
            quantity = Math.min(quantity + 1, parseInt(input.max) || 99);
        } else if (button.classList.contains('decrease')) {
            quantity = Math.max(quantity - 1, 1);
        }
        
        input.value = quantity;
        
        // Cập nhật qua AJAX
        await this.updateCartItem(cartId, quantity);
    }
    
    /**
     * Xử lý thay đổi input số lượng
     */
    async handleQuantityInput(input) {
        const cartId = input.dataset.cartId;
        let quantity = parseInt(input.value) || 1;
        
        // Validate
        if (quantity < 1) {
            quantity = 1;
            input.value = 1;
        } else if (quantity > (parseInt(input.max) || 99)) {
            quantity = Math.min(parseInt(input.max) || 99, 99);
            input.value = quantity;
        }
        
        // Cập nhật qua AJAX
        await this.updateCartItem(cartId, quantity);
    }
    
    /**
     * Cập nhật sản phẩm trong giỏ hàng
     */
    async updateCartItem(cartId, quantity) {
        try {
            const response = await this.makeRequest('POST', {
                action: 'update',
                cart_id: cartId,
                quantity: quantity
            });
            
            if (response.success) {
                this.updateCartCount();
                this.updateCartUI();
            } else {
                this.showMessage(response.message, 'error');
            }
        } catch (error) {
            this.showMessage('Có lỗi xảy ra khi cập nhật giỏ hàng', 'error');
            console.error('Update cart error:', error);
        }
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    async removeFromCart(cartId) {
        if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            return;
        }
        
        try {
            const response = await this.makeRequest('POST', {
                action: 'remove',
                cart_id: cartId
            });
            
            if (response.success) {
                this.showMessage(response.message, 'success');
                this.updateCartCount();
                this.updateCartUI();
            } else {
                this.showMessage(response.message, 'error');
            }
        } catch (error) {
            this.showMessage('Có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng', 'error');
            console.error('Remove from cart error:', error);
        }
    }
    
    /**
     * Xóa tất cả sản phẩm khỏi giỏ hàng
     */
    async clearCart() {
        if (!confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
            return;
        }
        
        try {
            const response = await this.makeRequest('POST', {
                action: 'clear'
            });
            
            if (response.success) {
                this.showMessage(response.message, 'success');
                this.updateCartCount();
                this.updateCartUI();
            } else {
                this.showMessage(response.message, 'error');
            }
        } catch (error) {
            this.showMessage('Có lỗi xảy ra khi xóa giỏ hàng', 'error');
            console.error('Clear cart error:', error);
        }
    }
    
    /**
     * Lấy danh sách sản phẩm trong giỏ hàng
     */
    async getCartList() {
        try {
            const response = await this.makeRequest('GET', {
                action: 'list'
            });
            
            if (response.success) {
                return response.items;
            }
        } catch (error) {
            console.error('Get cart list error:', error);
        }
        
        return [];
    }
    
    /**
     * Cập nhật số lượng giỏ hàng hiển thị
     */
    async updateCartCount() {
        try {
            const response = await this.makeRequest('GET', {
                action: 'count'
            });
            
            if (response.success) {
                // Cập nhật tất cả các element hiển thị số lượng giỏ hàng
                document.querySelectorAll('.cart-count').forEach(element => {
                    element.textContent = response.count;
                    element.style.display = response.count > 0 ? 'inline' : 'none';
                });
                
                // Cập nhật cart count trong session
                if (window.updateCartCount) {
                    window.updateCartCount(response.count);
                }
            }
        } catch (error) {
            console.error('Update cart count error:', error);
        }
    }
    
    /**
     * Cập nhật giao diện giỏ hàng
     */
    async updateCartUI() {
        // Reload trang giỏ hàng nếu đang ở trang đó
        if (window.location.pathname.includes('/cart')) {
            // Delay một chút để tránh conflict
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
    }
    
    /**
     * Gửi request AJAX
     */
    async makeRequest(method, data = {}) {
        const formData = new FormData();
        
        // Thêm data vào FormData
        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });
        
        const response = await fetch(this.ajaxUrl, {
            method: method,
            body: method === 'POST' ? formData : null
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    }
    
    /**
     * Hiển thị thông báo
     */
    showMessage(message, type = 'info') {
        // Xóa tất cả thông báo cũ trước
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            if (alert.parentNode) {
                alert.remove();
            }
        });
        
        // Tạo element thông báo
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Thêm vào đầu cart content
        const cartContent = document.querySelector('.cart-content-left') || document.body;
        cartContent.insertBefore(alertDiv, cartContent.firstChild);
        
        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 3000);
    }
}

// Khởi tạo CartManager khi DOM đã load
document.addEventListener('DOMContentLoaded', function() {
    window.cartManager = new CartManager();
});

// Export cho sử dụng global
window.CartManager = CartManager;
