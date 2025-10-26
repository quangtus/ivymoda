<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\user\edit.php

// Load header
require_once ROOT_PATH . 'app/views/shared/admin/header.php';

// Load sidebar
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';

// Kiểm tra tồn tại của biến trước khi sử dụng
$username = isset($username) ? $username : '';
$fullname = isset($fullname) ? $fullname : '';
$email = isset($email) ? $email : '';
$phone = isset($phone) ? $phone : '';
$address = isset($address) ? $address : '';
$user = isset($user) ? $user : (object)['status' => 1, 'role_id' => 2];
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Quản lý người dùng</h1>
            <p class="mb-4">Cập nhật thông tin người dùng</p>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Thông tin cơ bản</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">Đặt lại mật khẩu</a>
            </li>
        </ul>

        <!-- Tab nội dung -->
        <div class="tab-content" id="myTabContent">
            <!-- Thông tin cơ bản -->
            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger mt-3">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($success)): ?>
                    <div class="alert alert-success mt-3">
                        <?= $success ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="" class="mt-3">
                    <div class="form-group row">
                        <label for="username" class="col-sm-2 col-form-label">Tên đăng nhập</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>" readonly>
                            <small class="text-muted">Tên đăng nhập không thể thay đổi</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="fullname" class="col-sm-2 col-form-label">Họ tên <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($fullname) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="phone" class="col-sm-2 col-form-label">Số điện thoại</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="address" class="col-sm-2 col-form-label">Địa chỉ</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="address" name="address" rows="3"><?= htmlspecialchars($address) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="role_id" class="col-sm-2 col-form-label">Vai trò</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="role_id" name="role_id">
                                <?php foreach($roles as $role): ?>
                                    <option value="<?= $role->id ?>" <?= ($user->role_id == $role->id) ? 'selected' : '' ?>><?= $role->role_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label">Trạng thái</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="status" name="status">
                                <option value="1" <?= ($user->status == 1) ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="0" <?= ($user->status == 0) ? 'selected' : '' ?>>Vô hiệu hóa</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" name="update_user" class="btn btn-primary">Cập nhật thông tin</button>
                            <a href="<?= BASE_URL ?>admin/user" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Đặt lại mật khẩu -->
            <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                <form method="post" action="" class="mt-3">
                    <div class="form-group row">
                        <label for="new_password" class="col-sm-2 col-form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" required>
                            <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <div class="col-sm-10 offset-sm-2">
                            <button type="submit" name="reset_password" class="btn btn-warning">Đặt lại mật khẩu</button>
                            <a href="<?= BASE_URL ?>admin/user" class="btn btn-secondary">Quay lại</a>
                        </div>
                    </div>
                </form>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý tab
    const tabs = document.querySelectorAll('.nav-link');
    const tabContents = document.querySelectorAll('.tab-pane');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Xóa active class
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => {
                c.classList.remove('show', 'active');
            });
            
            // Thêm active class
            this.classList.add('active');
            const target = this.getAttribute('href');
            document.querySelector(target).classList.add('show', 'active');
        });
    });
});
</script>