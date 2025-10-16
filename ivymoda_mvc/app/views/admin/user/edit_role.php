<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\user\edit_role.php

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
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa vai trò</h1>
                <a href="<?= BASE_URL ?>admin/user/roles" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin vai trò</h6>
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

                            <form method="post" action="<?= BASE_URL ?>admin/user/editRole/<?= $role->id ?>">
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
                                              rows="4" placeholder="Mô tả chức năng của vai trò..."><?= htmlspecialchars($description) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" name="update_role" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Cập nhật vai trò
                                    </button>
                                    <a href="<?= BASE_URL ?>admin/user/roles" class="btn btn-secondary ml-2">
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
                            <h6 class="m-0 font-weight-bold text-primary">Thông tin chi tiết</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>ID vai trò:</strong> <?= $role->id ?>
                            </div>
                            <div class="mb-3">
                                <strong>Trạng thái:</strong> 
                                <?php if($role->id <= 3): ?>
                                    <span class="badge badge-primary">Vai trò mặc định</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Vai trò tùy chỉnh</span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <strong>Lưu ý:</strong>
                                <ul class="mt-2">
                                    <li>Vai trò mặc định (ID ≤ 3) không thể xóa</li>
                                    <li>Tên vai trò phải là duy nhất</li>
                                    <li>Thay đổi sẽ ảnh hưởng đến tất cả người dùng có vai trò này</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>
