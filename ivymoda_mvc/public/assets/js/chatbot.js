/**
 * Chatbot Widget - UC3.48
 * Chatbot hướng dẫn sử dụng hệ thống (FAQ)
 * 
 * Features:
 * - Hiển thị danh sách FAQ
 * - Tìm kiếm FAQ
 * - Phân loại theo category
 * - Responsive design
 * - Auto-open option
 */

class ChatbotWidget {
    constructor(options = {}) {
        this.options = {
            // Default settings
            position: 'bottom-right',
            autoOpen: false,
            autoOpenDelay: 0,
            maxFaqs: 10,
            searchMinLength: 2,
            showCategories: true,
            enableSearch: true,
            theme: 'light',
            welcomeMessage: 'Xin chào! Chọn câu hỏi bạn muốn hỏi:',
            baseUrl: window.location.origin + '/ivymoda/ivymoda_mvc/public/',
            ...options
        };
        
        this.isOpen = false;
        this.faqs = [];
        this.categories = [];
        this.currentCategory = null;
        this.searchKeyword = '';
        
        this.init();
    }
    
    init() {
        this.createWidget();
        this.loadFaqs();
        this.loadCategories();
        this.bindEvents();
        
        // Auto open if enabled
        if (this.options.autoOpen && this.options.autoOpenDelay > 0) {
            setTimeout(() => {
                this.open();
            }, this.options.autoOpenDelay * 1000);
        }
    }
    
    createWidget() {
        // Create widget HTML
        const widgetHTML = `
            <div id="chatbot-widget" class="chatbot-widget ${this.options.theme} ${this.options.position}">
                <!-- Toggle Button -->
                <div class="chatbot-toggle" onclick="chatbot.toggle()">
                    <i class="fas fa-robot"></i>
                    <span class="chatbot-badge" id="chatbot-badge">0</span>
                </div>
                
                <!-- Chatbot Panel -->
                <div class="chatbot-panel" id="chatbot-panel">
                    <div class="chatbot-header">
                        <h6>Hỗ trợ khách hàng</h6>
                        <button class="chatbot-close" onclick="chatbot.close()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="chatbot-body">
                        <!-- Welcome Message -->
                        <div class="chatbot-welcome" id="chatbot-welcome">
                            <h6>${this.options.welcomeMessage}</h6>
                        </div>
                        
                        <!-- Search -->
                        ${this.options.enableSearch ? `
                            <div class="chatbot-search">
                                <input type="text" id="chatbot-search-input" placeholder="Tìm kiếm câu hỏi...">
                                <button id="chatbot-search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        ` : ''}
                        
                        <!-- Categories -->
                        ${this.options.showCategories ? `
                            <div class="chatbot-categories" id="chatbot-categories">
                                <button class="category-btn active" data-category="">Tất cả</button>
                            </div>
                        ` : ''}
                        
                        <!-- FAQ List -->
                        <div class="chatbot-faqs" id="chatbot-faqs">
                            <div class="chatbot-loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>Đang tải...</span>
                            </div>
                        </div>
                        
                        <!-- FAQ Detail -->
                        <div class="chatbot-faq-detail" id="chatbot-faq-detail" style="display: none;">
                            <div class="faq-detail-header">
                                <button class="back-btn" onclick="chatbot.showFaqList()">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </button>
                            </div>
                            <div class="faq-detail-content" id="faq-detail-content">
                                <!-- FAQ content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add to body
        document.body.insertAdjacentHTML('beforeend', widgetHTML);
        
        // Add CSS
        this.addStyles();
    }
    
