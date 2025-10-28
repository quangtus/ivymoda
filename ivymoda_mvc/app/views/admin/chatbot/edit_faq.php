<?php
/**
 * Admin Edit FAQ - UC3.48
 * Trang sửa FAQ cho chatbot
 */
?>

<?php include_once __DIR__ . '/../../shared/admin/header.php'; ?>
<?php include_once __DIR__ . '/../../shared/admin/sidebar.php'; ?>

<div class="chatbot-admin-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-edit"></i>
            Sửa FAQ
        </h1>
        <div class="page-actions">
            <a href="<?php echo BASE_URL; ?>admin/chatbot/faq" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Thông tin FAQ</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>admin/chatbot/editFaq/<?php echo $faq->faq_id; ?>">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="question" class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="question" name="question" 
                                   value="<?php echo htmlspecialchars($faq->question); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Chọn danh mục</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category); ?>" 
                                            <?php echo ($faq->category === $category) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="display_order" class="form-label">Thứ tự hiển thị</label>
                            <input type="number" class="form-control" id="display_order" name="display_order" 
                                   value="<?php echo $faq->display_order; ?>" min="0">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="answer" class="form-label">Câu trả lời <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="answer" name="answer" rows="8" required><?php echo htmlspecialchars($faq->answer); ?></textarea>
                    <div class="form-text">Bạn có thể sử dụng HTML để định dạng câu trả lời.</div>
                </div>

                <div class="mb-3">
                    <label for="help_link" class="form-label">Link hướng dẫn</label>
                    <input type="url" class="form-control" id="help_link" name="help_link" 
                           value="<?php echo htmlspecialchars($faq->help_link); ?>"
                           placeholder="https://example.com/help">
                    <div class="form-text">Link đến trang hướng dẫn chi tiết (tùy chọn).</div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" 
                               <?php echo $faq->status ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="status">
                            Kích hoạt FAQ này
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Cập nhật FAQ
                    </button>
                    <a href="<?php echo BASE_URL; ?>admin/chatbot/faq" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
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
// Auto-resize textarea
document.getElementById('answer').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const question = document.getElementById('question').value.trim();
    const answer = document.getElementById('answer').value.trim();
    const category = document.getElementById('category').value;

    if (!question || !answer || !category) {
        e.preventDefault();
        alert('Vui lòng điền đầy đủ thông tin bắt buộc.');
        return;
    }

    if (question.length < 10) {
        e.preventDefault();
        alert('Câu hỏi phải có ít nhất 10 ký tự.');
        return;
    }

    if (answer.length < 20) {
        e.preventDefault();
        alert('Câu trả lời phải có ít nhất 20 ký tự.');
        return;
    }
});
</script>

<?php include_once __DIR__ . '/../../shared/admin/footer.php'; ?>
