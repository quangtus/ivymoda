<?php
/**
 * Admin Chatbot Dashboard - UC3.47 & UC3.48
 * Trang tổng quan chatbot
 */
?>

<?php include_once __DIR__ . '/../../shared/admin/header.php'; ?>
<?php include_once __DIR__ . '/../../shared/admin/sidebar.php'; ?>

<div class="chatbot-admin-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-robot"></i>
            Quản lý Chatbot
        </h1>
        <div class="page-actions">
            <a href="<?php echo BASE_URL; ?>admin/chatbot/faq" class="btn btn-primary">
                <i class="fas fa-question-circle"></i> Quản lý FAQ
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $faq_stats->total_faqs ?? 0; ?></div>
                <div class="stat-label">Tổng FAQ</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon active">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $faq_stats->active_faqs ?? 0; ?></div>
                <div class="stat-label">FAQ hoạt động</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon conversations">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $chatbot_stats->total_conversations ?? 0; ?></div>
                <div class="stat-label">Hội thoại hôm nay</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon response-time">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $chatbot_stats->avg_response_time ?? 0; ?>ms</div>
                <div class="stat-label">Thời gian phản hồi TB</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt"></i> Thao tác nhanh</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <a href="<?php echo BASE_URL; ?>admin/chatbot/faq" class="quick-action-btn">
                            <i class="fas fa-question-circle"></i>
                            <span>Quản lý FAQ</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo BASE_URL; ?>admin/chatbot/addFaq" class="quick-action-btn">
                            <i class="fas fa-plus"></i>
                            <span>Thêm FAQ mới</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="<?php echo BASE_URL; ?>admin/chatbot/conversations" class="quick-action-btn">
                            <i class="fas fa-history"></i>
                            <span>Lịch sử hội thoại</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Conversations -->
    <div class="recent-conversations">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-comments"></i> Hội thoại gần đây</h5>
                <a href="<?php echo BASE_URL; ?>admin/chatbot/conversations" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <div class="conversation-list">
                    <div class="conversation-item">
                        <div class="conversation-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="conversation-content">
                            <div class="conversation-message">Làm thế nào để đặt hàng?</div>
                            <div class="conversation-time">2 phút trước</div>
                        </div>
                        <div class="conversation-status">
                            <span class="badge bg-success">Đã trả lời</span>
                        </div>
                    </div>
                    <div class="conversation-item">
                        <div class="conversation-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="conversation-content">
                            <div class="conversation-message">Tôi muốn tìm áo sơ mi nam</div>
                            <div class="conversation-time">5 phút trước</div>
                        </div>
                        <div class="conversation-status">
                            <span class="badge bg-warning">Đang xử lý</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #6c757d;
    font-size: 20px;
}

.stat-icon.active {
    background: #d4edda;
    color: #155724;
}

.stat-icon.conversations {
    background: #cce5ff;
    color: #004085;
}

.stat-icon.response-time {
    background: #fff3cd;
    color: #856404;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.stat-label {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.quick-actions {
    margin-bottom: 30px;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    text-decoration: none;
    color: #333;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    border-color: #007bff;
    background: #f8f9fa;
    color: #007bff;
}

.quick-action-btn i {
    font-size: 24px;
    margin-bottom: 10px;
}

.quick-action-btn span {
    font-size: 14px;
    font-weight: 500;
}

.conversation-list {
    max-height: 400px;
    overflow-y: auto;
}

.conversation-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
}

.conversation-item:last-child {
    border-bottom: none;
}

.conversation-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: #6c757d;
}

.conversation-content {
    flex: 1;
}

.conversation-message {
    font-weight: 500;
    color: #333;
    margin-bottom: 5px;
}

.conversation-time {
    font-size: 12px;
    color: #666;
}

.conversation-status {
    margin-left: 15px;
}
</style>

<?php include_once __DIR__ . '/../../shared/admin/footer.php'; ?>
