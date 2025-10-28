<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\user\index.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Quản lý người dùng</h1>
            <p class="mb-4">Quản lý tất cả người dùng trong hệ thống</p>

    <!-- Thông báo -->
    <?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php 
        echo $_SESSION['success']; 
        unset($_SESSION['success']);
        ?>
    </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php 
        echo $_SESSION['error']; 
        unset($_SESSION['error']);
        ?>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách người dùng</h6>
            <a href="<?= ADMIN_URL ?>user/add" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Thêm người dùng mới
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $user): ?>
                                <tr>
                                    <td><?= $user->id ?></td>
                                    <td><?= $user->username ?></td>
                                    <td><?= $user->fullname ?></td>
                                    <td><?= $user->email ?></td>
                                    <td>
                                        <?php 
                                        if($user->role_id == 1) echo "Admin";
                                        else if($user->role_id == 2) echo "Khách hàng";
                                        else if($user->role_id == 3) echo "Nhân viên";
                                        else echo $user->role_name;
                                        ?>
                                    </td>
                                    <td>
                                        <?php if($user->status == 1): ?>
                                            <span class="badge badge-success">Hoạt động</span>
                                        <?php elseif($user->status == 0): ?>
                                            <span class="badge badge-secondary">Vô hiệu hóa</span>
                                        <?php elseif($user->status == 2): ?>
                                            <span class="badge badge-warning">Đã khóa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= ADMIN_URL ?>user/edit/<?= $user->id ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <?php if($user->id != $_SESSION['user_id']): ?>
                                            <?php if($user->status == 1): ?>
                                                <a href="<?= ADMIN_URL ?>user/status/<?= $user->id ?>/0" class="btn btn-sm btn-secondary" onclick="return confirm('Bạn có chắc muốn vô hiệu hóa người dùng này?')">
                                                    <i class="fas fa-ban"></i> Vô hiệu hóa
                                                </a>
                                            <?php elseif($user->status == 0 || $user->status == 2): ?>
                                                <a href="<?= ADMIN_URL ?>user/status/<?= $user->id ?>/1" class="btn btn-sm btn-success" onclick="return confirm('Bạn có chắc muốn kích hoạt người dùng này?')">
                                                    <i class="fas fa-check"></i> Kích hoạt
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= ADMIN_URL ?>user/delete/<?= $user->id ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có người dùng nào</td>
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

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>