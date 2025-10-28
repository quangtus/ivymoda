<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\category\add.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Thêm danh mục sản phẩm</h1>
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
                            <form method="POST" action="<?= ADMIN_URL ?>category/add">
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
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Lưu ý:</strong> Sau khi tạo danh mục, bạn có thể thêm các loại sản phẩm con vào danh mục này.
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Thêm danh mục
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
                            <h6 class="m-0 font-weight-bold text-primary">Hướng dẫn</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-folder-plus fa-3x text-primary mb-3"></i>
                                <h5>Tạo danh mục mới</h5>
                                <p class="text-muted">
                                    Danh mục sản phẩm giúp phân loại và tổ chức sản phẩm một cách có hệ thống.
                                </p>
                            </div>
                            
                            <hr>
                            
                            <h6 class="font-weight-bold">Các bước tiếp theo:</h6>
                            <ol class="small">
                                <li>Tạo danh mục sản phẩm</li>
                                <li>Thêm các loại sản phẩm con</li>
                                <li>Tạo sản phẩm thuộc danh mục</li>
                                <li>Quản lý và cập nhật thông tin</li>
                            </ol>

                            <hr>

                            <h6 class="font-weight-bold">Ví dụ danh mục:</h6>
                            <ul class="small text-muted">
                                <li>NỮ</li>
                                <li>NAM</li>
                                <li>TRẺ EM</li>
                                <li>PHỤ KIỆN</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">Lưu ý quan trọng</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Chú ý:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Tên danh mục không được trùng lặp</li>
                                    <li>Không thể xóa danh mục có sản phẩm</li>
                                    <li>Danh mục sẽ hiển thị trên website</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto focus vào input tên danh mục
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('danhmuc_ten').focus();
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