    addStyles() {
        const styles = `
            <style>
            .chatbot-widget {
                position: fixed;
                z-index: 9999;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            
            .chatbot-widget.bottom-right {
                bottom: 20px;
                right: 20px;
            }
            
            .chatbot-widget.bottom-left {
                bottom: 20px;
                left: 20px;
            }
            
            .chatbot-toggle {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
                transition: all 0.3s ease;
                position: relative;
            }
            
            .chatbot-toggle:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
            }
            
            .chatbot-toggle i {
                color: white;
                font-size: 24px;
            }
            
            .chatbot-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                background: #ff4757;
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: bold;
            }
            
            .chatbot-panel {
                position: absolute;
                bottom: 80px;
                right: 0;
                width: 350px;
                max-height: 500px;
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                overflow: hidden;
                transform: translateY(20px) scale(0.95);
                opacity: 0;
                transition: all 0.3s ease;
                display: none;
            }
            
            .chatbot-widget.bottom-left .chatbot-panel {
                right: auto;
                left: 0;
            }
            
            .chatbot-panel.open {
                transform: translateY(0) scale(1);
                opacity: 1;
                display: block;
            }
            
            .chatbot-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .chatbot-header h6 {
                margin: 0;
                font-size: 16px;
                font-weight: 600;
            }
            
            .chatbot-close {
                background: none;
                border: none;
                color: white;
                font-size: 18px;
                cursor: pointer;
                padding: 5px;
                border-radius: 50%;
                transition: background 0.3s ease;
            }
            
            .chatbot-close:hover {
                background: rgba(255, 255, 255, 0.2);
            }
            
            .chatbot-body {
                padding: 20px;
                max-height: 400px;
                overflow-y: auto;
            }
            
            .chatbot-welcome {
                text-align: center;
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
            }
            
            .chatbot-welcome h6 {
                margin: 0 0 5px 0;
                color: #333;
                font-size: 14px;
            }
            
            .chatbot-search {
                display: flex;
                margin-bottom: 15px;
                border: 1px solid #e0e0e0;
                border-radius: 25px;
                overflow: hidden;
            }
            
            .chatbot-search input {
                flex: 1;
                border: none;
                padding: 10px 15px;
                outline: none;
                font-size: 14px;
            }
            
            .chatbot-search button {
                background: #667eea;
                border: none;
                color: white;
                padding: 10px 15px;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            
            .chatbot-search button:hover {
                background: #5a6fd8;
            }
            
            .chatbot-categories {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 15px;
            }
            
            .category-btn {
                background: #f8f9fa;
                border: 1px solid #e0e0e0;
                color: #666;
                padding: 6px 12px;
                border-radius: 15px;
                font-size: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .category-btn:hover,
            .category-btn.active {
                background: #667eea;
                color: white;
                border-color: #667eea;
            }
            
            .chatbot-faqs {
                max-height: 300px;
                overflow-y: auto;
            }
            
            .faq-item {
                background: white;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                margin-bottom: 10px;
                overflow: hidden;
                transition: all 0.3s ease;
                cursor: pointer;
            }
            
            .faq-item:hover {
                border-color: #667eea;
                box-shadow: 0 2px 10px rgba(102, 126, 234, 0.1);
            }
            
            .faq-question {
                padding: 12px 15px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                background: #f8f9fa;
                font-size: 14px;
                color: #333;
            }
            
            .faq-question i {
                color: #667eea;
                font-size: 12px;
                transition: transform 0.3s ease;
            }
            
            .faq-item.active .faq-question i {
                transform: rotate(180deg);
            }
            
            .faq-answer {
                padding: 0 15px;
                max-height: 0;
                overflow: hidden;
                transition: all 0.3s ease;
                background: white;
            }
            
            .faq-item.active .faq-answer {
                padding: 15px;
                max-height: 200px;
            }
            
            .faq-answer p {
                margin: 0 0 10px 0;
                color: #555;
                line-height: 1.5;
                font-size: 13px;
            }
            
            .help-link {
                color: #667eea;
                text-decoration: none;
                font-size: 12px;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }
            
            .help-link:hover {
                text-decoration: underline;
            }
            
            .chatbot-loading {
                text-align: center;
                padding: 20px;
                color: #666;
            }
            
            .chatbot-loading i {
                font-size: 20px;
                margin-bottom: 10px;
                color: #667eea;
            }
            
            .chatbot-no-faqs {
                text-align: center;
                padding: 20px;
                color: #666;
            }
            
            .chatbot-no-faqs i {
                font-size: 48px;
                color: #ddd;
                margin-bottom: 15px;
            }
            
            .faq-detail-header {
                padding: 15px 20px;
                border-bottom: 1px solid #e0e0e0;
                background: #f8f9fa;
            }
            
            .back-btn {
                background: none;
                border: none;
                color: #667eea;
                cursor: pointer;
                font-size: 14px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .back-btn:hover {
                color: #5a6fd8;
            }
            
            .faq-detail-content {
                padding: 20px;
            }
            
            .faq-detail-question {
                font-size: 16px;
                font-weight: 600;
                color: #333;
                margin-bottom: 15px;
            }
            
            .faq-detail-answer {
                color: #555;
                line-height: 1.6;
                margin-bottom: 15px;
            }
            
            .faq-detail-category {
                display: inline-block;
                background: #e9ecef;
                color: #495057;
                padding: 4px 8px;
                border-radius: 12px;
                font-size: 12px;
                margin-bottom: 15px;
            }
            
            /* Dark theme */
            .chatbot-widget.dark .chatbot-panel {
                background: #2c3e50;
                color: white;
            }
            
            .chatbot-widget.dark .chatbot-welcome {
                background: #34495e;
            }
            
            .chatbot-widget.dark .faq-item {
                background: #34495e;
                border-color: #4a5f7a;
            }
            
            .chatbot-widget.dark .faq-question {
                background: #34495e;
                color: white;
            }
            
            .chatbot-widget.dark .faq-answer {
                background: #34495e;
                color: #ecf0f1;
            }
            
            /* Responsive */
            @media (max-width: 480px) {
                .chatbot-panel {
                    width: 300px;
                    max-height: 400px;
                }
                
                .chatbot-widget.bottom-right {
                    bottom: 10px;
                    right: 10px;
                }
                
                .chatbot-widget.bottom-left {
                    bottom: 10px;
                    left: 10px;
                }
            }
            </style>
        `;
        
        document.head.insertAdjacentHTML('beforeend', styles);
    }
    
