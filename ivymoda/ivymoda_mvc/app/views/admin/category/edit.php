<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\category\edit.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Sửa danh mục sản phẩm</h1>
                <a href="<?= ADMIN_URL ?>category" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
                </a>
            </div>

            <!-- Thông báo -->
            <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin danh mục</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= ADMIN_URL ?>category/edit/<?= $category->danhmuc_id ?>">
                                <div class="form-group">
                                    <label for="danhmuc_ten" class="font-weight-bold">
                                        Tên danh mục <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?= !empty($error) ? 'is-invalid' : '' ?>" 
                                           id="danhmuc_ten" 
                                           name="danhmuc_ten" 
                                           value="<?= htmlspecialchars($danhmuc_ten) ?>"
                                           placeholder="Nhập tên danh mục sản phẩm"
                                           required
                                           maxlength="255">
                                    <small class="form-text text-muted">
                                        Tên danh mục sẽ hiển thị trên website và trong admin panel
                                    </small>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Cập nhật danh mục
                                    </button>
                                    <a href="<?= ADMIN_URL ?>category" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin danh mục</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-folder fa-3x text-primary mb-3"></i>
                                <h5><?= htmlspecialchars($category->danhmuc_ten) ?></h5>
                                <p class="text-muted">ID: <?= $category->danhmuc_id ?></p>
                            </div>
                            
                            <hr>
                            
                            <h6 class="font-weight-bold">Thống kê:</h6>
                            <?php 
                            $categoryModel = new CategoryModel();
                            $stats = $categoryModel->getCategoryStats($category->danhmuc_id);
                            ?>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-tags text-info"></i> Loại sản phẩm: <strong><?= $stats->subcategories_count ?></strong></li>
                                <li><i class="fas fa-box text-success"></i> Sản phẩm: <strong><?= $stats->products_count ?></strong></li>
                            </ul>

                            <hr>

                            <div class="text-center">
                                <a href="<?= ADMIN_URL ?>category/subcategories/<?= $category->danhmuc_id ?>" 
                                   class="btn btn-info btn-sm">
                                    <i class="fas fa-list"></i> Quản lý loại sản phẩm
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Cảnh báo</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Chú ý:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Thay đổi tên danh mục sẽ ảnh hưởng đến website</li>
                                    <li>Không thể xóa danh mục có sản phẩm</li>
                                    <li>Thay đổi sẽ được áp dụng ngay lập tức</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <?php if($stats->products_count > 0): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-danger">Không thể xóa</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <i class="fas fa-lock"></i>
                                <strong>Danh mục này có <?= $stats->products_count ?> sản phẩm</strong>
                                <p class="mb-0 mt-2">Bạn cần xóa hoặc chuyển tất cả sản phẩm sang danh mục khác trước khi có thể xóa danh mục này.</p>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-danger">Xóa danh mục</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger">
                                <i class="fas fa-trash"></i>
                                <strong>Thao tác nguy hiểm</strong>
                                <p class="mb-3">Xóa danh mục sẽ xóa tất cả loại sản phẩm thuộc danh mục này.</p>
                                <a href="<?= ADMIN_URL ?>category/delete/<?= $category->danhmuc_id ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bạn có chắc muốn xóa danh mục này? Tất cả loại sản phẩm thuộc danh mục cũng sẽ bị xóa.')">
                                    <i class="fas fa-trash"></i> Xóa danh mục
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto focus vào input tên danh mục
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('danhmuc_ten').focus();
    document.getElementById('danhmuc_ten').select();
});

// Validation form
document.querySelector('form').addEventListener('submit', function(e) {
    const danhmucTen = document.getElementById('danhmuc_ten').value.trim();
    
    if (danhmucTen === '') {
        e.preventDefault();
        alert('Vui lòng nhập tên danh mục');
        document.getElementById('danhmuc_ten').focus();
        return false;
    }
    
    if (danhmucTen.length < 2) {
        e.preventDefault();
        alert('Tên danh mục phải có ít nhất 2 ký tự');
        document.getElementById('danhmuc_ten').focus();
        return false;
    }
});
</script>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
