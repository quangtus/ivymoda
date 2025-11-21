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
        
        <form action="<?= BASE_URL ?>auth/resetPassword" method="post" onsubmit="return validateResetPasswordForm(this);">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div style="margin-bottom: 15px;">
                <label for="new_password" style="display: block; margin-bottom: 5px;">Mật khẩu mới <span style="color: red;">*</span></label>
                <input type="password" id="new_password" name="new_password" minlength="8" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
                <small style="color: #6c757d; font-size: 12px;">Mật khẩu phải có ít nhất 8 ký tự</small>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="confirm_password" style="display: block; margin-bottom: 5px;">Xác nhận mật khẩu <span style="color: red;">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="8" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
                <small id="password_match_error_reset" style="color: #dc3545; font-size: 12px; display: none;">Mật khẩu xác nhận không khớp</small>
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

<script>
function validateResetPasswordForm(form) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const passwordMatchError = document.getElementById('password_match_error_reset');
    
    // Validate password match
    if (newPassword !== confirmPassword) {
        passwordMatchError.style.display = 'block';
        document.getElementById('confirm_password').focus();
        return false;
    } else {
        passwordMatchError.style.display = 'none';
    }
    
    return true;
}

// Real-time password match validation
document.addEventListener('DOMContentLoaded', function() {
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordMatchError = document.getElementById('password_match_error_reset');
    
    if (newPassword && confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            if (this.value !== newPassword.value) {
                passwordMatchError.style.display = 'block';
            } else {
                passwordMatchError.style.display = 'none';
            }
        });
        
        newPassword.addEventListener('input', function() {
            if (confirmPassword.value !== '' && this.value !== confirmPassword.value) {
                passwordMatchError.style.display = 'block';
            } else {
                passwordMatchError.style.display = 'none';
            }
        });
    }
});
</script>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>