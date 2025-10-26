<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<section class ="user-reset-password-section">
    <div class="reset-password-container" style="max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h1 style="text-align: center; margin-bottom: 20px;">Đặt lại mật khẩu</h1>
        
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
        <?php elseif($validToken): ?>
        
        <form action="<?= BASE_URL ?>auth/resetPassword" method="post">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div style="margin-bottom: 15px;">
                <label for="new_password" style="display: block; margin-bottom: 5px;">Mật khẩu mới <span style="color: red;">*</span></label>
                <input type="password" id="new_password" name="new_password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
                <small style="color: #6c757d; font-size: 12px;">Mật khẩu phải có ít nhất 6 ký tự</small>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="confirm_password" style="display: block; margin-bottom: 5px;">Xác nhận mật khẩu <span style="color: red;">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
            </div>
            
            <button type="submit" style="background-color: #221f20; color: white; border: none; padding: 10px 15px; width: 100%; border-radius: 3px; cursor: pointer;">Đặt lại mật khẩu</button>
        </form>
        <?php else: ?>
        <div class="alert alert-warning" style="color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 3px; margin-bottom: 15px;">
            Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.
        </div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="<?= BASE_URL ?>auth/forgotPassword" style="color: #007bff; text-decoration: none;">Yêu cầu liên kết mới</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.user-reset-password-section{
    padding: 100px 0 0;
}
</style>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>