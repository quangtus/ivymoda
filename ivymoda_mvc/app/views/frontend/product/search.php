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
    <div class="row" style="margin-left: -10px; margin-right: -10px;">
        <?php if(isset($products) && count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="col-lg-4 col-md-6 mb-4" style="padding-left: 10px; padding-right: 10px;">
                    <div class="card h-100">
                        <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>">
                            <div class="product-image">
                                <?php 
                                $imageToShow = $product->first_image ?? $product->sanpham_anh ?? '';
                                if(!empty($imageToShow)): 
                                ?>
                                    <img src="<?= BASE_URL ?>assets/uploads/<?= $imageToShow ?>" 
                                         alt="<?= htmlspecialchars($product->sanpham_tieude) ?>" 
                                         class="card-img-top"
                                         onerror="this.onerror=null; this.src='<?= BASE_URL ?>assets/images/no-image.svg'">
                                <?php else: ?>
                                    <img src="<?= BASE_URL ?>assets/images/no-image.svg" 
                                         alt="No image" 
                                         class="card-img-top">
                                <?php endif; ?>
                            </div>
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">
                                <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>" 
                                   class="text-decoration-none text-dark">
                                    <?= htmlspecialchars($product->sanpham_tieude) ?>
                                </a>
                            </h6>
                            <p class="card-text text-danger font-weight-bold mb-2">
                                <?= number_format($product->sanpham_gia, 0, ',', '.') ?>đ
                            </p>
                            <div class="product-meta mb-2">
                                <small class="text-muted">
                                    <span class="badge badge-light"><?= htmlspecialchars($product->danhmuc_ten ?? 'N/A') ?></span>
                                    <?php if(!empty($product->loaisanpham_ten)): ?>
                                        <span class="badge badge-light"><?= htmlspecialchars($product->loaisanpham_ten) ?></span>
                                    <?php endif; ?>
                                </small>
                            </div>
                            <div class="mt-auto">
                                <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>" 
                                   class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-eye"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
                <div class="col-12">
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
            <div class="col-12">
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
.product-image {
    height: 250px;
    overflow: hidden;
    position: relative;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-image:hover img {
    transform: scale(1.05);
}

.product-search-page {
    padding-top: 100px;
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

.card {
    transition: box-shadow 0.3s ease;
    margin-bottom: 20px;
}

.card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.badge {
    margin-right: 5px;
    margin-bottom: 5px;
}
</style>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
