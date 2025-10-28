<?php
/**
 * Admin Chatbot Conversations - UC3.47
 * Trang xem lịch sử hội thoại chatbot
 */
?>

<?php include_once __DIR__ . '/../../shared/admin/header.php'; ?>
<?php include_once __DIR__ . '/../../shared/admin/sidebar.php'; ?>

<div class="chatbot-admin-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-comments"></i>
            Lịch sử hội thoại
        </h1>
        <div class="page-actions">
            <a href="<?php echo BASE_URL; ?>admin/chatbot" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $total_conversations; ?></div>
                <div class="stat-label">Tổng hội thoại</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon faq">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $conversations->faq_count ?? 0; ?></div>
                <div class="stat-label">Từ FAQ</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon ai">
                <i class="fas fa-robot"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $conversations->ai_count ?? 0; ?></div>
                <div class="stat-label">Từ AI</div>
            </div>
        </div>
    </div>

    <!-- Conversations List -->
    <div class="conversations-container">
        <div class="card">
            <div class="card-header">
                <h5>Danh sách hội thoại</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($conversations)): ?>
                    <div class="conversation-list">
                        <?php foreach ($conversations as $conversation): ?>
                            <div class="conversation-item">
                                <div class="conversation-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="conversation-content">
                                    <div class="conversation-header">
                                        <div class="conversation-user">
                                            <?php if ($conversation->fullname): ?>
                                                <strong><?php echo htmlspecialchars($conversation->fullname); ?></strong>
                                                <span class="text-muted">(<?php echo htmlspecialchars($conversation->email); ?>)</span>
                                            <?php else: ?>
                                                <strong>Khách vãng lai</strong>
                                            <?php endif; ?>
                                        </div>
                                        <div class="conversation-time">
                                            <?php echo date('d/m/Y H:i:s', strtotime($conversation->created_at)); ?>
                                        </div>
                                    </div>
                                    <div class="conversation-message">
                                        <div class="user-message">
                                            <strong>Người dùng:</strong> <?php echo htmlspecialchars($conversation->user_message); ?>
                                        </div>
                                        <div class="bot-message">
                                            <strong>Bot:</strong> <?php echo htmlspecialchars(substr($conversation->bot_response, 0, 200)); ?>
                                            <?php if (strlen($conversation->bot_response) > 200): ?>
                                                <span class="text-muted">...</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="conversation-meta">
                                        <span class="badge <?php echo $conversation->is_from_faq ? 'bg-info' : 'bg-success'; ?>">
                                            <?php echo $conversation->is_from_faq ? 'FAQ' : 'AI'; ?>
                                        </span>
                                        <?php if ($conversation->response_time): ?>
                                            <span class="response-time">
                                                <i class="fas fa-clock"></i> <?php echo $conversation->response_time; ?>ms
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="conversation-actions">
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            onclick="viewConversation(<?php echo $conversation->conversation_id; ?>)"
                                            title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Conversations pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h4>Chưa có hội thoại nào</h4>
                        <p class="text-muted">Chưa có hội thoại nào được ghi nhận.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Conversation Detail Modal -->
<div class="modal fade" id="conversationDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết hội thoại</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="conversationDetailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

.stat-icon.faq {
    background: #cce5ff;
    color: #004085;
}

.stat-icon.ai {
    background: #d4edda;
    color: #155724;
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

.conversation-list {
    max-height: 600px;
    overflow-y: auto;
}

.conversation-item {
    display: flex;
    align-items: flex-start;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s;
}

.conversation-item:hover {
    background-color: #f8f9fa;
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
    flex-shrink: 0;
}

.conversation-content {
    flex: 1;
}

.conversation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.conversation-user {
    font-size: 14px;
}

.conversation-time {
    font-size: 12px;
    color: #666;
}

.conversation-message {
    margin-bottom: 10px;
}

.user-message, .bot-message {
    margin-bottom: 8px;
    padding: 8px 12px;
    border-radius: 8px;
}

.user-message {
    background: #e3f2fd;
    border-left: 3px solid #2196f3;
}

.bot-message {
    background: #f3e5f5;
    border-left: 3px solid #9c27b0;
}

.conversation-meta {
    display: flex;
    gap: 10px;
    align-items: center;
}

.response-time {
    font-size: 12px;
    color: #666;
}

.conversation-actions {
    margin-left: 15px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state i {
    font-size: 64px;
    color: #ddd;
    margin-bottom: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.page-title {
    margin: 0;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.page-actions {
    display: flex;
    gap: 10px;
}
</style>

<script>
// View conversation details
function viewConversation(conversationId) {
    fetch(`<?php echo BASE_URL; ?>ajax/chatbot_ajax.php?action=get_conversation_by_id&id=${conversationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const conversation = data.conversation;
                document.getElementById('conversationDetailContent').innerHTML = `
                    <div class="conversation-detail">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Người dùng:</label>
                            <p class="form-control-plaintext">${conversation.user_message}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bot trả lời:</label>
                            <div class="form-control-plaintext">${conversation.bot_response}</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Loại:</label>
                                <p class="form-control-plaintext">
                                    <span class="badge ${conversation.is_from_faq ? 'bg-info' : 'bg-success'}">
                                        ${conversation.is_from_faq ? 'FAQ' : 'AI'}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Thời gian phản hồi:</label>
                                <p class="form-control-plaintext">${conversation.response_time || 'N/A'}ms</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Thời gian:</label>
                                <p class="form-control-plaintext">${conversation.created_at}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Session ID:</label>
                                <p class="form-control-plaintext">${conversation.session_id}</p>
                            </div>
                        </div>
                        ${conversation.suggested_products ? `
                            <div class="mb-3">
                                <label class="form-label fw-bold">Sản phẩm gợi ý:</label>
                                <div class="form-control-plaintext">${conversation.suggested_products}</div>
                            </div>
                        ` : ''}
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('conversationDetailModal')).show();
            } else {
                alert('Không thể tải chi tiết hội thoại');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi kết nối');
        });
}
</script>

<?php include_once __DIR__ . '/../../shared/admin/footer.php'; ?>
