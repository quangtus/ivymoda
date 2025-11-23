<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\frontend\product\search.php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php';
?>

<div class="container mt-4 product-search-page">
    <!-- Search form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>product/search" class="row g-3 align-items-end search-form-row">
                        <div class="col-lg-6 col-md-6">
                            <label for="searchKeyword" class="form-label search-label">Từ khóa</label>
                            <input type="text" id="searchKeyword" name="q" class="form-control search-input" 
                                   placeholder="Nhập từ khóa..." 
                                   value="<?= htmlspecialchars($keyword) ?>" required>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <label for="searchCategory" class="form-label search-label">Danh mục</label>
                            <select id="searchCategory" name="category" class="form-select search-select">
                                <option value="">Tất cả danh mục</option>
                                <?php if(isset($categories) && count($categories) > 0): ?>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?= $category->danhmuc_id ?>" 
                                                <?= (isset($filters['category_id']) && $filters['category_id'] == $category->danhmuc_id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category->danhmuc_ten) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-2">
                            <button type="submit" class="btn search-submit w-100">
                                <i class="fas fa-search"></i>
                                <span>Tìm</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">Kết quả tìm kiếm</h1>
            <p class="text-muted">
                Tìm thấy <?= $totalProducts ?> sản phẩm cho từ khóa "<?= htmlspecialchars($keyword) ?>"
            </p>
        </div>
    </div>

    <!-- Results -->
    <div class="search-results-grid">
        <?php if(isset($products) && count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="search-product-item">
                    <div class="product-card">
                        <div class="product-image-wrapper">
                            <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>" class="product-link">
                                <?php 
                                $imageToShow = $product->first_image ?? $product->sanpham_anh ?? '';
                                if(!empty($imageToShow)): 
                                ?>
                                    <img src="<?= BASE_URL ?>assets/uploads/<?= $imageToShow ?>" 
                                         alt="<?= htmlspecialchars($product->sanpham_tieude) ?>" 
                                         class="product-image"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/no-image.svg'">
                                <?php else: ?>
                                    <img src="<?= BASE_URL ?>assets/images/no-image.svg" 
                                         alt="No image" 
                                         class="product-image"
                                         loading="lazy">
                                <?php endif; ?>
                            </a>
                        </div>
                        
                        <div class="product-content">
                            <h3 class="product-title">
                                <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>" class="product-title-link">
                                    <?= htmlspecialchars($product->sanpham_tieude) ?>
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
                                <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>" class="view-detail-btn">
                                    <i class="fas fa-eye"></i>
                                    <span>Xem chi tiết</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
                <div class="search-pagination">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= BASE_URL ?>product/search?q=<?= urlencode($keyword) ?>&category=<?= $filters['category_id'] ?? '' ?>&page=<?= $currentPage - 1 ?>">Trước</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>product/search?q=<?= urlencode($keyword) ?>&category=<?= $filters['category_id'] ?? '' ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= BASE_URL ?>product/search?q=<?= urlencode($keyword) ?>&category=<?= $filters['category_id'] ?? '' ?>&page=<?= $currentPage + 1 ?>">Sau</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="search-no-results">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Không tìm thấy sản phẩm nào</h4>
                    <p class="text-muted">Không có sản phẩm nào phù hợp với từ khóa "<?= htmlspecialchars($keyword) ?>"</p>
                    <a href="<?= BASE_URL ?>product" class="btn btn-primary">Xem tất cả sản phẩm</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.product-search-page {
    padding-top: 100px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.search-form-row {
    gap: 15px;
}

.search-label {
    font-weight: 600;
    color: #444;
    margin-bottom: 6px;
}

.search-input,
.search-select {
    min-height: 48px;
    border-radius: 8px;
    border: 1px solid #d9d9d9;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.04);
    padding: 0.6rem 0.85rem;
}

.search-input:focus,
.search-select:focus {
    border-color: #4dabf7;
    box-shadow: 0 0 0 3px rgba(77, 171, 247, 0.25);
}

.search-submit {
    min-height: 48px;
    min-width: 120px;
    border-radius: 8px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);
    border: none;
    color: #fff;
    box-shadow: 0 6px 15px rgba(255, 94, 98, 0.35);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.search-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(255, 94, 98, 0.45);
}

.search-form-row .col-lg-6,
.search-form-row .col-lg-4,
.search-form-row .col-lg-2,
.search-form-row .col-md-6,
.search-form-row .col-md-4,
.search-form-row .col-md-2 {
    padding-top: 5px;
    padding-bottom: 5px;
}

/* Search Results Grid - Giống trang tất cả sản phẩm */
.search-results-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    justify-content: flex-start;
    width: 100%;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.search-product-item {
    flex: 0 0 calc(25% - 1.125rem);
    min-width: 280px;
    max-width: 350px;
    padding: 0;
}

@media (max-width: 1400px) {
    .search-product-item {
        flex: 0 0 calc(33.333% - 1rem);
    }
}

@media (max-width: 992px) {
    .search-product-item {
        flex: 0 0 calc(50% - 0.75rem);
        min-width: 250px;
    }
}

@media (max-width: 768px) {
    .search-product-item {
        flex: 0 0 calc(50% - 0.75rem);
        min-width: 200px;
        max-width: none;
    }
}

@media (max-width: 576px) {
    .search-product-item {
        flex: 0 0 100%;
        min-width: auto;
        max-width: none;
    }
}

/* Product Card - Bo tròn như trang tất cả sản phẩm */
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

/* Image Wrapper - Bo tròn */
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

/* Product Content - Cách đều giữa các thành phần */
.product-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

/* Product Title - To lên */
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

.product-actions {
    margin-top: auto;
    padding-top: 1rem;
}

.view-detail-btn {
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
    text-decoration: none;
}

.view-detail-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    background: linear-gradient(45deg, #0056b3, #004085);
    color: white;
    text-decoration: none;
}

/* Pagination */
.search-pagination {
    width: 100%;
    margin: 30px 0 20px;
    flex: 0 0 100%;
}

.search-pagination .pagination {
    margin: 0;
}

.search-pagination .page-item {
    margin-right: 8px;
}

.search-pagination .page-link {
    border-radius: 6px;
    padding: 8px 16px;
    color: #007bff;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.search-pagination .page-item.active .page-link {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.search-pagination .page-link:hover {
    background: #e9ecef;
    border-color: #007bff;
    color: #007bff;
}

/* No Results */
.search-no-results {
    width: 100%;
    padding: 40px 20px;
}
</style>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
