<?php
/**
 * Chatbot Index View - UC3.48
 * Trang hiển thị chatbot widget (nếu cần trang riêng)
 */
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="page-title">Hỗ trợ khách hàng</h1>
            <p class="page-description">Chọn câu hỏi bạn muốn hỏi hoặc tìm kiếm thông tin cần thiết.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="faq-section">
                <h2>Câu hỏi thường gặp</h2>
                
                <?php if (!empty($faqs)): ?>
                    <div class="faq-list">
                        <?php foreach ($faqs as $faq): ?>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <h4><?php echo htmlspecialchars($faq->question); ?></h4>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p><?php echo nl2br(htmlspecialchars($faq->answer)); ?></p>
                                    <?php if ($faq->help_link): ?>
                                        <a href="<?php echo htmlspecialchars($faq->help_link); ?>" target="_blank" class="help-link">
                                            <i class="fas fa-external-link-alt"></i> Xem thêm chi tiết
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-faqs">
                        <i class="fas fa-question-circle"></i>
                        <p>Chưa có câu hỏi nào được thêm vào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="chatbot-info">
                <h3>Liên hệ hỗ trợ</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>Hotline: 0901234567</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>Email: support@ivymoda.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>Thời gian: 8:00 - 22:00</span>
                    </div>
                </div>
                
                <?php if (!empty($categories)): ?>
                    <h4>Danh mục câu hỏi</h4>
                    <div class="category-list">
                        <?php foreach ($categories as $category): ?>
                            <span class="category-tag"><?php echo htmlspecialchars($category->category); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.page-title {
    color: #333;
    margin-bottom: 10px;
}

.page-description {
    color: #666;
    margin-bottom: 30px;
}

.faq-section {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.faq-list {
    margin-top: 20px;
}

.faq-item {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-item:hover {
    border-color: #667eea;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.1);
}

.faq-question {
    padding: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8f9fa;
    transition: background 0.3s ease;
}

.faq-question:hover {
    background: #e9ecef;
}

.faq-question h4 {
    margin: 0;
    color: #333;
    font-size: 16px;
    flex: 1;
}

.faq-question i {
    color: #667eea;
    transition: transform 0.3s ease;
}

.faq-item.active .faq-question i {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 20px;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

.faq-item.active .faq-answer {
    padding: 20px;
    max-height: 200px;
}

.faq-answer p {
    margin: 0 0 15px 0;
    color: #555;
    line-height: 1.6;
}

.help-link {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
}

.help-link:hover {
    text-decoration: underline;
}

.no-faqs {
    text-align: center;
    padding: 40px;
    color: #666;
}

.no-faqs i {
    font-size: 48px;
    color: #ddd;
    margin-bottom: 15px;
}

.chatbot-info {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 20px;
}

.chatbot-info h3 {
    color: #333;
    margin-bottom: 20px;
}

.contact-info {
    margin-bottom: 30px;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    color: #555;
}

.contact-item i {
    width: 20px;
    color: #667eea;
    margin-right: 10px;
}

.category-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.category-tag {
    background: #e9ecef;
    color: #495057;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
}

@media (max-width: 768px) {
    .faq-section,
    .chatbot-info {
        padding: 20px;
    }
    
    .faq-question {
        padding: 15px;
    }
    
    .faq-item.active .faq-answer {
        padding: 15px;
    }
}
</style>

<script>
// Add FAQ toggle functionality for this page
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            // Close other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });
            
            // Toggle current item
            item.classList.toggle('active');
        });
    });
});
</script>
