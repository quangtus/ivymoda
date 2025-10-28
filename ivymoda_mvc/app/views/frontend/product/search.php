<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\frontend\product\search.php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php';
?>

<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>home">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>product">Sản phẩm</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tìm kiếm: "<?= htmlspecialchars($keyword) ?>"</li>
        </ol>
    </nav>

    <!-- Search form -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="<?= BASE_URL ?>product/search" class="row">
                        <div class="col-md-4">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Tìm kiếm sản phẩm..." 
                                   value="<?= htmlspecialchars($keyword) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-control">
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
                        <div class="col-md-2">
                            <input type="number" name="min_price" class="form-control" 
                                   placeholder="Giá từ" 
                                   value="<?= $filters['min_price'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="max_price" class="form-control" 
                                   placeholder="Giá đến" 
                                   value="<?= $filters['max_price'] ?? '' ?>">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i>
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
    <div class="row">
        <?php if(isset($products) && count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>">
                            <div class="product-image">
                                <?php if(!empty($product->sanpham_anh)): ?>
                                    <img src="<?= BASE_URL ?>assets/uploads/<?= $product->sanpham_anh ?>" 
                                         alt="<?= htmlspecialchars($product->sanpham_tieude) ?>" 
                                         class="card-img-top"
                                         onerror="this.src='<?= BASE_URL ?>assets/images/no-image.jpg'">
                                <?php else: ?>
                                    <img src="<?= BASE_URL ?>assets/images/no-image.jpg" 
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
                                    <?php if(!empty($product->color_ten)): ?>
                                        <span class="badge badge-light"><?= htmlspecialchars($product->color_ten) ?></span>
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
                                    <a class="page-link" href="<?= BASE_URL ?>product/search?q=<?= urlencode($keyword) ?>&category=<?= $filters['category_id'] ?? '' ?>&min_price=<?= $filters['min_price'] ?? '' ?>&max_price=<?= $filters['max_price'] ?? '' ?>&page=<?= $currentPage - 1 ?>">Trước</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>product/search?q=<?= urlencode($keyword) ?>&category=<?= $filters['category_id'] ?? '' ?>&min_price=<?= $filters['min_price'] ?? '' ?>&max_price=<?= $filters['max_price'] ?? '' ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= BASE_URL ?>product/search?q=<?= urlencode($keyword) ?>&category=<?= $filters['category_id'] ?? '' ?>&min_price=<?= $filters['min_price'] ?? '' ?>&max_price=<?= $filters['max_price'] ?? '' ?>&page=<?= $currentPage + 1 ?>">Sau</a>
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

.card {
    transition: box-shadow 0.3s ease;
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
