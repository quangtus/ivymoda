<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\size\index.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-ruler-horizontal"></i> Quản lý Size
                </h1>
                <a href="<?= ADMIN_URL ?>size/add" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Thêm size mới
                </a>
            </div>

            <!-- Thông báo -->
            <?php if(!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= $success ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <?php if(!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list"></i> Danh sách sizes (<?= count($sizes ?? []) ?>)
                    </h6>
                </div>
                <div class="card-body">
                    <?php if(!empty($sizes)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="30%">Tên Size</th>
                                    <th width="20%">Thứ tự hiển thị</th>
                                    <th width="20%">Ngày tạo</th>
                                    <th width="20%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($sizes as $size): ?>
                                <tr>
                                    <td><strong>#<?= $size->size_id ?></strong></td>
                                    <td>
                                        <span class="badge badge-lg badge-primary" style="font-size: 14px; padding: 8px 15px;">
                                            <?= htmlspecialchars($size->size_ten) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary"><?= $size->size_order ?></span>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y H:i', strtotime($size->created_at)) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= ADMIN_URL ?>size/edit/<?= $size->size_id ?>" 
                                           class="btn btn-sm btn-warning" 
                                           title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= ADMIN_URL ?>size/delete/<?= $size->size_id ?>" 
                                           class="btn btn-sm btn-danger" 
                                           title="Xóa"
                                           onclick="return confirm('Bạn có chắc muốn xóa size \'<?= addslashes($size->size_ten) ?>\' không?\n\nLưu ý: Không thể xóa nếu size đang được sử dụng!');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Chưa có size nào. <a href="<?= ADMIN_URL ?>size/add">Thêm size đầu tiên</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-lightbulb"></i> Hướng dẫn sử dụng
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li><strong>Thêm size mới:</strong> Nhấn nút "Thêm size mới" và điền tên size (VD: S, M, L, XL, XXL, 3XL, 4XL...)</li>
                        <li><strong>Thứ tự hiển thị:</strong> Số nhỏ hơn sẽ hiển thị trước (VD: XS=1, S=2, M=3, L=4...)</li>
                        <li><strong>Xóa size:</strong> Chỉ có thể xóa size chưa được sử dụng trong bất kỳ sản phẩm nào</li>
                        <li><strong>Sử dụng:</strong> Sau khi thêm size, bạn có thể chọn size này khi thêm/sửa sản phẩm</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
