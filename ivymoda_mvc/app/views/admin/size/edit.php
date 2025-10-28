<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\size\edit.php

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
                    <i class="fas fa-edit"></i> Sửa Size: <span class="text-primary"><?= htmlspecialchars($size->size_ten ?? '') ?></span>
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

            <!-- Form sửa size -->
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
                                    <i class="fas fa-save"></i> Cập nhật
                                </button>
                                <a href="<?= ADMIN_URL ?>size" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Thông tin size -->
                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header py-3 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-info-circle"></i> Thông tin hiện tại
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td width="40%"><strong>ID:</strong></td>
                                    <td>#<?= $size->size_id ?? '' ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td><?= isset($size->created_at) ? date('d/m/Y H:i', strtotime($size->created_at)) : '' ?></td>
                                </tr>
                            </table>
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
                                <li>Thay đổi tên size sẽ ảnh hưởng đến tất cả sản phẩm đang sử dụng size này</li>
                                <li>Tên size không được trùng với size khác</li>
                                <li>Nên sử dụng chữ IN HOA</li>
                                <li>Thứ tự quyết định cách hiển thị trên website</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
