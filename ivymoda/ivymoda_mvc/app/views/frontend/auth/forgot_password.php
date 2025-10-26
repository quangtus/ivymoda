<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>
<section class ="user-forgot-password-section">
    <div class="forgot-password-container" style="max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h1 style="text-align: center; margin-bottom: 20px;">Quên mật khẩu</h1>
        
        <?php if(!empty($error)): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 3px; margin-bottom: 15px;">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
        <div class="alert alert-success" style="color: #155724; background-color: #d4edda; padding: 10px; border-radius: 3px; margin-bottom: 15px;">
            <?php echo $success; ?>
        </div>
        <?php endif; ?>
        
        <p style="margin-bottom: 20px;">Vui lòng nhập email đã đăng ký của bạn. Chúng tôi sẽ gửi một liên kết đặt lại mật khẩu đến email của bạn.</p>
        
        <form action="<?= BASE_URL ?>auth/forgotPassword" method="post">
            <div style="margin-bottom: 15px;">
                <label for="email" style="display: block; margin-bottom: 5px;">Email</label>
                <input type="email" id="email" name="email" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;" required>
            </div>
            
            <button type="submit" style="background-color: #221f20; color: white; border: none; padding: 10px 15px; width: 100%; border-radius: 3px; cursor: pointer;">Gửi yêu cầu</button>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="<?= BASE_URL ?>auth/login" style="color: #007bff; text-decoration: none;">Quay lại đăng nhập</a>
            </div>
        </form>
    </div>
</section>

<style>
.user-forgot-password-section{
    padding: 100px 0 0;
}
</style>
<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>