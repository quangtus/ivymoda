<?php
/**
 * Admin Chatbot Config - UC3.48
 * Trang cấu hình chatbot
 */
?>

<?php include_once __DIR__ . '/../../shared/admin/header.php'; ?>
<?php include_once __DIR__ . '/../../shared/admin/sidebar.php'; ?>

<div class="chatbot-admin-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-cog"></i>
            Cấu hình Chatbot
        </h1>
        <div class="page-actions">
            <a href="<?php echo BASE_URL; ?>admin/chatbot" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="<?php echo BASE_URL; ?>admin/chatbot/config">
                <!-- Chatbot Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Cài đặt Chatbot</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="welcome_message" class="form-label">Lời chào mặc định</label>
                                <textarea class="form-control" id="welcome_message" name="welcome_message" rows="3" 
                                          placeholder="Xin chào! Chọn câu hỏi bạn muốn hỏi:">Xin chào! Chọn câu hỏi bạn muốn hỏi:</textarea>
                                <div class="form-text">Lời chào hiển thị khi người dùng mở chatbot</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="enable_faq_mode" class="form-label">Chế độ FAQ</label>
                                <select class="form-select" id="enable_faq_mode" name="enable_faq_mode">
                                    <option value="1" selected>Bật</option>
                                    <option value="0">Tắt</option>
                                </select>
                                <div class="form-text">Bật/tắt chế độ hiển thị FAQ</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Vị trí hiển thị</label>
                                <select class="form-select" id="position" name="position">
                                    <option value="bottom-right" selected>Góc dưới bên phải</option>
                                    <option value="bottom-left">Góc dưới bên trái</option>
                                </select>
                                <div class="form-text">Vị trí hiển thị chatbot trên trang</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Cài đặt FAQ</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_faqs_display" class="form-label">Số FAQ hiển thị tối đa</label>
                                <input type="number" class="form-control" id="max_faqs_display" name="max_faqs_display" 
                                       value="10" min="1" max="50">
                                <div class="form-text">Số lượng FAQ hiển thị tối đa mỗi lần (1-50)</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="search_min_length" class="form-label">Độ dài tối thiểu tìm kiếm</label>
                                <input type="number" class="form-control" id="search_min_length" name="search_min_length" 
                                       value="2" min="1" max="10">
                                <div class="form-text">Số ký tự tối thiểu để bắt đầu tìm kiếm</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Cài đặt nâng cao</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="auto_open_delay" class="form-label">Tự động mở (giây)</label>
                                <input type="number" class="form-control" id="auto_open_delay" name="auto_open_delay" 
                                       value="0" min="0" max="60">
                                <div class="form-text">Tự động mở chatbot sau X giây (0 = không tự động)</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="show_categories" class="form-label">Hiển thị danh mục</label>
                                <select class="form-select" id="show_categories" name="show_categories">
                                    <option value="1" selected>Có</option>
                                    <option value="0">Không</option>
                                </select>
                                <div class="form-text">Hiển thị danh mục FAQ trong chatbot</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="enable_search" class="form-label">Bật tìm kiếm</label>
                                <select class="form-select" id="enable_search" name="enable_search">
                                    <option value="1" selected>Có</option>
                                    <option value="0">Không</option>
                                </select>
                                <div class="form-text">Cho phép tìm kiếm FAQ</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="widget_theme" class="form-label">Giao diện</label>
                                <select class="form-select" id="widget_theme" name="widget_theme">
                                    <option value="light" selected>Sáng</option>
                                    <option value="dark">Tối</option>
                                    <option value="auto">Tự động</option>
                                </select>
                                <div class="form-text">Giao diện chatbot</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu cấu hình
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetToDefault()">
                        <i class="fas fa-undo"></i> Đặt lại mặc định
                    </button>
                </div>
            </form>
        </div>

        <div class="col-md-4">
            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Thống kê</h5>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h4><?php echo $stats->total_faqs ?? 0; ?></h4>
                                <p>Tổng FAQ</p>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon active">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h4><?php echo $stats->active_faqs ?? 0; ?></h4>
                                <p>Đang hoạt động</p>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="stat-content">
                                <h4><?php echo $stats->total_categories ?? 0; ?></h4>
                                <p>Danh mục</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Preview</h5>
                </div>
                <div class="card-body">
                    <div class="chatbot-preview">
                        <div class="preview-header">
                            <h6>Hỗ trợ khách hàng</h6>
                            <button class="btn-close"></button>
                        </div>
                        <div class="preview-body">
                            <div class="preview-welcome">
                                <h6>Xin chào! 👋</h6>
                                <p>Chọn câu hỏi bạn muốn hỏi:</p>
                            </div>
                            <div class="preview-search">
                                <input type="text" placeholder="Tìm kiếm câu hỏi..." disabled>
                            </div>
                            <div class="preview-faq">
                                <div class="preview-faq-item">
                                    <div class="preview-question">
                                        Làm thế nào để đăng ký tài khoản?
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                                <div class="preview-faq-item">
                                    <div class="preview-question">
                                        Tôi quên mật khẩu, phải làm sao?
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help -->
            <div class="card">
                <div class="card-header">
                    <h5>Hướng dẫn</h5>
                </div>
                <div class="card-body">
                    <div class="help-content">
                        <h6>Cấu hình chatbot:</h6>
                        <ul>
                            <li>Lời chào nên thân thiện và hướng dẫn rõ ràng</li>
                            <li>Vị trí góc dưới bên phải phù hợp với hầu hết website</li>
                            <li>Không nên tự động mở quá sớm (gây khó chịu)</li>
                        </ul>

                        <h6>FAQ Settings:</h6>
                        <ul>
                            <li>Hiển thị 10-15 FAQ là tối ưu</li>
                            <li>Bật tìm kiếm giúp người dùng dễ tìm thông tin</li>
                            <li>Danh mục giúp phân loại câu hỏi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    border-top: 1px solid #e0e0e0;
}

