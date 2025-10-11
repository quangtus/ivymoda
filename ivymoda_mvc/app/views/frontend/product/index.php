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
            <div class = "product-index-left">
                <ul>
                    <?php if(isset($categories) && count($categories) > 0): ?>
                        <?php foreach($categories as $category): ?>
                            <li class="product-index-left"><a href="<?= BASE_URL ?>product/category/<?= $category->danhmuc_id ?>" 
                            class="list-group-item list-group-item-action">
                                <?= htmlspecialchars($category->danhmuc_ten) ?>
                            </a></li> 
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class = "product-index-right row">
                <div class = "product-index-right-top-item">
                    <h1 class="h2">Tất cả sản phẩm</h1>
                    <p class="text-muted">Tìm thấy <?= $totalProducts ?> sản phẩm</p>
                </div>
                <div class="product-index-right-top-item">
                    <button><span>Bộ lọc</span> <i class="fas fa-sort-down"></i></button>
                </div>
                <div class="product-index-right-top-item">
                    <select name = "" id = "">
                        <option value = "">Sắp xếp theo</option>
                        <option value = "">Giá thấp đến cao</option>
                        <option value = "">Giá cao đến thấp</option>
                    </select>
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

<?php require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
