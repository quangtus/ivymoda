<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\frontend\product\category.php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php';
?>

<section class = "product-index">
    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>home">Trang chủ</a></li>
                <span class="">&#8594;</span>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>product">Sản phẩm</a></li>
                <span class="">&#8594;</span>
                <li class="breadcrumb-item" aria-current="page"><?= isset($category) ? htmlspecialchars($category->danhmuc_ten) : 'Bộ lọc sản phẩm' ?></li>
            </ol>
        </nav>

    <div class="produc-index-container">
        <div class = "row">
            <!-- Filter Sidebar - Hidden by default on mobile -->
            <div class = "product-index-left filter-sidebar" style="display:none">
                <div class="filter-section">
                    <h4>Bộ lọc</h4>
                    
                    <!-- Category Filter -->
                    <div class="filter-group">
                        <h5>Danh mục</h5>
                        <ul class="filter-list">
                            <li><a href="<?= BASE_URL ?>product" class="<?= !isset($category) ? 'active' : '' ?>">Tất cả sản phẩm</a></li>
                            <?php if(isset($categories) && count($categories) > 0): ?>
                                <?php foreach($categories as $cat): ?>
                                    <li><a href="<?= BASE_URL ?>product/category/<?= $cat->danhmuc_id ?>" 
                                    class="<?= (isset($category) && $cat->danhmuc_id == $category->danhmuc_id) ? 'active' : '' ?>">
                                        <?= htmlspecialchars($cat->danhmuc_ten) ?>
                                    </a></li> 
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Product Type Filter -->
                    <div class="filter-group">
                        <h5>Loại sản phẩm</h5>
                        <select name="type" id="filter-type" class="filter-select">
                            <option value="">Tất cả loại</option>
                            <?php if(isset($subcategories) && is_array($subcategories) && count($subcategories) > 0): ?>
                                <?php foreach($subcategories as $type): ?>
                                    <option value="<?= $type->loaisanpham_id ?>" <?= (isset($filters['subcategory_id']) && $filters['subcategory_id'] == $type->loaisanpham_id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type->loaisanpham_ten) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="filter-group">
                        <h5>Khoảng giá</h5>
                        <select name="price" id="side-filter-price" class="filter-select">
                            <option value="">Tất cả giá</option>
                            <option value="lt500" <?= (isset($filters['price_range']) && $filters['price_range'] == 'lt500') ? 'selected' : '' ?>>Dưới 500k</option>
                            <option value="500-1000" <?= (isset($filters['price_range']) && $filters['price_range'] == '500-1000') ? 'selected' : '' ?>>500k - 1tr</option>
                            <option value="gt1000" <?= (isset($filters['price_range']) && $filters['price_range'] == 'gt1000') ? 'selected' : '' ?>>Trên 1tr</option>
                        </select>
                    </div>

                    <!-- Size Filter -->
                    <div class="filter-group">
                        <h5>Size</h5>
                        <select name="size" id="side-filter-size" class="filter-select">
                            <option value="">Tất cả size</option>
                            <?php if(isset($sizes) && count($sizes) > 0): ?>
                                <?php foreach($sizes as $size): ?>
                                    <option value="<?= $size->size_id ?>" <?= (isset($filters['size_id']) && $filters['size_id'] == $size->size_id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($size->size_ten) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Filter Actions -->
                    <div class="filter-actions">
                        <button type="button" id="apply-filters" class="btn btn-primary">Áp dụng bộ lọc</button>
                        <button type="button" id="clear-filters" class="btn btn-secondary">Xóa bộ lọc</button>
                    </div>
                </div>
            </div>
            <div class = "product-index-right row">
                <div class = "product-index-right-top-item">
                    <h1 class="h2"><?= isset($category) ? htmlspecialchars($category->danhmuc_ten) : 'Bộ lọc sản phẩm' ?></h1>
                    <p class="text-muted">Tìm thấy <?= $totalProducts ?> sản phẩm</p>
                </div>
                <div class="product-index-right-top-item">
                    <select id="filter-price" class="sort-select">
                        <option value="">Bộ lọc</option>
                        <option value="lt500" <?= (isset($filters['price_range']) && $filters['price_range'] == 'lt500') ? 'selected' : '' ?>>Giá dưới 500k</option>
                        <option value="500-1000" <?= (isset($filters['price_range']) && $filters['price_range'] == '500-1000') ? 'selected' : '' ?>>Giá 500k - 1tr</option>
                        <option value="gt1000" <?= (isset($filters['price_range']) && $filters['price_range'] == 'gt1000') ? 'selected' : '' ?>>Giá trên 1tr</option>
                    </select>
                </div>
                <div class="product-index-right-top-item">
                    <select id="filter-size" class="sort-select">
                        <option value="">Size</option>
                        <?php if(isset($sizes) && count($sizes) > 0): ?>
                            <?php foreach($sizes as $size): ?>
                                <option value="<?= $size->size_id ?>" <?= (isset($filters['size_id']) && $filters['size_id'] == $size->size_id) ? 'selected' : '' ?>><?= htmlspecialchars($size->size_ten) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="product-index-right-top-item">
                    <select name="sort" id="sort-products" class="sort-select">
                        <option value="">Sắp xếp theo</option>
                        <option value="price_asc" <?= (isset($filters['sort']) && $filters['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá thấp đến cao</option>
                        <option value="price_desc" <?= (isset($filters['sort']) && $filters['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá cao đến thấp</option>
                        <option value="name_asc" <?= (isset($filters['sort']) && $filters['sort'] == 'name_asc') ? 'selected' : '' ?>>Tên A-Z</option>
                        <option value="name_desc" <?= (isset($filters['sort']) && $filters['sort'] == 'name_desc') ? 'selected' : '' ?>>Tên Z-A</option>
                    </select>
                </div>
                <div class="product-index-right-top-item">
                    <button type="button" id="apply-filters-top" class="btn btn-primary">Áp dụng</button>
                </div>
                <div class="product-index-right-top-item">
                    <form method="GET" action="<?= BASE_URL ?>product/search" class="d-flex">
                        <div class="search-input-wrapper" style="width:100%;">
                            <input type="text" name="q" class="form-control" placeholder="Tìm kiếm" value="<?= htmlspecialchars($keyword ?? '') ?>">
                            <?php if(isset($category) && isset($category->danhmuc_id)): ?>
                            <input type="hidden" name="category" value="<?= (int)$category->danhmuc_id ?>">
                            <?php endif; ?>
                            <button type="submit" class="search-btn" aria-label="Tìm kiếm">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class = "product-index-right-content row">
                    <?php if(isset($products) && count($products) > 0): ?>
                        <?php foreach($products as $product): ?>
                            <div class="product-index-right-content-item">
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
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Không có sản phẩm nào</h4>
                            <p class="text-muted"><?= isset($category) ? 'Danh mục "' . htmlspecialchars($category->danhmuc_ten) . '" chưa có sản phẩm nào.' : 'Không tìm thấy sản phẩm phù hợp với bộ lọc.' ?></p>
                            <a href="<?= BASE_URL ?>product" class="btn btn-primary">Xem tất cả sản phẩm</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-index-right-bottom">
                    <!-- Pagination -->
                    <?php if($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center row">
                                <?php 
                                $paginationBaseUrl = isset($category) ? 
                                    BASE_URL . 'product/category/' . $category->danhmuc_id : 
                                    BASE_URL . 'product/filter';
                                $currentParams = $_GET;
                                ?>
                                <?php if($currentPage > 1): ?>
                                    <?php $prevParams = array_merge($currentParams, ['page' => $currentPage - 1]); ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $paginationBaseUrl ?>?<?= http_build_query($prevParams) ?>">Trước</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                    <?php $pageParams = array_merge($currentParams, ['page' => $i]); ?>
                                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= $paginationBaseUrl ?>?<?= http_build_query($pageParams) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if($currentPage < $totalPages): ?>
                                    <?php $nextParams = array_merge($currentParams, ['page' => $currentPage + 1]); ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $paginationBaseUrl ?>?<?= http_build_query($nextParams) ?>">Sau</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

        <!-- Filter sidebar -->
        <div class="row">
            <!-- Product grid -->
            <div class="col-md-9">
                
            </div>
        </div>
    </div>
</section>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/product-index.css">

<script>
// JavaScript cho trang category với hệ thống filter
document.addEventListener('DOMContentLoaded', function() {
    // Toggle filter sidebar
    const toggleFiltersBtn = document.getElementById('toggle-filters');
    const filterSidebar = document.querySelector('.filter-sidebar');
    
    if (toggleFiltersBtn && filterSidebar) {
        toggleFiltersBtn.addEventListener('click', function() {
            filterSidebar.classList.toggle('active');
        });
    }
    
    // Apply filters (sidebar + top bar)
    const applyFiltersBtn = document.getElementById('apply-filters');
    const applyFiltersTopBtn = document.getElementById('apply-filters-top');
    [applyFiltersBtn, applyFiltersTopBtn].forEach(function(btn){
        if (btn) btn.addEventListener('click', function(){ applyFilters(); });
    });
    
    // Clear filters
    const clearFiltersBtn = document.getElementById('clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            clearFilters();
        });
    }
    
    // Sort products does NOT auto-apply; use the Apply button
    
    // Layout adjustment for products
    const productContainer = document.querySelector('.product-index-right-content');
    const productItems = document.querySelectorAll('.product-index-right-content-item');
    
    if (productContainer && productItems.length > 0) {
        // Reset layout trước
        productContainer.style.justifyContent = 'flex-start';
        productContainer.style.alignItems = 'flex-start';
        productContainer.style.width = '100%';
        productContainer.style.margin = '0';
        productContainer.style.padding = '0';
        
        // Đếm số sản phẩm
        const productCount = productItems.length;
        
        // Điều chỉnh layout dựa trên số lượng sản phẩm
        if (productCount === 1) {
            // 1 sản phẩm: căn giữa
            productContainer.style.justifyContent = 'center';
            productContainer.style.maxWidth = '400px';
            productContainer.style.margin = '0 auto';
            productContainer.style.width = '100%';
        } else if (productCount === 2) {
            // 2 sản phẩm: căn giữa với khoảng cách hợp lý
            productContainer.style.justifyContent = 'center';
            productContainer.style.gap = '2rem';
            productContainer.style.width = '100%';
            productContainer.style.margin = '0';
        } else if (productCount === 3) {
            // 3 sản phẩm: căn trái với khoảng cách đều
            productContainer.style.justifyContent = 'flex-start';
            productContainer.style.gap = '1.5rem';
            productContainer.style.width = '100%';
        } else {
            // 4+ sản phẩm: layout bình thường
            productContainer.style.justifyContent = 'flex-start';
            productContainer.style.gap = '1.5rem';
            productContainer.style.width = '100%';
        }
        
        console.log(`✅ Đã điều chỉnh layout cho ${productCount} sản phẩm - không bị lệch phải`);
    }
    
    function applyFilters() {
        const params = new URLSearchParams();
        const categoryId = '<?= isset($category) ? (int)$category->danhmuc_id : 0 ?>';
        const typeEl = document.getElementById('filter-type');
        const priceEl = document.getElementById('filter-price') || document.getElementById('side-filter-price');
        const sizeEl = document.getElementById('filter-size') || document.getElementById('side-filter-size');
        const sortEl = document.getElementById('sort-products');
        const type = typeEl ? typeEl.value : '';
        const price = priceEl ? priceEl.value : '';
        const size = sizeEl ? sizeEl.value : '';
        const sort = sortEl ? sortEl.value : '';
        if (type) params.set('type', type);
        if (price) params.set('price', price);
        if (size) params.set('size', size);
        if (sort) params.set('sort', sort);
        const base = '<?= BASE_URL ?>product/category/<?= isset($category) ? (int)$category->danhmuc_id : 0 ?>';
        const qs = params.toString();
        window.location.href = qs ? `${base}?${qs}` : base;
    }
    
    function clearFilters() {
        const currentUrl = new URL(window.location);
        const pathParts = currentUrl.pathname.split('/');
        const categoryId = pathParts[pathParts.length - 1];
        
        if (categoryId && !isNaN(categoryId)) {
            window.location.href = '<?= BASE_URL ?>product/category/' + categoryId;
        } else {
            window.location.href = '<?= BASE_URL ?>product';
        }
    }
    
    // Remove auto-apply on change; use Apply button instead
});

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
</script>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>