    bindEvents() {
        // Search functionality
        if (this.options.enableSearch) {
            const searchInput = document.getElementById('chatbot-search-input');
            const searchBtn = document.getElementById('chatbot-search-btn');
            
            searchInput.addEventListener('input', (e) => {
                this.searchKeyword = e.target.value;
                if (this.searchKeyword.length >= this.options.searchMinLength) {
                    this.searchFaqs();
                } else if (this.searchKeyword.length === 0) {
                    this.loadFaqs();
                }
            });
            
            searchBtn.addEventListener('click', () => {
                this.searchFaqs();
            });
            
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.searchFaqs();
                }
            });
        }
        
        // Category filtering
        if (this.options.showCategories) {
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('category-btn')) {
                    const category = e.target.dataset.category;
                    this.filterByCategory(category);
                    
                    // Update active state
                    document.querySelectorAll('.category-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    e.target.classList.add('active');
                }
            });
        }
    }
    
    async loadFaqs() {
        try {
            const response = await fetch(`${this.options.baseUrl}ajax/chatbot_ajax.php?action=get_faqs&category=${this.currentCategory || ''}`);
            const data = await response.json();
            
            if (data.success) {
                this.faqs = data.faqs.slice(0, this.options.maxFaqs);
                this.renderFaqs();
                this.updateBadge();
            } else {
                this.showError('Không thể tải FAQ');
            }
        } catch (error) {
            console.error('Error loading FAQs:', error);
            this.showError('Lỗi kết nối');
        }
    }
    
    async searchFaqs() {
        if (this.searchKeyword.length < this.options.searchMinLength) {
            return;
        }
        
        try {
            const response = await fetch(`${this.options.baseUrl}ajax/chatbot_ajax.php?action=search_faqs&keyword=${encodeURIComponent(this.searchKeyword)}`);
            const data = await response.json();
            
            if (data.success) {
                this.faqs = data.faqs.slice(0, this.options.maxFaqs);
                this.renderFaqs();
            } else {
                this.showError('Không tìm thấy kết quả');
            }
        } catch (error) {
            console.error('Error searching FAQs:', error);
            this.showError('Lỗi tìm kiếm');
        }
    }
    
    async loadCategories() {
        if (!this.options.showCategories) return;
        
        try {
            const response = await fetch(`${this.options.baseUrl}ajax/chatbot_ajax.php?action=get_categories`);
            const data = await response.json();
            
            if (data.success) {
                this.categories = data.categories;
                this.renderCategories();
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }
    
    renderCategories() {
        const categoriesContainer = document.getElementById('chatbot-categories');
        if (!categoriesContainer) return;
        
        let categoriesHTML = '<button class="category-btn active" data-category="">Tất cả</button>';
        
        this.categories.forEach(category => {
            categoriesHTML += `<button class="category-btn" data-category="${category}">${category}</button>`;
        });
        
        categoriesContainer.innerHTML = categoriesHTML;
    }
    
    renderFaqs() {
        const faqsContainer = document.getElementById('chatbot-faqs');
        if (!faqsContainer) return;
        
        if (this.faqs.length === 0) {
            faqsContainer.innerHTML = `
                <div class="chatbot-no-faqs">
                    <i class="fas fa-question-circle"></i>
                    <p>Không có câu hỏi nào</p>
                </div>
            `;
            return;
        }
        
        let faqsHTML = '';
        this.faqs.forEach(faq => {
            faqsHTML += `
                <div class="faq-item" onclick="chatbot.showFaqDetail(${faq.id})">
                    <div class="faq-question">
                        <span>${faq.question}</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            `;
        });
        
        faqsContainer.innerHTML = faqsHTML;
    }
    
    showFaqDetail(faqId) {
        const faq = this.faqs.find(f => f.id == faqId);
        if (!faq) return;
        
        const faqList = document.getElementById('chatbot-faqs');
        const faqDetail = document.getElementById('chatbot-faq-detail');
        const faqDetailContent = document.getElementById('faq-detail-content');
        
        faqDetailContent.innerHTML = `
            <div class="faq-detail-question">${faq.question}</div>
            <div class="faq-detail-category">${faq.category}</div>
            <div class="faq-detail-answer">${faq.answer}</div>
            ${faq.help_link ? `<a href="${faq.help_link}" target="_blank" class="help-link"><i class="fas fa-external-link-alt"></i> Xem thêm chi tiết</a>` : ''}
        `;
        
        faqList.style.display = 'none';
        faqDetail.style.display = 'block';
    }
    
    showFaqList() {
        const faqList = document.getElementById('chatbot-faqs');
        const faqDetail = document.getElementById('chatbot-faq-detail');
        
        faqList.style.display = 'block';
        faqDetail.style.display = 'none';
    }
    
    filterByCategory(category) {
        this.currentCategory = category;
        this.loadFaqs();
    }
    
    updateBadge() {
        const badge = document.getElementById('chatbot-badge');
        if (badge) {
            badge.textContent = this.faqs.length;
        }
    }
    
    showError(message) {
        const faqsContainer = document.getElementById('chatbot-faqs');
        if (faqsContainer) {
            faqsContainer.innerHTML = `
                <div class="chatbot-no-faqs">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${message}</p>
                </div>
            `;
        }
    }
    
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
    
    open() {
        const panel = document.getElementById('chatbot-panel');
        if (panel) {
            panel.classList.add('open');
            this.isOpen = true;
        }
    }
    
    close() {
        const panel = document.getElementById('chatbot-panel');
        if (panel) {
            panel.classList.remove('open');
            this.isOpen = false;
        }
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Create global chatbot instance
    window.chatbot = new ChatbotWidget({
        // Configuration options
        position: 'bottom-right',
        autoOpen: false,
        autoOpenDelay: 0,
        maxFaqs: 10,
        searchMinLength: 2,
        showCategories: true,
        enableSearch: true,
        theme: 'light',
        welcomeMessage: 'Xin chào! Chọn câu hỏi bạn muốn hỏi:',
        baseUrl: window.location.origin + '/ivymoda/ivymoda_mvc/public/'
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatbotWidget;
}