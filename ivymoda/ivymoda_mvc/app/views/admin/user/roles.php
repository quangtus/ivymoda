<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\user\roles.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';

// Đảm bảo các biến tồn tại
$role_name = isset($role_name) ? $role_name : '';
$description = isset($description) ? $description : '';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Quản lý vai trò người dùng</h1>
            <p class="mb-4">Quản lý các vai trò trong hệ thống</p>

            <div class="row">
                <!-- Danh sách vai trò -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách vai trò</h6>
                        </div>
                        <div class="card-body">
                            <?php if(!empty($error)): ?>
                                <div class="alert alert-danger"><?= $error ?></div>
                            <?php endif; ?>
                            
                            <?php if(!empty($success)): ?>
                                <div class="alert alert-success"><?= $success ?></div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên vai trò</th>
                                            <th>Mô tả</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($roles as $role): ?>
                                            <tr>
                                                <td><?= $role->id ?></td>
                                                <td><?= $role->role_name ?></td>
                                                <td><?= $role->description ?></td>
                                                <td>
                                                    <?php if($role->id > 3): // Không cho phép xóa vai trò mặc định ?>
                                                        <a href="<?= BASE_URL ?>admin/user/deleteRole/<?= $role->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa vai trò này?')">Xóa</a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Vai trò mặc định</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form thêm vai trò mới -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thêm vai trò mới</h6>
                        </div>
                        <div class="card-body">
                            <form method="post" action="<?= BASE_URL ?>admin/user/roles">
                                <div class="form-group">
                                    <label for="role_name">Tên vai trò <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="role_name" name="role_name" value="<?= htmlspecialchars($role_name) ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($description) ?></textarea>
                                </div>
                                
                                <button type="submit" name="add_role" class="btn btn-primary">Thêm vai trò</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>