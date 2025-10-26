<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\category\index.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Quản lý danh mục sản phẩm</h1>
            <p class="mb-4">Quản lý tất cả danh mục và loại sản phẩm trong hệ thống</p>

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

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách danh mục sản phẩm</h6>
                    <a href="<?= ADMIN_URL ?>category/add" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Thêm danh mục mới
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên danh mục</th>
                                    <th>Số loại sản phẩm</th>
                                    <th>Số sản phẩm</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($categories)): ?>
                                    <?php foreach($categories as $category): ?>
                                        <?php 
                                        // Lấy thống kê cho danh mục
                                        $categoryModel = new CategoryModel();
                                        $stats = $categoryModel->getCategoryStats($category->danhmuc_id);
                                        ?>
                                        <tr>
                                            <td><?= $category->danhmuc_id ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($category->danhmuc_ten) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?= $stats->subcategories_count ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success"><?= $stats->products_count ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= ADMIN_URL ?>category/subcategories/<?= $category->danhmuc_id ?>" 
                                                       class="btn btn-sm btn-info" title="Quản lý loại sản phẩm">
                                                        <i class="fas fa-list"></i> Loại SP
                                                    </a>
                                                    <a href="<?= ADMIN_URL ?>category/edit/<?= $category->danhmuc_id ?>" 
                                                       class="btn btn-sm btn-warning" title="Sửa danh mục">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </a>
                                                    <?php if($stats->products_count == 0): ?>
                                                        <a href="<?= ADMIN_URL ?>category/delete/<?= $category->danhmuc_id ?>" 
                                                           class="btn btn-sm btn-danger" 
                                                           title="Xóa danh mục"
                                                           onclick="return confirm('Bạn có chắc muốn xóa danh mục này? Tất cả loại sản phẩm thuộc danh mục cũng sẽ bị xóa.')">
                                                            <i class="fas fa-trash"></i> Xóa
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-secondary" disabled title="Không thể xóa danh mục có sản phẩm">
                                                            <i class="fas fa-lock"></i> Khóa
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                                            Chưa có danh mục nào. <a href="<?= ADMIN_URL ?>category/add">Thêm danh mục đầu tiên</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
                                        Tổng danh mục</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?= count($categories) ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-folder fa-2x text-gray-300"></i>
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
                                        Tổng loại sản phẩm</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php 
                                        $totalSubcategories = 0;
                                        if(!empty($categories)) {
                                            foreach($categories as $category) {
                                                $categoryModel = new CategoryModel();
                                                $stats = $categoryModel->getCategoryStats($category->danhmuc_id);
                                                $totalSubcategories += $stats->subcategories_count;
                                            }
                                        }
                                        echo $totalSubcategories;
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                                        Tổng sản phẩm</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php 
                                        $totalProducts = 0;
                                        if(!empty($categories)) {
                                            foreach($categories as $category) {
                                                $categoryModel = new CategoryModel();
                                                $stats = $categoryModel->getCategoryStats($category->danhmuc_id);
                                                $totalProducts += $stats->products_count;
                                            }
                                        }
                                        echo $totalProducts;
                                        ?>
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
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Danh mục trống</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php 
                                        $emptyCategories = 0;
                                        if(!empty($categories)) {
                                            foreach($categories as $category) {
                                                $categoryModel = new CategoryModel();
                                                $stats = $categoryModel->getCategoryStats($category->danhmuc_id);
                                                if($stats->products_count == 0) {
                                                    $emptyCategories++;
                                                }
                                            }
                                        }
                                        echo $emptyCategories;
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
