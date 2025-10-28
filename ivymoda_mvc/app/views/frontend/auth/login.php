<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>
<section class ="user-login-section">
    <div class="login-container" style="max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h1 style="text-align: center; margin-bottom: 20px;">Đăng nhập tài khoản</h1>
        
        <?php if(!empty($error)): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 3px; margin-bottom: 15px;">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form action="<?= BASE_URL ?>auth/login" method="post" style="margin-bottom: 15px;">
            <div style="margin-bottom: 15px;">
                <label for="username" style="display: block; margin-bottom: 5px;">Tên đăng nhập</label>
                <input type="text" id="username" name="username" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="password" style="display: block; margin-bottom: 5px;">Mật khẩu</label>
                <input type="password" id="password" name="password" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
            </div>
            
            <button type="submit" style="background-color: #221f20; color: white; border: none; padding: 10px 15px; width: 100%; border-radius: 3px; cursor: pointer;">Đăng nhập</button>
        </form>
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="<?= BASE_URL ?>auth/forgotPassword" style="color: #007bff; text-decoration: none;">Quên mật khẩu?</a>
            <span style="margin: 0 5px;">|</span>
            <a href="<?= BASE_URL ?>auth/register" style="color: #007bff; text-decoration: none;">Đăng ký ngay</a>
        </div>
        
        <!-- Thêm link đăng nhập admin -->
        <div class="admin-login-link" style="text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
            <a href="<?= BASE_URL ?>admin/auth/login" style="color: #dc3545; text-decoration: none; font-weight: bold;">
                <i class="fas fa-lock" style="margin-right: 5px;"></i>
                Đăng nhập với tư cách quản trị viên
            </a>
        </div>
    </div>
</section>

<style>
.user-login-section{
    padding: 100px 0 0;
}
</style>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>