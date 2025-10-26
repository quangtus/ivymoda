<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\category\subcategories.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Quản lý loại sản phẩm</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>category">Danh mục</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category->danhmuc_ten) ?></li>
                    </ol>
                </nav>
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
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-tags"></i> Loại sản phẩm - <?= htmlspecialchars($category->danhmuc_ten) ?>
                            </h6>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSubcategoryModal">
                                <i class="fas fa-plus"></i> Thêm loại sản phẩm
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên loại sản phẩm</th>
                                            <th>Số sản phẩm</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($subcategories)): ?>
                                            <?php foreach($subcategories as $subcategory): ?>
                                                <?php 
                                                // Lấy số sản phẩm trong loại này
                                                $categoryModel = new CategoryModel();
                                                $hasProducts = $categoryModel->hasProductsInSubcategory($subcategory->loaisanpham_id);
                                                ?>
                                                <tr>
                                                    <td><?= $subcategory->loaisanpham_id ?></td>
                                                    <td>
                                                        <strong><?= htmlspecialchars($subcategory->loaisanpham_ten) ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php if($hasProducts): ?>
                                                            <span class="badge badge-success">Có sản phẩm</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Trống</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-warning" 
                                                                    onclick="editSubcategory(<?= $subcategory->loaisanpham_id ?>, '<?= htmlspecialchars($subcategory->loaisanpham_ten) ?>')"
                                                                    title="Sửa loại sản phẩm">
                                                                <i class="fas fa-edit"></i> Sửa
                                                            </button>
                                                            <?php if(!$hasProducts): ?>
                                                                <a href="<?= ADMIN_URL ?>category/deleteSubcategory/<?= $subcategory->loaisanpham_id ?>" 
                                                                   class="btn btn-sm btn-danger" 
                                                                   title="Xóa loại sản phẩm"
                                                                   onclick="return confirm('Bạn có chắc muốn xóa loại sản phẩm này?')">
                                                                    <i class="fas fa-trash"></i> Xóa
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-sm btn-secondary" disabled title="Không thể xóa loại sản phẩm có sản phẩm">
                                                                    <i class="fas fa-lock"></i> Khóa
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    <i class="fas fa-tags fa-2x mb-2"></i><br>
                                                    Chưa có loại sản phẩm nào. 
                                                    <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#addSubcategoryModal">
                                                        Thêm loại sản phẩm đầu tiên
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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
                            <ul class="list-unstyled">
                                <li><i class="fas fa-tags text-info"></i> Loại sản phẩm: <strong><?= count($subcategories) ?></strong></li>
                            </ul>

                            <hr>

                            <div class="text-center">
                                <a href="<?= ADMIN_URL ?>category/edit/<?= $category->danhmuc_id ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Sửa danh mục
                                </a>
                                <a href="<?= ADMIN_URL ?>category" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">Hướng dẫn</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-lightbulb fa-2x text-info mb-3"></i>
                                <h6>Tạo loại sản phẩm</h6>
                                <p class="text-muted small">
                                    Loại sản phẩm giúp phân chia chi tiết hơn các sản phẩm trong danh mục.
                                </p>
                            </div>
                            
                            <hr>
                            
                            <h6 class="font-weight-bold">Ví dụ loại sản phẩm:</h6>
                            <ul class="small text-muted">
                                <li><strong>NỮ:</strong> Áo Nữ, Quần Nữ, Đầm Nữ</li>
                                <li><strong>NAM:</strong> Áo Nam, Quần Nam, Áo Khoác</li>
                                <li><strong>TRẺ EM:</strong> Áo Trẻ Em, Quần Trẻ Em</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm loại sản phẩm -->
<div class="modal fade" id="addSubcategoryModal" tabindex="-1" role="dialog" aria-labelledby="addSubcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubcategoryModalLabel">
                    <i class="fas fa-plus"></i> Thêm loại sản phẩm
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="<?= ADMIN_URL ?>category/subcategories/<?= $category->danhmuc_id ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="loaisanpham_ten" class="font-weight-bold">
                            Tên loại sản phẩm <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="loaisanpham_ten" 
                               name="loaisanpham_ten" 
                               value="<?= htmlspecialchars($loaisanpham_ten) ?>"
                               placeholder="Nhập tên loại sản phẩm"
                               required
                               maxlength="255">
                        <small class="form-text text-muted">
                            Tên loại sản phẩm sẽ hiển thị trong danh sách sản phẩm
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" name="add_subcategory" class="btn btn-primary">
                        <i class="fas fa-save"></i> Thêm loại sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa loại sản phẩm -->
<div class="modal fade" id="editSubcategoryModal" tabindex="-1" role="dialog" aria-labelledby="editSubcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubcategoryModalLabel">
                    <i class="fas fa-edit"></i> Sửa loại sản phẩm
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editSubcategoryForm" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_loaisanpham_ten" class="font-weight-bold">
                            Tên loại sản phẩm <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="edit_loaisanpham_ten" 
                               name="loaisanpham_ten" 
                               placeholder="Nhập tên loại sản phẩm"
                               required
                               maxlength="255">
                        <small class="form-text text-muted">
                            Tên loại sản phẩm sẽ hiển thị trong danh sách sản phẩm
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" name="edit_subcategory" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Hàm sửa loại sản phẩm
function editSubcategory(id, name) {
    document.getElementById('edit_loaisanpham_ten').value = name;
    document.getElementById('editSubcategoryForm').action = '<?= ADMIN_URL ?>category/editSubcategory/' + id;
    $('#editSubcategoryModal').modal('show');
}

// Auto focus vào input khi mở modal
$('#addSubcategoryModal').on('shown.bs.modal', function () {
    $('#loaisanpham_ten').focus();
});

$('#editSubcategoryModal').on('shown.bs.modal', function () {
    $('#edit_loaisanpham_ten').focus();
    $('#edit_loaisanpham_ten').select();
});

// Validation form
document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        const input = this.querySelector('input[name="loaisanpham_ten"]');
        const value = input.value.trim();
        
        if (value === '') {
            e.preventDefault();
            alert('Vui lòng nhập tên loại sản phẩm');
            input.focus();
            return false;
        }
        
        if (value.length < 2) {
            e.preventDefault();
            alert('Tên loại sản phẩm phải có ít nhất 2 ký tự');
            input.focus();
            return false;
        }
    });
});
</script>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
