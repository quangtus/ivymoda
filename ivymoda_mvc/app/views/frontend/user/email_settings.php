<?php
$title = $data['title'] ?? 'Quản lý Email';
$user_info = $data['user_info'] ?? null;
$email_logs = $data['email_logs'] ?? [];
$error = $data['error'] ?? '';
$success = $data['success'] ?? '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .email-settings-card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 15px;
        }
        .email-log-item {
            border-left: 4px solid #007bff;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .email-log-item.failed {
            border-left-color: #dc3545;
        }
        .email-log-item.sent {
            border-left-color: #28a745;
        }
        .status-badge {
            font-size: 0.8em;
            padding: 4px 8px;
        }
        .settings-section {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <?php include ROOT_PATH . 'app/views/shared/frontend/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar -->
                <div class="card email-settings-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Tài khoản</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?php echo BASE_URL; ?>user/profile" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-circle"></i> Thông tin cá nhân
                        </a>
                        <a href="<?php echo BASE_URL; ?>user/emailSettings" class="list-group-item list-group-item-action active">
                            <i class="fas fa-envelope"></i> Quản lý Email
                        </a>
                        <a href="<?php echo BASE_URL; ?>user/orders" class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-bag"></i> Đơn hàng của tôi
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-9">
                <!-- Flash Messages -->
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Email Settings -->
                <div class="settings-section">
                    <h4><i class="fas fa-cog"></i> Cài đặt Email</h4>
                    <p class="text-muted">Quản lý các thông báo email bạn muốn nhận từ IVY Moda</p>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" 
                                           name="email_notifications" value="1" checked>
                                    <label class="form-check-label" for="email_notifications">
                                        <strong>Thông báo đơn hàng</strong>
                                        <small class="d-block text-muted">Nhận email xác nhận đơn hàng, cập nhật trạng thái giao hàng</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="promotion_emails" 
                                           name="promotion_emails" value="1" checked>
                                    <label class="form-check-label" for="promotion_emails">
                                        <strong>Email khuyến mãi</strong>
                                        <small class="d-block text-muted">Nhận thông tin về các chương trình khuyến mãi đặc biệt</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" name="update_email_settings" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu cài đặt
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Email History -->
                <div class="settings-section">
                    <h4><i class="fas fa-history"></i> Lịch sử Email</h4>
                    <p class="text-muted">Danh sách các email đã được gửi đến địa chỉ: <strong><?php echo $user_info->email; ?></strong></p>
                    
                    <?php if (empty($email_logs)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-envelope-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có email nào được gửi</p>
                        </div>
                    <?php else: ?>
                        <div class="email-logs">
                            <?php foreach ($email_logs as $log): ?>
                                <div class="email-log-item <?php echo $log['status']; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($log['subject']); ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> 
                                                <?php echo date('d/m/Y H:i', strtotime($log['sent_at'])); ?>
                                            </small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <span class="badge status-badge <?php echo $log['status'] == 'sent' ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $log['status'] == 'sent' ? 'Đã gửi' : 'Thất bại'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php if ($log['status'] == 'failed' && !empty($log['error_message'])): ?>
                                        <div class="mt-2">
                                            <small class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                <?php echo htmlspecialchars($log['error_message']); ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Email Information -->
                <div class="settings-section">
                    <h4><i class="fas fa-info-circle"></i> Thông tin Email</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-envelope"></i> Địa chỉ Email</h6>
                                    <p class="mb-0"><?php echo $user_info->email; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-shield-alt"></i> Bảo mật</h6>
                                    <p class="mb-0">Email được mã hóa và bảo mật</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include ROOT_PATH . 'app/views/shared/frontend/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
