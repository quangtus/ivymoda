<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\product\view.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Chi tiết sản phẩm</h1>
                <a href="<?= ADMIN_URL ?>product" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Quay lại
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="product-image text-center">
                                        <?php if(!empty($product->sanpham_anh)): ?>
                                            <img src="<?= BASE_URL ?>assets/uploads/<?= $product->sanpham_anh ?>" 
                                                 alt="<?= htmlspecialchars($product->sanpham_tieude) ?>" 
                                                 class="img-fluid rounded shadow"
                                                 style="max-height: 400px; object-fit: cover;"
                                                 onerror="this.src='<?= BASE_URL ?>assets/images/no-image.jpg'">
                                        <?php else: ?>
                                            <div class="no-image-placeholder" style="height: 400px; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #f8f9fa; border-radius: 8px;">
                                                <i class="fas fa-image fa-5x text-muted"></i>
                                                <p class="text-muted mt-3">Không có ảnh</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td><?= $product->sanpham_id ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tên sản phẩm:</strong></td>
                                            <td><?= htmlspecialchars($product->sanpham_tieude) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Mã sản phẩm:</strong></td>
                                            <td><code><?= htmlspecialchars($product->sanpham_ma) ?></code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Danh mục:</strong></td>
                                            <td><span class="badge badge-info"><?= htmlspecialchars($product->danhmuc_ten ?? 'N/A') ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Loại sản phẩm:</strong></td>
                                            <td><span class="badge badge-secondary"><?= htmlspecialchars($product->loaisanpham_ten ?? 'N/A') ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Màu sắc:</strong></td>
                                            <td><span class="badge badge-warning"><?= htmlspecialchars($product->color_ten ?? 'N/A') ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Giá:</strong></td>
                                            <td><strong class="text-success"><?= number_format($product->sanpham_gia, 0, ',', '.') ?>đ</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if(!empty($product->sanpham_chitiet)): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Mô tả chi tiết</h6>
                        </div>
                        <div class="card-body">
                            <p><?= nl2br(htmlspecialchars($product->sanpham_chitiet)) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($product->sanpham_baoquan)): ?>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Hướng dẫn bảo quản</h6>
                        </div>
                        <div class="card-body">
                            <p><?= nl2br(htmlspecialchars($product->sanpham_baoquan)) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thao tác</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?= ADMIN_URL ?>product/edit/<?= $product->sanpham_id ?>" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Sửa sản phẩm
                                </a>
                                <a href="<?= ADMIN_URL ?>product/delete/<?= $product->sanpham_id ?>" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                    <i class="fas fa-trash"></i> Xóa sản phẩm
                                </a>
                                <a href="<?= BASE_URL ?>home/product/<?= $product->sanpham_id ?>" 
                                   class="btn btn-info" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Xem trên website
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin bổ sung</h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <i class="fas fa-box fa-3x text-primary mb-3"></i>
                                <h5><?= htmlspecialchars($product->sanpham_tieude) ?></h5>
                                <p class="text-muted">ID: <?= $product->sanpham_id ?></p>
                            </div>
                            
                            <hr>
                            
                            <h6 class="font-weight-bold">Thông tin liên kết:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-folder text-info"></i> Danh mục: <?= htmlspecialchars($product->danhmuc_ten ?? 'N/A') ?></li>
                                <li><i class="fas fa-tags text-secondary"></i> Loại: <?= htmlspecialchars($product->loaisanpham_ten ?? 'N/A') ?></li>
                                <li><i class="fas fa-palette text-warning"></i> Màu: <?= htmlspecialchars($product->color_ten ?? 'N/A') ?></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">Giá sản phẩm</h6>
                        </div>
                        <div class="card-body text-center">
                            <h2 class="text-success font-weight-bold">
                                <?= number_format($product->sanpham_gia, 0, ',', '.') ?>đ
                            </h2>
                            <p class="text-muted">Giá bán lẻ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
