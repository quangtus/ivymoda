<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\user\add.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Quản lý người dùng</h1>
            <p class="mb-4">Thêm người dùng mới vào hệ thống</p>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thêm người dùng mới</h6>
                </div>
                <div class="card-body">
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($success)): ?>
                        <div class="alert alert-success">
                            <?= $success ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= BASE_URL ?>admin/user/add">
                        <div class="form-group row">
                            <label for="username" class="col-sm-2 col-form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username" name="username" value="<?= isset($username) ? $username : '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="password" class="col-sm-2 col-form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                                <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" value="<?= isset($email) ? $email : '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="fullname" class="col-sm-2 col-form-label">Họ tên <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="fullname" name="fullname" value="<?= isset($fullname) ? $fullname : '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="phone" class="col-sm-2 col-form-label">Số điện thoại</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone" name="phone" value="<?= isset($phone) ? $phone : '' ?>">
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="address" class="col-sm-2 col-form-label">Địa chỉ</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="address" name="address" rows="3"><?= isset($address) ? $address : '' ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="role_id" class="col-sm-2 col-form-label">Vai trò</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="role_id" name="role_id">
                                    <?php foreach($roles as $role): ?>
                                        <option value="<?= $role->id ?>"><?= $role->role_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                                <a href="<?= BASE_URL ?>admin/user" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>