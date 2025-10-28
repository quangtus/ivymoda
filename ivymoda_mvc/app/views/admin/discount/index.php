<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\discount\index.php

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
                    <i class="fas fa-tags"></i> Quản lý mã giảm giá
                </h1>
                <a href="<?= ADMIN_URL ?>discount/add" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Thêm mã giảm giá
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
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách mã giảm giá</h6>
                </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mã Code</th>
                                    <th>Tên</th>
                                    <th>Loại</th>
                                    <th>Giá trị</th>
                                    <th>Ngày bắt đầu</th>
                                    <th>Ngày kết thúc</th>
                                    <th>Số lượng</th>
                                    <th>Đã sử dụng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($discounts)): ?>
                                    <?php foreach ($discounts as $discount): ?>
                                        <tr>
                                            <td><?= $discount->ma_id ?></td>
                                            <td>
                                                <code class="bg-light px-2 py-1 rounded"><?= htmlspecialchars($discount->ma_code) ?></code>
                                            </td>
                                            <td><?= htmlspecialchars($discount->ma_ten) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $discount->loai_giam === 'percent' ? 'info' : 'warning' ?>">
                                                    <?= $discount->loai_giam === 'percent' ? 'Phần trăm' : 'Số tiền' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($discount->loai_giam === 'percent'): ?>
                                                    <?= $discount->ma_giam ?>%
                                                <?php else: ?>
                                                    <?= number_format($discount->ma_giam, 0, ',', '.') ?> ₫
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($discount->ngay_bat_dau)) ?></td>
                                            <td><?= date('d/m/Y', strtotime($discount->ngay_ket_thuc)) ?></td>
                                            <td>
                                                <?= $discount->so_luong ? number_format($discount->so_luong) : 'Không giới hạn' ?>
                                            </td>
                                            <td><?= $discount->da_su_dung ?? 0 ?></td>
                                            <td>
                                                <span class="badge badge-<?= $discount->trang_thai ? 'success' : 'secondary' ?>">
                                                    <?= $discount->trang_thai ? 'Kích hoạt' : 'Vô hiệu' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= ADMIN_URL ?>discount/edit/<?= $discount->ma_id ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= ADMIN_URL ?>discount/toggle/<?= $discount->ma_id ?>" 
                                                       class="btn btn-sm btn-outline-<?= $discount->trang_thai ? 'warning' : 'success' ?>" 
                                                       title="<?= $discount->trang_thai ? 'Vô hiệu hóa' : 'Kích hoạt' ?>">
                                                        <i class="fas fa-<?= $discount->trang_thai ? 'pause' : 'play' ?>"></i>
                                                    </a>
                                                    <a href="<?= ADMIN_URL ?>discount/delete/<?= $discount->ma_id ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       title="Xóa"
                                                       onclick="return confirm('Bạn có chắc muốn xóa mã giảm giá này?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p>Chưa có mã giảm giá nào</p>
                                                <a href="<?= ADMIN_URL ?>discount/add" class="btn btn-primary">
                                                    <i class="fas fa-plus"></i> Thêm mã giảm giá đầu tiên
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Load footer
require_once ROOT_PATH . 'app/views/shared/admin/footer.php';
?>