.stats-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9ecef;
    color: #6c757d;
}

.stat-icon.active {
    background: #d4edda;
    color: #155724;
}

.stat-content h4 {
    margin: 0;
    font-size: 20px;
    font-weight: bold;
    color: #333;
}

.stat-content p {
    margin: 0;
    color: #666;
    font-size: 12px;
}

.chatbot-preview {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    background: white;
}

.preview-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preview-header h6 {
    margin: 0;
    font-size: 14px;
}

.btn-close {
    background: none;
    border: none;
    color: white;
    font-size: 12px;
    padding: 2px;
}

.preview-body {
    padding: 15px;
    background: #f8f9fa;
}

.preview-welcome {
    text-align: center;
    margin-bottom: 15px;
}

.preview-welcome h6 {
    margin: 0 0 5px 0;
    color: #333;
    font-size: 14px;
}

.preview-welcome p {
    margin: 0;
    color: #666;
    font-size: 12px;
}

.preview-search input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 15px;
    font-size: 12px;
    background: white;
}

.preview-faq {
    margin-top: 15px;
}

.preview-faq-item {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    margin-bottom: 8px;
    overflow: hidden;
}

.preview-question {
    padding: 10px 12px;
    font-size: 12px;
    color: #333;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.preview-question i {
    color: #667eea;
    font-size: 10px;
}

.help-content h6 {
    color: #333;
    margin-top: 20px;
    margin-bottom: 10px;
    font-size: 14px;
}

.help-content h6:first-child {
    margin-top: 0;
}

.help-content ul {
    margin-bottom: 20px;
    padding-left: 20px;
}

.help-content li {
    margin-bottom: 5px;
    color: #666;
    font-size: 13px;
}

.form-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}
</style>

<script>
// Reset to default values
function resetToDefault() {
    if (confirm('Bạn có chắc chắn muốn đặt lại tất cả cấu hình về mặc định?')) {
        document.getElementById('welcome_message').value = 'Xin chào! Chọn câu hỏi bạn muốn hỏi:';
        document.getElementById('enable_faq_mode').value = '1';
        document.getElementById('position').value = 'bottom-right';
        document.getElementById('max_faqs_display').value = '10';
        document.getElementById('search_min_length').value = '2';
        document.getElementById('auto_open_delay').value = '0';
        document.getElementById('show_categories').value = '1';
        document.getElementById('enable_search').value = '1';
        document.getElementById('widget_theme').value = 'light';
    }
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const maxFaqs = parseInt(document.getElementById('max_faqs_display').value);
    const searchMinLength = parseInt(document.getElementById('search_min_length').value);
    const autoOpenDelay = parseInt(document.getElementById('auto_open_delay').value);

    if (maxFaqs < 1 || maxFaqs > 50) {
        e.preventDefault();
        alert('Số FAQ hiển thị tối đa phải từ 1 đến 50');
        return;
    }

    if (searchMinLength < 1 || searchMinLength > 10) {
        e.preventDefault();
        alert('Độ dài tối thiểu tìm kiếm phải từ 1 đến 10');
        return;
    }

    if (autoOpenDelay < 0 || autoOpenDelay > 60) {
        e.preventDefault();
        alert('Thời gian tự động mở phải từ 0 đến 60 giây');
        return;
    }
});
</script>

<?php include_once __DIR__ . '/../../shared/admin/footer.php'; ?>
