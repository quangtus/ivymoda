<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\product\index.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Quản lý sản phẩm</h1>
            <p class="mb-4">Quản lý tất cả sản phẩm trong hệ thống</p>

            <!-- Thông báo -->
            <?php if(!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Bộ lọc và tìm kiếm -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bộ lọc sản phẩm</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= ADMIN_URL ?>product" class="row">
                        <div class="col-md-3">
                            <label>Tìm kiếm:</label>
                            <input type="text" name="search" class="form-control" placeholder="Tên sản phẩm, mã SP..." value="<?= $_GET['search'] ?? '' ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Danh mục:</label>
                            <select name="category" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="1" <?= (isset($_GET['category']) && $_GET['category'] == '1') ? 'selected' : '' ?>>NỮ</option>
                                <option value="2" <?= (isset($_GET['category']) && $_GET['category'] == '2') ? 'selected' : '' ?>>NAM</option>
                                <option value="3" <?= (isset($_GET['category']) && $_GET['category'] == '3') ? 'selected' : '' ?>>TRẺ EM</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Trạng thái:</label>
                            <select name="status" class="form-control">
                                <option value="">Tất cả</option>
                                <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="0" <?= (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : '' ?>>Tạm dừng</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Sắp xếp:</label>
                            <select name="sort" class="form-control">
                                <option value="newest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
                                <option value="oldest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : '' ?>>Cũ nhất</option>
                                <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá tăng dần</option>
                                <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá giảm dần</option>
                                <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>Tên A-Z</option>
                                <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : '' ?>>Tên Z-A</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                                <a href="<?= ADMIN_URL ?>product" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Xóa bộ lọc
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Danh sách sản phẩm dạng card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
                    <div class="d-flex">
                        <div class="btn-group mr-2" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleView('grid')">
                                <i class="fas fa-th"></i> Lưới
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleView('list')">
                                <i class="fas fa-list"></i> Danh sách
                            </button>
                        </div>
                        <a href="<?= ADMIN_URL ?>product/add" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Thêm sản phẩm mới
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Kiểm tra và hiển thị sản phẩm -->
                    <?php if(isset($products) && is_array($products) && count($products) > 0): ?>
                        <!-- View dạng lưới -->
                        <div id="grid-view" class="row">
                            <?php foreach($products as $product): ?>
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                    <div class="card product-card h-100">
                                        <div class="product-image-container">
                                            <?php 
                                            $imageToShow = $product->first_image ?? $product->sanpham_anh ?? '';
                                            if(!empty($imageToShow)): 
                                            ?>
                                                <img src="<?= BASE_URL ?>assets/uploads/<?= $imageToShow ?>" 
                                                     class="card-img-top product-image" 
                                                     alt="<?= htmlspecialchars($product->sanpham_tieude ?? '') ?>"
                                                     onerror="this.src='<?= BASE_URL ?>assets/images/no-image.svg'">
                                            <?php else: ?>
                                                <div class="no-image-placeholder">
                                                    <i class="fas fa-image fa-3x text-muted"></i>
                                                    <p class="text-muted">No Image</p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Badge trạng thái -->
                                            <div class="product-badges">
                                                <?php if(isset($product->sanpham_noi_bat) && $product->sanpham_noi_bat): ?>
                                                    <span class="badge badge-warning">Nổi bật</span>
                                                <?php endif; ?>
                                                <?php if(isset($product->sanpham_ban_chay) && $product->sanpham_ban_chay): ?>
                                                    <span class="badge badge-success">Bán chạy</span>
                                                <?php endif; ?>
                                                <?php if(isset($product->sanpham_moi) && $product->sanpham_moi): ?>
                                                    <span class="badge badge-info">Mới</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title product-title" title="<?= htmlspecialchars($product->sanpham_tieude ?? '') ?>">
                                                <?= htmlspecialchars(mb_substr($product->sanpham_tieude ?? '', 0, 50)) ?>
                                                <?= mb_strlen($product->sanpham_tieude ?? '') > 50 ? '...' : '' ?>
                                            </h6>
                                            
                                            <div class="product-meta mb-2">
                                                <small class="text-muted">
                                                    <strong>Mã:</strong> <?= htmlspecialchars($product->sanpham_ma ?? 'N/A') ?><br>
                                                    <strong>Danh mục:</strong> <?= htmlspecialchars($product->danhmuc_ten ?? 'N/A') ?><br>
                                                    <strong>Loại:</strong> <?= htmlspecialchars($product->loaisanpham_ten ?? 'N/A') ?><br>
                                                    <strong>Màu:</strong> <?= htmlspecialchars($product->color_ten ?? 'N/A') ?>
                                                </small>
                                            </div>
                                            
                                            <div class="product-price mb-2">
                                                <h6 class="text-danger font-weight-bold mb-0">
                                                    <?= number_format($product->sanpham_gia ?? 0, 0, ',', '.') ?>đ
                                                </h6>
                                                <?php if(isset($product->sanpham_gia_goc) && $product->sanpham_gia_goc > $product->sanpham_gia): ?>
                                                    <small class="text-muted">
                                                        <s><?= number_format($product->sanpham_gia_goc, 0, ',', '.') ?>đ</s>
                                                        <span class="text-success">(-<?= $product->sanpham_giam_gia ?? 0 ?>%)</span>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="product-stats mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-eye"></i> <?= $product->sanpham_luot_xem ?? 0 ?> lượt xem<br>
                                                    <i class="fas fa-star"></i> <?= number_format($product->sanpham_danh_gia ?? 0, 1) ?> (<?= $product->sanpham_so_danh_gia ?? 0 ?> đánh giá)
                                                </small>
                                            </div>
                                            
                                            <div class="mt-auto">
                                                <div class="btn-group w-100" role="group">
                                                    <a href="<?= BASE_URL ?>admin/product/edit/<?= $product->sanpham_id ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </a>
                                                    <a href="<?= BASE_URL ?>admin/productimage/<?= $product->sanpham_id ?>" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-images"></i> Ảnh
                                                    </a>
                                                    <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>" 
                                                       class="btn btn-sm btn-info" target="_blank">
                                                        <i class="fas fa-eye"></i> Xem
                                                    </a>
                                                    <a href="<?= BASE_URL ?>admin/product/delete/<?= $product->sanpham_id ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- View dạng danh sách (ẩn mặc định) -->
                        <div id="list-view" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ảnh</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Mã SP</th>
                                            <th>Danh mục</th>
                                            <th>Giá</th>
                                            <th>Lượt xem</th>
                                            <th>Đánh giá</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($products as $product): ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                $imageToShow = $product->first_image ?? $product->sanpham_anh ?? '';
                                                if(!empty($imageToShow)): 
                                                ?>
                                                    <img src="<?= BASE_URL ?>assets/uploads/<?= $imageToShow ?>" 
                                                         width="50" height="50" 
                                                         class="rounded" 
                                                         alt="<?= htmlspecialchars($product->sanpham_tieude ?? '') ?>"
                                                         onerror="this.src='<?= BASE_URL ?>assets/images/no-image.svg'">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($product->sanpham_tieude ?? 'N/A') ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?= htmlspecialchars($product->loaisanpham_ten ?? 'N/A') ?> - <?= htmlspecialchars($product->color_ten ?? 'N/A') ?></small>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($product->sanpham_ma ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($product->danhmuc_ten ?? 'N/A') ?></td>
                                            <td>
                                                <strong class="text-danger"><?= number_format($product->sanpham_gia ?? 0, 0, ',', '.') ?>đ</strong>
                                                <?php if(isset($product->sanpham_gia_goc) && $product->sanpham_gia_goc > $product->sanpham_gia): ?>
                                                    <br><small class="text-muted"><s><?= number_format($product->sanpham_gia_goc, 0, ',', '.') ?>đ</s></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $product->sanpham_luot_xem ?? 0 ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-warning">
                                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star<?= $i <= ($product->sanpham_danh_gia ?? 0) ? '' : '-o' ?>"></i>
                                                        <?php endfor; ?>
                                                    </span>
                                                    <small class="ml-1">(<?= $product->sanpham_so_danh_gia ?? 0 ?>)</small>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($product->sanpham_status): ?>
                                                    <span class="badge badge-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Tạm dừng</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= BASE_URL ?>admin/product/edit/<?= $product->sanpham_id ?>" 
                                                       class="btn btn-sm btn-primary" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>admin/productimage/<?= $product->sanpham_id ?>" 
                                                       class="btn btn-sm btn-warning" title="Quản lý ảnh">
                                                        <i class="fas fa-images"></i>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>product/detail/<?= $product->sanpham_id ?>" 
                                                       class="btn btn-sm btn-info" target="_blank" title="Xem">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= BASE_URL ?>admin/product/delete/<?= $product->sanpham_id ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')" title="Xóa">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                            
                            <!-- Phân trang -->
                            <nav>
                                <ul class="pagination">
                                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= BASE_URL ?>admin/product?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php else: ?>
                            <div class="alert alert-info">
                                Không có sản phẩm nào. <a href="<?= BASE_URL ?>admin/product/add" class="alert-link">Thêm sản phẩm mới</a>.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>product?page=<?= $currentPage - 1 ?>">Trước</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="<?= ADMIN_URL ?>product?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= ADMIN_URL ?>product?page=<?= $currentPage + 1 ?>">Sau</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thống kê tổng quan -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Tổng sản phẩm</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $totalProducts ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Sản phẩm mới</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= count($products) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-plus-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Trang hiện tại</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= $currentPage ?>/<?= $totalPages ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Sản phẩm/trang</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        10
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-th fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS cho trang quản lý sản phẩm */
.product-card {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: #007bff;
}

.product-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
    background-color: #f8f9fa;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.no-image-placeholder {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    color: #6c757d;
}

.product-badges {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 2;
}

.product-badges .badge {
    margin-right: 5px;
    margin-bottom: 5px;
    font-size: 0.7rem;
}

.product-title {
    font-size: 0.9rem;
    line-height: 1.3;
    margin-bottom: 0.5rem;
    color: #333;
}

.product-meta {
    font-size: 0.8rem;
    line-height: 1.4;
}

.product-price h6 {
    font-size: 1.1rem;
    margin-bottom: 0;
}

.product-stats {
    font-size: 0.8rem;
}

.product-stats i {
    width: 12px;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .product-image-container {
        height: 150px;
    }
    
    .product-title {
        font-size: 0.8rem;
    }
    
    .product-meta {
        font-size: 0.7rem;
    }
}

/* Animation cho toggle view */
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Custom scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

<script>
// JavaScript cho trang quản lý sản phẩm
function toggleView(viewType) {
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const gridBtn = document.querySelector('button[onclick="toggleView(\'grid\')"]');
    const listBtn = document.querySelector('button[onclick="toggleView(\'list\')"]');
    
    if (viewType === 'grid') {
        gridView.classList.remove('d-none');
        listView.classList.add('d-none');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        gridView.classList.add('fade-in');
    } else {
        gridView.classList.add('d-none');
        listView.classList.remove('d-none');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        listView.classList.add('fade-in');
    }
}

// Khởi tạo view mặc định
document.addEventListener('DOMContentLoaded', function() {
    // Set grid view as default active
    const gridBtn = document.querySelector('button[onclick="toggleView(\'grid\')"]');
    if (gridBtn) {
        gridBtn.classList.add('active');
    }
    
    // Add click handlers for better UX
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

// Auto-submit form when filter changes
document.addEventListener('DOMContentLoaded', function() {
    const filterSelects = document.querySelectorAll('select[name="category"], select[name="status"], select[name="sort"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });
});

// Confirm delete with better message
function confirmDelete(productName) {
    return confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${productName}"?\n\nHành động này không thể hoàn tác!`);
}
</script>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>