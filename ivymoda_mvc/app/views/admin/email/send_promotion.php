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
                        <li class="breadcrumb-item active">Gửi Email Khuyến Mãi</li>
                    </ol>
                </div>
                <h4 class="page-title">Gửi Email Khuyến Mãi</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>admin/email/send-promotion" id="sendPromotionForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="promotion_id" class="form-label">Chọn Khuyến Mãi <span class="text-danger">*</span></label>
                                    <select class="form-select" id="promotion_id" name="promotion_id" required>
                                        <option value="">Chọn khuyến mãi...</option>
                                        <?php if (!empty($data['promotions'])): ?>
                                            <?php foreach ($data['promotions'] as $promotion): ?>
                                                <option value="<?= $promotion->promotion_id ?>" 
                                                        data-title="<?= htmlspecialchars($promotion->title) ?>"
                                                        data-content="<?= htmlspecialchars($promotion->content) ?>"
                                                        data-start="<?= date('d/m/Y', strtotime($promotion->start_date)) ?>"
                                                        data-end="<?= date('d/m/Y', strtotime($promotion->end_date)) ?>">
                                                    <?= htmlspecialchars($promotion->title) ?> 
                                                    (<?= date('d/m/Y', strtotime($promotion->start_date)) ?> - <?= date('d/m/Y', strtotime($promotion->end_date)) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="form-text">Chọn khuyến mãi để gửi email</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Đối Tượng Gửi</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="send_type" id="send_all" value="all" checked>
                                        <label class="form-check-label" for="send_all">
                                            Gửi cho tất cả khách hàng
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="send_type" id="send_custom" value="custom">
                                        <label class="form-check-label" for="send_custom">
                                            Gửi cho danh sách email tùy chỉnh
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="customRecipientsDiv" style="display: none;">
                            <label for="custom_recipients" class="form-label">Danh Sách Email</label>
                            <textarea class="form-control" id="custom_recipients" name="custom_recipients" rows="5" 
                                      placeholder="Nhập danh sách email, mỗi email một dòng..."></textarea>
                            <div class="form-text">Mỗi email trên một dòng, ví dụ: customer1@email.com</div>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Lưu ý:</strong> Email sẽ được gửi hàng loạt với tốc độ 100 email/phút để tránh bị coi là spam.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <a href="<?= BASE_URL ?>admin/email" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Quay lại
                                </a>
                                <button type="submit" class="btn btn-success" id="sendBtn">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Gửi Email
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview promotion -->
    <div class="row" id="previewRow" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Preview Email</h4>
                    <div id="promotionPreview" class="border p-3 bg-light">
                        <!-- Preview content will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê khách hàng -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Thống Kê Khách Hàng</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-primary" id="totalCustomers"><?= $data['customerStats']['total_customers'] ?? 0 ?></h3>
                                <p class="text-muted">Tổng khách hàng</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-success" id="customEmails">0</h3>
                                <p class="text-muted">Email tùy chỉnh</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h3 class="text-info" id="estimatedTime">-</h3>
                                <p class="text-muted">Thời gian gửi ước tính</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sendTypeRadios = document.querySelectorAll('input[name="send_type"]');
    const customRecipientsDiv = document.getElementById('customRecipientsDiv');
    const customRecipients = document.getElementById('custom_recipients');
    const promotionSelect = document.getElementById('promotion_id');
    const previewRow = document.getElementById('previewRow');
    const promotionPreview = document.getElementById('promotionPreview');
    const sendBtn = document.getElementById('sendBtn');
    const sendForm = document.getElementById('sendPromotionForm');

    // Toggle custom recipients
    sendTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'custom') {
                customRecipientsDiv.style.display = 'block';
                customRecipients.required = true;
            } else {
                customRecipientsDiv.style.display = 'none';
                customRecipients.required = false;
            }
            updateStats();
        });
    });

    // Show preview when promotion is selected
    promotionSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const title = selectedOption.dataset.title;
            const content = selectedOption.dataset.content;
            const start = selectedOption.dataset.start;
            const end = selectedOption.dataset.end;
            
            promotionPreview.innerHTML = `
                <div class="promotion-preview">
                    <h3 style="color: #e74c3c; text-align: center; margin-bottom: 20px;">🎉 ${title}</h3>
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; border: 2px solid #e74c3c;">
                        <h4>Chương trình khuyến mãi đặc biệt!</h4>
                        ${content}
                        <p><strong>Thời gian áp dụng:</strong> Từ ${start} đến ${end}</p>
                    </div>
                    <div style="text-align: center;">
                        <button style="background-color: #e74c3c; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-weight: bold;">MUA NGAY</button>
                    </div>
                </div>
            `;
            previewRow.style.display = 'block';
        } else {
            previewRow.style.display = 'none';
        }
    });

    // Update stats
    function updateStats() {
        const sendType = document.querySelector('input[name="send_type"]:checked').value;
        const totalCustomers = <?= $data['customerStats']['total_customers'] ?? 0 ?>;
        
        if (sendType === 'all') {
            document.getElementById('totalCustomers').textContent = totalCustomers;
            document.getElementById('customEmails').textContent = '0';
            
            if (totalCustomers > 0) {
                const estimatedMinutes = Math.ceil(totalCustomers / 100);
                document.getElementById('estimatedTime').textContent = `${estimatedMinutes} phút`;
            } else {
                document.getElementById('estimatedTime').textContent = '-';
            }
        } else {
            const emails = customRecipients.value.split('\n').filter(email => email.trim() !== '');
            const validEmails = emails.filter(email => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email.trim());
            });
            
            document.getElementById('totalCustomers').textContent = validEmails.length;
            document.getElementById('customEmails').textContent = validEmails.length;
            
            if (validEmails.length > 0) {
                const estimatedMinutes = Math.ceil(validEmails.length / 100);
                document.getElementById('estimatedTime').textContent = `${estimatedMinutes} phút`;
            } else {
                document.getElementById('estimatedTime').textContent = '-';
            }
        }
    }

    // Update stats on input change
    customRecipients.addEventListener('input', updateStats);

    // Form submission with confirmation
    sendForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const sendType = document.querySelector('input[name="send_type"]:checked').value;
        const promotionId = promotionSelect.value;
        
        if (!promotionId) {
            alert('Vui lòng chọn khuyến mãi');
            return;
        }
        
        if (sendType === 'custom') {
            const emails = customRecipients.value.split('\n').filter(email => email.trim() !== '');
            const validEmails = emails.filter(email => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email.trim());
            });
            
            if (validEmails.length === 0) {
                alert('Vui lòng nhập ít nhất một email hợp lệ');
                return;
            }
        }
        
        const confirmMessage = sendType === 'all' 
            ? 'Bạn có chắc muốn gửi email khuyến mãi cho tất cả khách hàng?'
            : `Bạn có chắc muốn gửi email khuyến mãi cho ${document.getElementById('customEmails').textContent} khách hàng?`;
        
        if (confirm(confirmMessage)) {
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';
            sendBtn.disabled = true;
            this.submit();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../shared/admin/footer.php'; ?>
