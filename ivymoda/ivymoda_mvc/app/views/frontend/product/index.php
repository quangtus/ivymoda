<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\frontend\product\index.php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php';
?>

<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>home">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tất cả sản phẩm</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">Tất cả sản phẩm</h1>
            <p class="text-muted">Tìm thấy <?= $totalProducts ?> sản phẩm</p>
        </div>
        <div class="col-md-4">
            <!-- Search form -->
            <form method="GET" action="<?= BASE_URL ?>product/search" class="d-flex">
                <input type="text" name="q" class="form-control" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($keyword ?? '') ?>">
                <button type="submit" class="btn btn-primary ml-2">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Filter sidebar -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <!-- Danh mục -->
                    <div class="mb-3">
                        <h6>Danh mục</h6>
                        <div class="list-group list-group-flush">
                            <a href="<?= BASE_URL ?>product" class="list-group-item list-group-item-action">
                                Tất cả sản phẩm
                            </a>
                            <?php if(isset($categories) && count($categories) > 0): ?>
                                <?php foreach($categories as $category): ?>
                                    <a href="<?= BASE_URL ?>product/category/<?= $category->danhmuc_id ?>" 
                                       class="list-group-item list-group-item-action">
                                        <?= htmlspecialchars($category->danhmuc_ten) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product grid -->
        <div class="col-md-9">
            <?php if(isset($products) && count($products) > 0): ?>
                <div class="products-grid-balanced">
                    <?php foreach($products as $product): ?>
                        <div class="product-component">
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
                </div>

                <!-- Pagination -->
                <?php if($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
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

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Không có sản phẩm nào</h4>
                    <p class="text-muted">Hiện tại chưa có sản phẩm nào để hiển thị.</p>
                    <a href="<?= BASE_URL ?>home" class="btn btn-primary">Về trang chủ</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Grid Layout Cân Đối */
.products-grid-balanced {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    width: 100%;
    box-sizing: border-box;
    padding: 0;
}

/* Product Component */
.product-component {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
}

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

/* Image Wrapper - KHÔNG XO XO ẢNH */
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

/* Product Content */
.product-content {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

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

.add-to-cart-btn {
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
}

.add-to-cart-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
    background: linear-gradient(45deg, #0056b3, #004085);
}

.badge {
    margin-right: 5px;
    margin-bottom: 5px;
}
</style>

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
