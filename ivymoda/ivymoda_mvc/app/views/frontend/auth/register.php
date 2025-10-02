<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<div class="register-container" style="max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
    <h1 style="text-align: center; margin-bottom: 20px;">Đăng ký tài khoản</h1>
    
    <?php if(!empty($error)): ?>
    <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 3px; margin-bottom: 15px;">
        <?php echo $error; ?>
    </div>
    <?php endif; ?>
    
    <?php if(!empty($success)): ?>
    <div class="alert alert-success" style="color: #155724; background-color: #d4edda; padding: 10px; border-radius: 3px; margin-bottom: 15px;">
        <?php echo $success; ?>
        <p><a href="<?= BASE_URL ?>auth/login" style="color: #007bff;">Đăng nhập ngay</a></p>
    </div>
    <?php else: ?>
    
    <form action="<?= BASE_URL ?>auth/register" method="post">
        <div style="margin-bottom: 15px;">
            <label for="username" style="display: block; margin-bottom: 5px;">Tên đăng nhập <span style="color: red;">*</span></label>
            <input type="text" id="username" name="username" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="password" style="display: block; margin-bottom: 5px;">Mật khẩu <span style="color: red;">*</span></label>
            <input type="password" id="password" name="password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
            <small style="color: #6c757d; font-size: 12px;">Mật khẩu phải có ít nhất 6 ký tự</small>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="confirm_password" style="display: block; margin-bottom: 5px;">Xác nhận mật khẩu <span style="color: red;">*</span></label>
            <input type="password" id="confirm_password" name="confirm_password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="email" style="display: block; margin-bottom: 5px;">Email <span style="color: red;">*</span></label>
            <input type="email" id="email" name="email" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="fullname" style="display: block; margin-bottom: 5px;">Họ tên <span style="color: red;">*</span></label>
            <input type="text" id="fullname" name="fullname" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="phone" style="display: block; margin-bottom: 5px;">Số điện thoại</label>
            <input type="tel" id="phone" name="phone" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="address" style="display: block; margin-bottom: 5px;">Địa chỉ</label>
            <textarea id="address" name="address" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" rows="3"></textarea>
        </div>
        
        <button type="submit" style="background-color: #221f20; color: white; border: none; padding: 10px 15px; width: 100%; border-radius: 3px; cursor: pointer;">Đăng ký</button>
        
        <div style="text-align: center; margin-top: 15px;">
            <p>Đã có tài khoản? <a href="<?= BASE_URL ?>auth/login" style="color: #007bff; text-decoration: none;">Đăng nhập ngay</a></p>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>