<?php
/**
 * Admin Add FAQ - UC3.48
 * Trang thêm FAQ mới
 */
?>

<?php include_once __DIR__ . '/../../shared/admin/header.php'; ?>
<?php include_once __DIR__ . '/../../shared/admin/sidebar.php'; ?>

<div class="chatbot-admin-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-plus"></i>
            Thêm FAQ mới
        </h1>
        <div class="page-actions">
            <a href="<?php echo BASE_URL; ?>admin/chatbot" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="form-container">
        <div class="config-title">Thông tin FAQ</div>
                    <form method="POST" action="<?php echo BASE_URL; ?>admin/chatbot/add">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="question" class="form-label">
                                    Câu hỏi <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="question" name="question" 
                                       value="<?php echo htmlspecialchars($_POST['question'] ?? ''); ?>" 
                                       placeholder="Nhập câu hỏi thường gặp..." required>
                                <div class="form-text">Câu hỏi sẽ hiển thị cho người dùng trong chatbot</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="answer" class="form-label">
                                    Câu trả lời <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="answer" name="answer" rows="6" 
                                          placeholder="Nhập câu trả lời chi tiết..." required><?php echo htmlspecialchars($_POST['answer'] ?? ''); ?></textarea>
                                <div class="form-text">Câu trả lời có thể chứa HTML để hiển thị link và format</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">
                                    Danh mục <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Chọn danh mục</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category->category); ?>" 
                                                <?php echo (($_POST['category'] ?? '') === $category->category) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category->category); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Phân loại FAQ để dễ quản lý</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="display_order" class="form-label">Thứ tự hiển thị</label>
                                <input type="number" class="form-control" id="display_order" name="display_order" 
                                       value="<?php echo htmlspecialchars($_POST['display_order'] ?? '0'); ?>" 
                                       min="0" placeholder="0">
                                <div class="form-text">Số càng nhỏ càng hiển thị trước (0 = đầu tiên)</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="help_link" class="form-label">Link hướng dẫn chi tiết</label>
                                <input type="url" class="form-control" id="help_link" name="help_link" 
                                       value="<?php echo htmlspecialchars($_POST['help_link'] ?? ''); ?>" 
                                       placeholder="https://example.com/help">
                                <div class="form-text">Link dẫn đến trang hướng dẫn chi tiết (tùy chọn)</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1" <?php echo (($_POST['status'] ?? '1') === '1') ? 'selected' : ''; ?>>Hoạt động</option>
                                    <option value="0" <?php echo (($_POST['status'] ?? '1') === '0') ? 'selected' : ''; ?>>Không hoạt động</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu FAQ
                            </button>
                            <a href="<?php echo BASE_URL; ?>admin/chatbot" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Hướng dẫn</h5>
                </div>
                <div class="card-body">
                    <div class="help-content">
                        <h6>Viết câu hỏi hiệu quả:</h6>
                        <ul>
                            <li>Sử dụng ngôn ngữ đơn giản, dễ hiểu</li>
                            <li>Đặt câu hỏi từ góc độ khách hàng</li>
                            <li>Tránh thuật ngữ kỹ thuật phức tạp</li>
                        </ul>

                        <h6>Viết câu trả lời tốt:</h6>
                        <ul>
                            <li>Trả lời đầy đủ, chi tiết</li>
                            <li>Sử dụng HTML để format (link, bold, italic)</li>
                            <li>Đưa ra các bước cụ thể nếu cần</li>
                        </ul>

                        <h6>Danh mục gợi ý:</h6>
                        <ul>
                            <li>Đăng ký & Đăng nhập</li>
                            <li>Đặt hàng</li>
                            <li>Thanh toán</li>
                            <li>Đơn hàng</li>
                            <li>Khuyến mãi</li>
                            <li>Chính sách</li>
                            <li>Sản phẩm</li>
                            <li>Hỗ trợ</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Preview</h5>
                </div>
                <div class="card-body">
                    <div class="faq-preview">
                        <div class="preview-question">
                            <strong>Q:</strong> <span id="previewQuestion">Câu hỏi sẽ hiển thị ở đây...</span>
                        </div>
                        <div class="preview-answer">
                            <strong>A:</strong> <span id="previewAnswer">Câu trả lời sẽ hiển thị ở đây...</span>
                        </div>
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

.help-content h6 {
    color: #333;
    margin-top: 20px;
    margin-bottom: 10px;
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
}

.faq-preview {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

.preview-question,
.preview-answer {
    margin-bottom: 10px;
    line-height: 1.5;
}

.preview-question strong,
.preview-answer strong {
    color: #333;
}

#previewQuestion,
#previewAnswer {
    color: #666;
}

.text-danger {
    color: #dc3545 !important;
}

.form-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}
</style>

<script>
// Real-time preview
document.addEventListener('DOMContentLoaded', function() {
    const questionInput = document.getElementById('question');
    const answerInput = document.getElementById('answer');
    const previewQuestion = document.getElementById('previewQuestion');
    const previewAnswer = document.getElementById('previewAnswer');

    function updatePreview() {
        previewQuestion.textContent = questionInput.value || 'Câu hỏi sẽ hiển thị ở đây...';
        previewAnswer.innerHTML = answerInput.value || 'Câu trả lời sẽ hiển thị ở đây...';
    }

    questionInput.addEventListener('input', updatePreview);
    answerInput.addEventListener('input', updatePreview);

    // Initialize preview
    updatePreview();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const question = document.getElementById('question').value.trim();
    const answer = document.getElementById('answer').value.trim();
    const category = document.getElementById('category').value;

    if (!question || !answer || !category) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ thông tin bắt buộc');
        return;
    }

    if (question.length < 10) {
        e.preventDefault();
        alert('Câu hỏi phải có ít nhất 10 ký tự');
        return;
    }

    if (answer.length < 20) {
        e.preventDefault();
        alert('Câu trả lời phải có ít nhất 20 ký tự');
        return;
    }
});
</script>

<?php include_once __DIR__ . '/../../shared/admin/footer.php'; ?>
