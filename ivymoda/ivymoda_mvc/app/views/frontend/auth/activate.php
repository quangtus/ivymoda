<?php
/**
 * View: Kích hoạt tài khoản
 * File: app/views/frontend/auth/activate.php
 */
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>assets/css/style.css" rel="stylesheet">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .auth-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-header h2 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        .auth-header p {
            color: #666;
            margin: 0;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 500;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Kích hoạt tài khoản</h2>
                <p>Xác nhận tài khoản của bạn</p>
            </div>
            
            <?php if(!empty($data['success'])): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $data['success']; ?>
                </div>
                <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>auth/login" class="btn btn-primary">
                        Đăng nhập ngay
                    </a>
                </div>
            <?php elseif(!empty($data['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $data['error']; ?>
                </div>
                <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>auth/register" class="btn btn-primary">
                        Đăng ký lại
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="<?php echo BASE_URL; ?>">
                    <i class="fas fa-arrow-left me-1"></i>
                    Về trang chủ
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>
