<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\frontend\product\index.php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php';
?>

<section class = "product-index">
    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>home">Trang chủ</a></li>
                <span class="">&#8594;</span>
                <li class="breadcrumb-item" aria-current="page">Tất cả sản phẩm</li>
                <!-- <li class="breadcrumb-item active" aria-current="page">Tất cả sản phẩm</li> -->
            </ol>
        </nav>

    <div class="produc-index-container">
        <div class = "row">
            <div class = "product-index-left" style="display:none"></div>
            <div class = "product-index-right row">
                <div class = "product-index-right-top-item">
                    <h1 class="h2">Tất cả sản phẩm</h1>
                    <p class="text-muted">Tìm thấy <?= $totalProducts ?> sản phẩm</p>
                </div>
                <div class="product-index-right-top-item">
                    <select id="filter-category" class="sort-select">
                        <option value="">Danh mục</option>
                        <?php if(isset($categories) && count($categories) > 0): ?>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat->danhmuc_id ?>"><?= htmlspecialchars($cat->danhmuc_ten) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="product-index-right-top-item">
                    <select id="filter-type" class="sort-select">
                        <option value="">Loại</option>
                        <?php if(isset($productTypes) && count($productTypes) > 0): ?>
                            <?php foreach($productTypes as $type): ?>
                                <option data-category="<?= $type->danhmuc_id ?>" value="<?= $type->loaisanpham_id ?>"><?= htmlspecialchars($type->loaisanpham_ten) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="product-index-right-top-item">
                    <select id="filter-price" class="sort-select">
                        <option value="">Khoảng giá</option>
                        <option value="lt500">Giá dưới 500k</option>
                        <option value="500-1000">Giá 500k - 1tr</option>
                        <option value="gt1000">Giá trên 1tr</option>
                    </select>
                </div>
                <div class="product-index-right-top-item">
                    <select id="filter-size" class="sort-select">
                        <option value="">Size</option>
                        <?php if(isset($sizes) && count($sizes) > 0): ?>
                            <?php foreach($sizes as $size): ?>
                                <option value="<?= $size->size_id ?>"><?= htmlspecialchars($size->size_ten) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="product-index-right-top-item">
                    <select id="filter-sort" class="sort-select">
                        <option value="">Sắp xếp</option>
                        <option value="price_asc">Giá: thấp đến cao</option>
                        <option value="price_desc">Giá: cao đến thấp</option>
                        <option value="name_asc">Tên: A-Z</option>
                        <option value="name_desc">Tên: Z-A</option>
                    </select>
                </div>
                <div class="product-index-right-top-item">
                    <button id="apply-filters" class="btn btn-primary">Áp dụng</button>
                </div>
                <div class="product-index-right-top-item">
                    <form method="GET" action="<?= BASE_URL ?>product/search" class="d-flex">
                        <div class="search-input-wrapper" style="width:100%;">
                            <input type="text" name="q" class="form-control" placeholder="Tìm kiếm" value="<?= htmlspecialchars($keyword ?? '') ?>">
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
                            <p class="text-muted">Hiện tại chưa có sản phẩm nào để hiển thị.</p>
                            <a href="<?= BASE_URL ?>home" class="btn btn-primary">Về trang chủ</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-index-right-bottom">
                    <!-- Pagination -->
                    <?php if($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center row">
                                <?php if($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= BASE_URL ?>product?page=<?= $currentPage - 1 ?>">Trước</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= BASE_URL ?>product?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= BASE_URL ?>product?page=<?= $currentPage + 1 ?>">Sau</a>
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
// Apply filters only when clicking the button; also cascade type by category
document.addEventListener('DOMContentLoaded', function() {
    const base = '<?= BASE_URL ?>product/filter';
    const els = {
        category: document.getElementById('filter-category'),
        type: document.getElementById('filter-type'),
        price: document.getElementById('filter-price'),
        size: document.getElementById('filter-size'),
        sort: document.getElementById('filter-sort'),
        apply: document.getElementById('apply-filters')
    };

    // Filter type options to match selected category
    function filterTypeOptions() {
        if (!els.type) return;
        const selectedCat = els.category ? els.category.value : '';
        Array.from(els.type.options).forEach(function(opt) {
            if (!opt.getAttribute) return;
            const cat = opt.getAttribute('data-category');
            if (!cat || !selectedCat) {
                opt.style.display = '';
                return;
            }
            opt.style.display = (cat === selectedCat) ? '' : 'none';
        });
        // Reset type if current hidden
        if (els.type && els.type.selectedOptions.length) {
            const sel = els.type.selectedOptions[0];
            if (sel && sel.style.display === 'none') {
                els.type.value = '';
            }
        }
    }

    if (els.category) {
        els.category.addEventListener('change', filterTypeOptions);
        filterTypeOptions();
    }

    if (els.apply) {
        els.apply.addEventListener('click', function() {
            const params = new URLSearchParams();
            const cat = els.category && els.category.value;
            const type = els.type && els.type.value;
            const price = els.price && els.price.value;
            const size = els.size && els.size.value;
            const sort = els.sort && els.sort.value;
            if (cat) params.set('category', cat);
            if (type) params.set('product_type', type);
            if (price) params.set('price_range', price);
            if (size) params.set('size', size);
            if (sort) params.set('sort', sort);
            const qs = params.toString();
            window.location.href = qs ? `${base}?${qs}` : base;
        });
    }
});
</script>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
