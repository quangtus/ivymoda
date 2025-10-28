<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\size\add.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-plus-circle"></i> Thêm Size Mới
                </h1>
                <a href="<?= ADMIN_URL ?>size" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
                </a>
            </div>

            <!-- Thông báo lỗi -->
            <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle"></i> Lỗi!</strong><br>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Form thêm size -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit"></i> Thông tin Size
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="size_ten">
                                        Tên Size <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="size_ten" 
                                           name="size_ten" 
                                           placeholder="VD: XS, S, M, L, XL, XXL, 3XL..." 
                                           value="<?= htmlspecialchars($size_ten ?? '') ?>"
                                           required
                                           autofocus>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Nhập tên size (viết hoa). VD: XS, S, M, L, XL, XXL, 3XL, 4XL, FREE SIZE...
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="size_order">
                                        Thứ tự hiển thị <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="size_order" 
                                           name="size_order" 
                                           placeholder="VD: 1, 2, 3..." 
                                           value="<?= htmlspecialchars($size_order ?? '0') ?>"
                                           min="0"
                                           required>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Số nhỏ hơn hiển thị trước. VD: XS=1, S=2, M=3, L=4, XL=5, XXL=6, 3XL=7
                                    </small>
                                </div>

                                <hr>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu Size
                                </button>
                                <a href="<?= ADMIN_URL ?>size" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Hướng dẫn -->
                    <div class="card shadow mb-4 border-left-info">
                        <div class="card-header py-3 bg-info text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-lightbulb"></i> Gợi ý tên size
                            </h6>
                        </div>
                        <div class="card-body">
                            <h6 class="font-weight-bold">Size áo/quần thông thường:</h6>
                            <div class="mb-3">
                                <span class="badge badge-primary mr-1 mb-1">XS</span>
                                <span class="badge badge-primary mr-1 mb-1">S</span>
                                <span class="badge badge-primary mr-1 mb-1">M</span>
                                <span class="badge badge-primary mr-1 mb-1">L</span>
                                <span class="badge badge-primary mr-1 mb-1">XL</span>
                                <span class="badge badge-primary mr-1 mb-1">XXL</span>
                                <span class="badge badge-primary mr-1 mb-1">3XL</span>
                                <span class="badge badge-primary mr-1 mb-1">4XL</span>
                            </div>

                            <h6 class="font-weight-bold">Size số:</h6>
                            <div class="mb-3">
                                <span class="badge badge-secondary mr-1 mb-1">27</span>
                                <span class="badge badge-secondary mr-1 mb-1">28</span>
                                <span class="badge badge-secondary mr-1 mb-1">29</span>
                                <span class="badge badge-secondary mr-1 mb-1">30</span>
                                <span class="badge badge-secondary mr-1 mb-1">31</span>
                                <span class="badge badge-secondary mr-1 mb-1">32</span>
                            </div>

                            <h6 class="font-weight-bold">Size đặc biệt:</h6>
                            <div>
                                <span class="badge badge-success mr-1 mb-1">FREE SIZE</span>
                                <span class="badge badge-success mr-1 mb-1">ONE SIZE</span>
                            </div>
                        </div>
                    </div>

                    <!-- Lưu ý -->
                    <div class="card shadow mb-4 border-left-warning">
                        <div class="card-header py-3 bg-warning text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-exclamation-triangle"></i> Lưu ý
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0 small">
                                <li>Tên size không được trùng</li>
                                <li>Nên sử dụng chữ IN HOA</li>
                                <li>Thứ tự quyết định cách hiển thị trên website</li>
                                <li>Sau khi thêm, size sẽ xuất hiện trong form thêm/sửa sản phẩm</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
