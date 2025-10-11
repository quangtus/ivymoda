<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\frontend\product\category.php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php';
?>

<div class="container mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>home">Trang chủ</a></li>
            <span class="">&#8594;</span>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>product">Sản phẩm</a></li>
            <span class="">&#8594;</span>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category->danhmuc_ten) ?></li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2"><?= htmlspecialchars($category->danhmuc_ten) ?></h1>
            <p class="text-muted">Tìm thấy <?= $totalProducts ?> sản phẩm</p>
        </div>
        <div class="col-md-4">
            <!-- Search form -->
            <form method="GET" action="<?= BASE_URL ?>product/search" class="d-flex">
                <input type="text" name="q" class="form-control" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($keyword ?? '') ?>">
                <input type="hidden" name="category" value="<?= $category->danhmuc_id ?>">
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
                                <?php foreach($categories as $cat): ?>
                                    <a href="<?= BASE_URL ?>product/category/<?= $cat->danhmuc_id ?>" 
                                       class="list-group-item list-group-item-action <?= $cat->danhmuc_id == $category->danhmuc_id ? 'active' : '' ?>">
                                        <?= htmlspecialchars($cat->danhmuc_ten) ?>
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
                                    <a class="page-link" href="<?= BASE_URL ?>product/category/<?= $category->danhmuc_id ?>?page=<?= $currentPage - 1 ?>">Trước</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= BASE_URL ?>product/category/<?= $category->danhmuc_id ?>?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= BASE_URL ?>product/category/<?= $category->danhmuc_id ?>?page=<?= $currentPage + 1 ?>">Sau</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Không có sản phẩm nào</h4>
                    <p class="text-muted">Danh mục "<?= htmlspecialchars($category->danhmuc_ten) ?>" chưa có sản phẩm nào.</p>
                    <a href="<?= BASE_URL ?>product" class="btn btn-primary">Xem tất cả sản phẩm</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/product-category.css">

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>