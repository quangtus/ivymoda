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
                            <label for="category" class="form-label">
                                Danh mục <span class="text-danger">*</span>
                                <button type="button" class="btn btn-sm btn-link p-0 ms-2" id="toggleCategoryMode">
                                    <i class="fas fa-exchange-alt"></i> Đổi chế độ
                                </button>
                            </label>
                            
                            <?php
                            // Check nếu category hiện tại có trong danh sách
                            $categoryExists = false;
                            if (!empty($categories)) {
                                foreach ($categories as $cat) {
                                    if ($cat->category === $faq->category) {
                                        $categoryExists = true;
                                        break;
                                    }
                                }
                            }
                            ?>
                            
                            <!-- MODE 1: Chọn từ danh sách có sẵn -->
                            <div id="selectMode" style="display: <?php echo $categoryExists ? 'block' : 'none'; ?>;">
                                <select class="form-control" id="categorySelect" name="category_select">
                                    <option value="">-- Chọn danh mục có sẵn --</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo htmlspecialchars($cat->category); ?>"
                                                    <?php echo ($cat->category === $faq->category) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat->category); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Chọn từ <strong><?php echo count($categories); ?> danh mục</strong> có sẵn
                                </div>
                            </div>
                            
                            <!-- MODE 2: Tạo danh mục mới hoặc giữ nguyên -->
                            <div id="inputMode" style="display: <?php echo !$categoryExists ? 'block' : 'none'; ?>;">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-edit"></i></span>
                                    <input type="text" class="form-control" id="categoryInput" 
                                           name="category_input" 
                                           value="<?php echo htmlspecialchars($faq->category); ?>"
                                           placeholder="Nhập tên danh mục...">
                                </div>
                                <div class="form-text text-success">
                                    <i class="fas fa-lightbulb"></i> Sửa hoặc tạo danh mục mới
                                </div>
                            </div>
                            
                            <!-- Hidden field chứa giá trị cuối cùng -->
                            <input type="hidden" id="category" name="category" 
                                   value="<?php echo htmlspecialchars($faq->category); ?>" required>
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
// ============================================
// CATEGORY MODE TOGGLE & VALIDATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    const selectMode = document.getElementById('selectMode');
    const inputMode = document.getElementById('inputMode');
    const categorySelect = document.getElementById('categorySelect');
    const categoryInput = document.getElementById('categoryInput');
    const categoryHidden = document.getElementById('category');
    const toggleBtn = document.getElementById('toggleCategoryMode');
    const form = document.querySelector('form');
    
    // Chuyển đổi giữa 2 mode
    toggleBtn.addEventListener('click', function() {
        if (selectMode.style.display === 'none') {
            // Chuyển sang mode SELECT
            selectMode.style.display = 'block';
            inputMode.style.display = 'none';
            categoryInput.value = '';
            categoryHidden.value = categorySelect.value;
        } else {
            // Chuyển sang mode INPUT
            selectMode.style.display = 'none';
            inputMode.style.display = 'block';
            categorySelect.value = '';
            categoryHidden.value = categoryInput.value;
            categoryInput.focus();
        }
    });
    
    // Cập nhật hidden field khi chọn từ dropdown
    categorySelect.addEventListener('change', function() {
        categoryHidden.value = this.value;
    });
    
    // Cập nhật hidden field khi nhập tay
    categoryInput.addEventListener('input', function() {
        categoryHidden.value = this.value;
    });
    
    // Auto-resize textarea cho answer
    const answerTextarea = document.getElementById('answer');
    answerTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        const question = document.getElementById('question').value.trim();
        const answer = answerTextarea.value.trim();
        const categoryValue = categoryHidden.value.trim();

        if (!question || !answer || !categoryValue) {
            e.preventDefault();
            alert('⚠️ Vui lòng điền đầy đủ thông tin bắt buộc.');
            return false;
        }

        if (question.length < 10) {
            e.preventDefault();
            alert('⚠️ Câu hỏi phải có ít nhất 10 ký tự.');
            document.getElementById('question').focus();
            return false;
        }

        if (answer.length < 20) {
            e.preventDefault();
            alert('⚠️ Câu trả lời phải có ít nhất 20 ký tự.');
            answerTextarea.focus();
            return false;
        }
        
        if (categoryValue.length > 100) {
            e.preventDefault();
            alert('⚠️ Tên danh mục không được quá 100 ký tự!');
            categoryInput.focus();
            return false;
        }

        return true;
    });
    
    console.log('✅ FAQ Edit Manager initialized');
});
</script>

<?php include_once __DIR__ . '/../../shared/admin/footer.php'; ?>
