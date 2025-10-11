/**
 * Image Handler JavaScript
 * Xử lý hiển thị ảnh sản phẩm và xử lý lỗi
 */

document.addEventListener('DOMContentLoaded', function() {
    // Xử lý tất cả ảnh sản phẩm
    const productImages = document.querySelectorAll('.product-image, .card-img-top');

    productImages.forEach(function(img) {
        // Chỉ thêm loading nếu ảnh chưa load
        if (!img.complete) {
            img.classList.add('image-loading');
        }

        // Xử lý khi ảnh load thành công
        img.addEventListener('load', function() {
            this.classList.remove('image-loading');
            this.classList.add('loaded');
        });

        // Xử lý khi ảnh lỗi
        img.addEventListener('error', function() {
            this.classList.remove('image-loading');
            this.classList.add('error');

            // Thay thế bằng ảnh placeholder
            const onerrorAttr = this.getAttribute('onerror');
            const onerrorMatch = onerrorAttr ? onerrorAttr.match(/'([^']+)'/) : null;
            const placeholder = this.getAttribute('data-placeholder') ||
                (onerrorMatch ? onerrorMatch[1] : null) ||
                '/ivymoda/ivymoda_mvc/public/assets/images/no-image.svg';

            if (placeholder && placeholder !== this.src) {
                this.src = placeholder;
            }
        });
    });

    // Lazy loading cho ảnh
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });

        // Quan sát tất cả ảnh có data-src
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// Switch gallery by color (frontend product detail)
function switchColorGallery(colorId) {
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    const mainImage = document.getElementById('mainProductImage');
    if (!thumbnails || !mainImage) return;

    let firstMatch = null;
    thumbnails.forEach(function(item) {
        const itemColorId = item.getAttribute('data-color-id');
        const match = !colorId || (itemColorId && parseInt(itemColorId, 10) === parseInt(colorId, 10));
        item.style.display = match ? '' : 'none';
        if (match && !firstMatch) firstMatch = item;
    });

    if (firstMatch) {
        const img = firstMatch.querySelector('img');
        if (img) {
            mainImage.src = img.src;
            document.querySelectorAll('.thumbnail-item').forEach(el => el.classList.remove('active'));
            firstMatch.classList.add('active');
        }
    }
}

/**
 * Thêm ảnh vào giỏ hàng với animation
 */
function addToCartWithAnimation(productId, button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;

    // Simulate API call
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> Đã thêm';
        button.classList.remove('btn-outline-primary');
        button.classList.add('btn-success');

        // Reset sau 2 giây
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
            button.disabled = false;
        }, 2000);
    }, 1000);
}

/**
 * Thêm vào yêu thích với animation
 */
function addToWishlistWithAnimation(productId, button) {
    const heartIcon = button.querySelector('i');

    if (heartIcon.classList.contains('fas')) {
        // Đã yêu thích, bỏ yêu thích
        heartIcon.classList.remove('fas');
        heartIcon.classList.add('far');
        button.classList.remove('btn-danger');
        button.classList.add('btn-outline-danger');
    } else {
        // Chưa yêu thích, thêm yêu thích
        heartIcon.classList.remove('far');
        heartIcon.classList.add('fas');
        button.classList.remove('btn-outline-danger');
        button.classList.add('btn-danger');

        // Animation
        button.style.transform = 'scale(1.2)';
        setTimeout(() => {
            button.style.transform = 'scale(1)';
        }, 200);
    }
}

/**
 * Xem nhanh sản phẩm
 */
function quickView(productId) {
    // Tạo modal xem nhanh
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xem nhanh sản phẩm</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Đang tải thông tin sản phẩm...</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Show modal
    $(modal).modal('show');

    // Load product data via AJAX
    fetch(`/ivymoda/ivymoda_mvc/public/ajax/quick_view.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.querySelector('.modal-body').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <img src="${data.product.image}" class="img-fluid" alt="${data.product.name}">
                        </div>
                        <div class="col-md-6">
                            <h4>${data.product.name}</h4>
                            <p class="text-muted">${data.product.description}</p>
                            <h5 class="text-primary">${data.product.price}</h5>
                            <button class="btn btn-primary" onclick="addToCartWithAnimation(${productId}, this)">
                                <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                            </button>
                        </div>
                    </div>
                `;
            } else {
                modal.querySelector('.modal-body').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        Không thể tải thông tin sản phẩm.
                    </div>
                `;
            }
        })
        .catch(error => {
            modal.querySelector('.modal-body').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Có lỗi xảy ra khi tải dữ liệu.
                </div>
            `;
        });

    // Remove modal when hidden
    $(modal).on('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

/**
 * Preload ảnh để tránh nhấp nháy
 */
function preloadImage(src) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = reject;
        img.src = src;
    });
}

/**
 * Tạo placeholder ảnh
 */
function createImagePlaceholder(width = 200, height = 200, text = 'No Image') {
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');

    // Background
    ctx.fillStyle = '#f8f9fa';
    ctx.fillRect(0, 0, width, height);

    // Border
    ctx.strokeStyle = '#dee2e6';
    ctx.lineWidth = 2;
    ctx.setLineDash([5, 5]);
    ctx.strokeRect(5, 5, width - 10, height - 10);

    // Text
    ctx.fillStyle = '#6c757d';
    ctx.font = '14px Arial';
    ctx.textAlign = 'center';
    ctx.fillText(text, width / 2, height / 2);

    return canvas.toDataURL();
}