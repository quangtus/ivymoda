<?php require_once __DIR__ . '/../../shared/admin/header.php'; ?>

<?php require_once __DIR__ . '/../../shared/admin/sidebar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/dashboard">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin/email">Quản lý Email</a></li>
                        <li class="breadcrumb-item active">Cấu hình SMTP</li>
                    </ol>
                </div>
                <h4 class="page-title">Cấu hình SMTP</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>admin/email/smtp-config">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_host" class="form-label">SMTP Host <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                           value="<?= htmlspecialchars($data['config']['smtp_host']) ?>" 
                                           placeholder="smtp.gmail.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_port" class="form-label">SMTP Port <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                           value="<?= htmlspecialchars($data['config']['smtp_port']) ?>" 
                                           placeholder="587" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                           value="<?= htmlspecialchars($data['config']['smtp_username']) ?>" 
                                           placeholder="your-email@gmail.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                           value="<?= htmlspecialchars($data['config']['smtp_password']) ?>" 
                                           placeholder="App Password" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="smtp_secure" class="form-label">Bảo mật</label>
                                    <select class="form-select" id="smtp_secure" name="smtp_secure">
                                        <option value="tls" <?= $data['config']['smtp_secure'] == 'tls' ? 'selected' : '' ?>>TLS</option>
                                        <option value="ssl" <?= $data['config']['smtp_secure'] == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                        <option value="" <?= empty($data['config']['smtp_secure']) ? 'selected' : '' ?>>Không</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_email" class="form-label">Email Gửi <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="from_email" name="from_email" 
                                           value="<?= htmlspecialchars($data['config']['from_email']) ?>" 
                                           placeholder="noreply@ivymoda.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="from_name" class="form-label">Tên Người Gửi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="from_name" name="from_name" 
                                   value="<?= htmlspecialchars($data['config']['from_name']) ?>" 
                                   placeholder="IVY Moda" required>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <a href="<?= BASE_URL ?>admin/email" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Lưu Cấu Hình
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hướng dẫn cấu hình -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Hướng Dẫn Cấu Hình SMTP</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Gmail:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Host:</strong> smtp.gmail.com</li>
                                <li><strong>Port:</strong> 587 (TLS) hoặc 465 (SSL)</li>
                                <li><strong>Username:</strong> your-email@gmail.com</li>
                                <li><strong>Password:</strong> App Password (không phải mật khẩu thường)</li>
                                <li><strong>Bảo mật:</strong> TLS hoặc SSL</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Outlook/Hotmail:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Host:</strong> smtp-mail.outlook.com</li>
                                <li><strong>Port:</strong> 587</li>
                                <li><strong>Username:</strong> your-email@outlook.com</li>
                                <li><strong>Password:</strong> Mật khẩu tài khoản</li>
                                <li><strong>Bảo mật:</strong> TLS</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3">
                        <h6><i class="fas fa-info-circle me-2"></i>Lưu ý quan trọng:</h6>
                        <ul class="mb-0">
                            <li>Với Gmail, bạn cần bật 2FA và tạo App Password</li>
                            <li>Với Outlook, có thể cần bật "Less secure app access"</li>
                            <li>Test email sau khi cấu hình để đảm bảo hoạt động</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../shared/admin/footer.php'; ?>