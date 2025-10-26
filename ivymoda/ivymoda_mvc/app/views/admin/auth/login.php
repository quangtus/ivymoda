<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Đăng nhập Admin - IVY moda' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/54f0cb7e4a.js" crossorigin="anonymous"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        
        .admin-login-header {
            background: #2c3e50;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .admin-login-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .admin-login-header .subtitle {
            margin-top: 5px;
            opacity: 0.8;
            font-size: 14px;
        }
        
        .admin-login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-admin-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        
        .btn-admin-login:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-to-site {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e1e8ed;
        }
        
        .back-to-site a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-to-site a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-header">
            <i class="fas fa-shield-alt fa-2x mb-2"></i>
            <h1>IVY moda Admin</h1>
            <div class="subtitle">Khu vực quản trị hệ thống</div>
        </div>
        
        <div class="admin-login-body">
            <?php if(!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form action="<?= ADMIN_URL ?>auth/login" method="post">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user me-2"></i>Tên đăng nhập
                    </label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Nhập tên đăng nhập" required>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock me-2"></i>Mật khẩu
                    </label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>
                
                <button type="submit" class="btn-admin-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                </button>
            </form>
            
            <div class="back-to-site">
                <a href="<?= BASE_URL ?>">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
</body>
</html>