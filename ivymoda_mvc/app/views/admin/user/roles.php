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
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($success)): ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên vai trò</th>
                                            <th>Mô tả</th>
                                            <th>Số người dùng</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($roles as $role): ?>
                                            <tr>
                                                <td><?= $role->id ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($role->role_name) ?></strong>
                                                    <?php if($role->id <= 3): ?>
                                                        <span class="badge badge-primary ml-2">Mặc định</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($role->description) ?></td>
                                                <td>
                                                    <span class="badge badge-info"><?= $role->user_count ?> người dùng</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= BASE_URL ?>admin/user/editRole/<?= $role->id ?>" 
                                                           class="btn btn-primary btn-sm">
                                                            <i class="fas fa-edit"></i> Sửa
                                                        </a>
                                                        <?php if($role->id > 3): // Không cho phép xóa vai trò mặc định ?>
                                                            <?php if($role->user_count > 0): ?>
                                                                <button type="button" class="btn btn-danger btn-sm" disabled title="Vai trò đang được sử dụng">
                                                                    <i class="fas fa-trash"></i> Xóa
                                                                </button>
                                                            <?php else: ?>
                                                                <a href="<?= BASE_URL ?>admin/user/deleteRole/<?= $role->id ?>" 
                                                                   class="btn btn-danger btn-sm" 
                                                                   onclick="return confirm('Bạn có chắc muốn xóa vai trò này?')">
                                                                    <i class="fas fa-trash"></i> Xóa
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php else: ?>
                                                            <button type="button" class="btn btn-danger btn-sm" disabled title="Không thể xóa vai trò mặc định">
                                                                <i class="fas fa-trash"></i> Xóa
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
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
                                    <input type="text" class="form-control" id="role_name" name="role_name" 
                                           value="<?= htmlspecialchars($role_name) ?>" 
                                           placeholder="Nhập tên vai trò..." 
                                           required>
                                    <small class="form-text text-muted">Tên vai trò phải là duy nhất trong hệ thống</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="description">Mô tả</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="3" placeholder="Mô tả chức năng của vai trò..."><?= htmlspecialchars($description) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" name="add_role" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Thêm vai trò
                                    </button>
                                    <button type="reset" class="btn btn-secondary ml-2">
                                        <i class="fas fa-undo"></i> Làm mới
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>