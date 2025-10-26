<?php
require_once ROOT_PATH . 'app/views/shared/frontend/header.php'; 
?>

<div class="container" style="padding: 50px 0;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="text-center">
                <!-- Icon thành công -->
                <div class="success-icon mb-4">
                    <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745;"></i>
                </div>
                
                <!-- Thông báo thành công -->
                <h2 class="success-title mb-3">Đặt hàng thành công!</h2>
                <p class="success-message mb-4">
                    Cảm ơn bạn đã đặt hàng tại IVY moda. Chúng tôi sẽ xử lý đơn hàng của bạn và liên hệ lại trong thời gian sớm nhất.
                </p>
                
                <!-- Thông tin đơn hàng -->
                <div class="order-info card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin đơn hàng</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Mã đơn hàng:</strong> #<?= date('YmdHis') ?></p>
                                <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i') ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Trạng thái:</strong> <span class="badge badge-warning">Đang xử lý</span></p>
                                <p><strong>Dự kiến giao:</strong> 2-3 ngày làm việc</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Hướng dẫn tiếp theo -->
                <div class="next-steps card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Bước tiếp theo</h5>
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <div class="step-icon mb-2">
                                    <i class="fas fa-phone" style="font-size: 2rem; color: #007bff;"></i>
                                </div>
                                <h6>Xác nhận đơn hàng</h6>
                                <p class="text-muted small">Chúng tôi sẽ gọi điện xác nhận đơn hàng trong vòng 30 phút</p>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <div class="step-icon mb-2">
                                    <i class="fas fa-truck" style="font-size: 2rem; color: #28a745;"></i>
                                </div>
                                <h6>Giao hàng</h6>
                                <p class="text-muted small">Đơn hàng sẽ được giao trong 2-3 ngày làm việc</p>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <div class="step-icon mb-2">
                                    <i class="fas fa-star" style="font-size: 2rem; color: #ffc107;"></i>
                                </div>
                                <h6>Đánh giá</h6>
                                <p class="text-muted small">Hãy đánh giá sản phẩm sau khi nhận hàng</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Nút hành động -->
                <div class="action-buttons">
                    <a href="<?= BASE_URL ?>" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                    <a href="<?= BASE_URL ?>product" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                    </a>
                </div>
                
                <!-- Liên hệ hỗ trợ -->
                <div class="support-info mt-5">
                    <h6>Cần hỗ trợ?</h6>
                    <p class="text-muted">
                        Nếu bạn có bất kỳ câu hỏi nào về đơn hàng, vui lòng liên hệ với chúng tôi:
                    </p>
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="contact-info">
                                <p><i class="fas fa-phone me-2"></i> Hotline: 1900 1234</p>
                                <p><i class="fas fa-envelope me-2"></i> Email: support@ivymoda.com</p>
                                <p><i class="fas fa-clock me-2"></i> Thời gian: 8:00 - 22:00 (T2-CN)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    animation: bounceIn 0.6s ease;
}

.success-title {
    color: #28a745;
    font-weight: 700;
}

.success-message {
    font-size: 1.1rem;
    color: #6c757d;
    line-height: 1.6;
}

.order-info {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
}

.next-steps {
    background-color: #fff;
}

.step-icon {
    transition: transform 0.3s ease;
}

.step-icon:hover {
    transform: scale(1.1);
}

.action-buttons .btn {
    min-width: 200px;
    margin-bottom: 1rem;
}

.support-info {
    background-color: #f8f9fa;
    padding: 2rem;
    border-radius: 0.5rem;
    margin-top: 2rem;
}

.contact-info p {
    margin-bottom: 0.5rem;
    color: #495057;
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .action-buttons .btn {
        min-width: auto;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .step-icon {
        margin-bottom: 1rem;
    }
    
    .support-info {
        padding: 1.5rem;
    }
}
</style>

<?php 
require_once ROOT_PATH . 'app/views/shared/frontend/footer.php'; 
?>
