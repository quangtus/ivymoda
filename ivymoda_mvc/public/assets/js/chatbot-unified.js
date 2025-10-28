/**
 * Unified Chatbot Widget - UC3.47 & UC3.48
 * Kết hợp FAQ Chatbot và AI Chatbot trong 1 widget với tab switching
 * 
 * Features:
 * - Single widget với 2 tabs: FAQ và AI Tư vấn
 * - FAQ: Hiển thị danh sách câu hỏi, tìm kiếm, phân loại
 * - AI: Chat với Gemini AI, gợi ý sản phẩm
 * - Responsive design
 * - Smooth transitions
 */

class UnifiedChatbot {
    constructor(options = {}) {
        this.options = {
            position: 'bottom-right',
            autoOpen: false,
            theme: 'light',
            defaultTab: 'faq',
            baseUrl: window.BASE_URL || window.location.origin + '/ivymoda/ivymoda_mvc/public/',
            assetsUrl: window.ASSETS_URL || window.BASE_URL + 'assets/',
            ajaxUrl: (window.BASE_URL || '') + 'ajax/chatbot_ajax.php',
            ...options
        };

        this.isOpen = false;
        this.currentTab = this.options.defaultTab;

        // FAQ specific properties
        this.faqs = [];
        this.faqCategories = [];
        this.currentCategory = null;
        this.searchKeyword = '';

        // AI specific properties
        this.aiMessages = [];
        this.aiSessionId = this.generateSessionId();
        this.isAITyping = false;

        this.init();
    }

    init() {
        console.log('🤖 Initializing Unified Chatbot...');
        this.createWidget();
        this.bindEvents();
        this.loadFAQData();

        console.log('✅ Unified Chatbot initialized');
    }

    createWidget() {
        const widgetHTML = `
            <div id="unified-chatbot-widget" class="unified-chatbot-widget ${this.options.theme} ${this.options.position}">
                <!-- Toggle Button -->
                <button class="unified-chatbot-toggle" id="unified-chatbot-toggle">
                    <i class="fas fa-comments"></i>
                    <span class="unified-chatbot-badge" id="unified-chatbot-badge" style="display: none;">0</span>
                </button>
                
                <!-- Chatbot Panel -->
                <div class="unified-chatbot-panel" id="unified-chatbot-panel">
                    <!-- Header -->
                    <div class="unified-chatbot-header">
                        <div class="unified-chatbot-title">
                            <i class="fas fa-robot"></i>
                            <span>Trợ lý ảo IVY</span>
                        </div>
                        <button class="unified-chatbot-close" id="unified-chatbot-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <!-- Tab Navigation -->
                    <div class="unified-chatbot-tabs">
                        <button class="unified-chatbot-tab active" data-tab="faq" id="tab-faq">
                            <i class="fas fa-question-circle"></i>
                            <span>Hỏi đáp</span>
                        </button>
                        <button class="unified-chatbot-tab" data-tab="ai" id="tab-ai">
                            <i class="fas fa-robot"></i>
                            <span>AI Tư vấn</span>
                        </button>
                    </div>
                    
                    <!-- Body Content -->
                    <div class="unified-chatbot-body">
                        <!-- FAQ Content -->
                        <div class="unified-chatbot-content active" id="content-faq">
                            <div class="faq-content" id="faq-main">
                                <!-- Welcome Message -->
                                <div class="faq-welcome">
                                    <h6>${this.options.faq.welcomeMessage}</h6>
                                </div>
                                
                                <!-- Search -->
                                <div class="faq-search">
                                    <input type="text" id="faq-search-input" placeholder="Tìm kiếm câu hỏi...">
                                    <button id="faq-search-btn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                
                                <!-- Categories -->
                                <div class="faq-categories" id="faq-categories">
                                    <button class="faq-category-btn active" data-category="">Tất cả</button>
                                </div>
                                
                                <!-- FAQ List -->
                                <div class="faq-list" id="faq-list">
                                    <div class="chatbot-loading">
                                        <i class="fas fa-spinner fa-spin"></i>
                                        <span>Đang tải câu hỏi...</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FAQ Detail -->
                            <div class="faq-detail" id="faq-detail">
                                <div class="faq-detail-header">
                                    <button class="faq-back-btn" id="faq-back-btn">
                                        <i class="fas fa-arrow-left"></i>
                                        <span>Quay lại</span>
                                    </button>
                                </div>
                                <div class="faq-detail-content" id="faq-detail-content">
                                    <!-- FAQ detail will be loaded here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- AI Content -->
                        <div class="unified-chatbot-content" id="content-ai">
                            <div class="ai-content">
                                <!-- Messages -->
                                <div class="ai-messages" id="ai-messages">
                                    <div class="ai-message bot">
                                        <div class="ai-message-avatar">
                                            <i class="fas fa-robot"></i>
                                        </div>
                                        <div class="ai-message-content">
                                            <div class="ai-message-bubble">${(this.options.ai.welcomeMessage || '').trim()}</div>
                                            <div class="ai-message-time">${this.getCurrentTime()}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Typing Indicator -->
                                <div class="ai-typing" id="ai-typing" style="display: none;">
                                    <div class="ai-typing-dots">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <span class="ai-typing-text">AI đang trả lời...</span>
                                </div>
                                
                                <!-- Input Area -->
                                <div class="ai-input-area">
                                    <div class="ai-input-container">
                                        <textarea 
                                            id="ai-input" 
                                            placeholder="Nhập câu hỏi của bạn..."
                                            rows="1"
                                            maxlength="${this.options.ai.maxMessageLength}"></textarea>
                                        <button id="ai-send-btn" class="ai-send-btn">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                    <div class="ai-suggestions" id="ai-suggestions">
                                        <button class="ai-suggestion-btn" data-text="Tôi muốn tìm áo sơ mi nam">Tìm áo sơ mi nam</button>
                                        <button class="ai-suggestion-btn" data-text="Gợi ý sản phẩm bán chạy">Sản phẩm bán chạy</button>
                                        <button class="ai-suggestion-btn" data-text="Tư vấn size phù hợp">Tư vấn size</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', widgetHTML);
    }

    bindEvents() {
        // Toggle button
        document.getElementById('unified-chatbot-toggle').addEventListener('click', () => {
            this.toggle();
        });

        // Close button
        document.getElementById('unified-chatbot-close').addEventListener('click', () => {
            this.close();
        });

        // Tab switching
        document.querySelectorAll('.unified-chatbot-tab').forEach(tab => {
            tab.addEventListener('click', (e) => {
                const tabName = e.currentTarget.dataset.tab;
                this.switchTab(tabName);
            });
        });

        // FAQ Events
        this.bindFAQEvents();

        // AI Events
        this.bindAIEvents();
    }

    bindFAQEvents() {
        // Search
        const searchInput = document.getElementById('faq-search-input');
        const searchBtn = document.getElementById('faq-search-btn');

        searchBtn.addEventListener('click', () => {
            this.searchKeyword = searchInput.value.trim();
            this.renderFAQList();
        });

        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.searchKeyword = searchInput.value.trim();
                this.renderFAQList();
            }
        });

        // Back button
        document.getElementById('faq-back-btn').addEventListener('click', () => {
            this.showFAQList();
        });
    }

    bindAIEvents() {
        const aiInput = document.getElementById('ai-input');
        const aiSendBtn = document.getElementById('ai-send-btn');

        // Send button
        aiSendBtn.addEventListener('click', () => {
            this.sendAIMessage();
        });

        // Enter to send (Shift+Enter for new line)
        aiInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendAIMessage();
            }
        });

        // Auto resize textarea
        aiInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Suggestion buttons
        document.querySelectorAll('.ai-suggestion-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const text = e.currentTarget.dataset.text;
                aiInput.value = text;
                this.sendAIMessage();
            });
        });
    }

    // ============================================
    // CORE METHODS
    // ============================================

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        const panel = document.getElementById('unified-chatbot-panel');
        panel.classList.add('open');
        this.isOpen = true;
        console.log('📖 Chatbot opened');

        // Load AI history if on AI tab
        if (this.currentTab === 'ai' && this.aiMessages.length === 0) {
            this.loadAIHistory();
        }
    }

    close() {
        const panel = document.getElementById('unified-chatbot-panel');
        panel.classList.remove('open');
        this.isOpen = false;
        console.log('📕 Chatbot closed');
    }

    switchTab(tabName) {
        console.log('🔄 Switching to tab:', tabName);

        // Update tab buttons
        document.querySelectorAll('.unified-chatbot-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.getElementById(`tab-${tabName}`).classList.add('active');

        // Update content
        document.querySelectorAll('.unified-chatbot-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(`content-${tabName}`).classList.add('active');

        this.currentTab = tabName;

        // Load data if needed
        if (tabName === 'ai' && this.aiMessages.length === 0) {
            this.loadAIHistory();
        }
    }

    // ============================================
    // FAQ METHODS
    // ============================================

    async loadFAQData() {
        try {
            // Load FAQs
            const faqResponse = await fetch(this.options.ajaxUrl + '?action=get_faqs');
            const faqData = await faqResponse.json();

            if (faqData.success) {
                this.faqs = faqData.faqs || [];
                console.log('📚 Loaded', this.faqs.length, 'FAQs');
            }

            // Load categories
            const catResponse = await fetch(this.options.ajaxUrl + '?action=get_faq_categories');
            const catData = await catResponse.json();

            if (catData.success) {
                this.faqCategories = catData.categories || [];
                console.log('📂 Loaded', this.faqCategories.length, 'FAQ categories');
                this.renderFAQCategories();
            }

            this.renderFAQList();

        } catch (error) {
            console.error('❌ Error loading FAQ data:', error);
            this.showFAQError('Không thể tải dữ liệu. Vui lòng thử lại sau.');
        }
    }

    renderFAQCategories() {
        const container = document.getElementById('faq-categories');

        let html = '<button class="faq-category-btn active" data-category="">Tất cả</button>';

        this.faqCategories.forEach(cat => {
            html += `<button class="faq-category-btn" data-category="${cat.id}">${cat.name}</button>`;
        });

        container.innerHTML = html;

        // Bind click events
        container.querySelectorAll('.faq-category-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                container.querySelectorAll('.faq-category-btn').forEach(b => b.classList.remove('active'));
                e.currentTarget.classList.add('active');

                this.currentCategory = e.currentTarget.dataset.category || null;
                this.renderFAQList();
            });
        });
    }

    renderFAQList() {
        const container = document.getElementById('faq-list');

        // Filter FAQs
        let filteredFaqs = this.faqs;

        if (this.currentCategory) {
            filteredFaqs = filteredFaqs.filter(faq => faq.category == this.currentCategory);
        }

        if (this.searchKeyword && this.searchKeyword.length >= 2) {
            const keyword = this.searchKeyword.toLowerCase();
            filteredFaqs = filteredFaqs.filter(faq =>
                faq.question.toLowerCase().includes(keyword) ||
                faq.answer.toLowerCase().includes(keyword)
            );
        }

        // Render
        if (filteredFaqs.length === 0) {
            container.innerHTML = `
                <div class="chatbot-empty">
                    <i class="fas fa-search"></i>
                    <p>Không tìm thấy câu hỏi phù hợp</p>
                </div>
            `;
            return;
        }

        let html = '';
        filteredFaqs.forEach(faq => {
            // Strip HTML tags và truncate text
            const plainAnswer = faq.answer.replace(/<[^>]*>/g, '');
            const preview = plainAnswer.length > 100 ? plainAnswer.substring(0, 100) + '...' : plainAnswer;

            html += `
                <div class="faq-item" data-id="${faq.id}">
                    <div class="faq-item-question">
                        <i class="fas fa-question-circle"></i>
                        ${this.escapeHtml(faq.question)}
                    </div>
                    <div class="faq-item-answer">
                        ${this.escapeHtml(preview)}
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        // Bind click events
        container.querySelectorAll('.faq-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const faqId = e.currentTarget.dataset.id;
                this.showFAQDetail(faqId);
            });
        });
    }

    showFAQDetail(faqId) {
            const faq = this.faqs.find(f => f.id == faqId);
            if (!faq) {
                console.warn('❌ FAQ not found:', faqId);
                return;
            }

            console.log('📖 Showing FAQ detail:', faq);

            const detailContent = document.getElementById('faq-detail-content');
            const faqMain = document.getElementById('faq-main');
            const faqDetail = document.getElementById('faq-detail');

            console.log('🔍 Elements check:', {
                detailContent: !!detailContent,
                faqMain: !!faqMain,
                faqDetail: !!faqDetail
            });

            if (!detailContent || !faqMain || !faqDetail) {
                console.error('❌ Required elements not found!');
                return;
            }

            // Không escape HTML cho answer vì cần hiển thị formatting
            detailContent.innerHTML = `
            <h6>${this.escapeHtml(faq.question)}</h6>
            <div class="faq-answer-content">${faq.answer}</div>
            ${faq.help_link ? `<p class="faq-help-link"><a href="${faq.help_link}" target="_blank">Xem thêm chi tiết →</a></p>` : ''}
        `;
        
        console.log('📝 HTML set, length:', detailContent.innerHTML.length);
        
        // Toggle visibility: use both class and inline style to avoid CSS conflicts
        faqMain.classList.add('hidden');
        faqDetail.classList.add('active');
        // Inline styles ensure the detail is shown even if stylesheet fails to load
        faqMain.style.display = 'none';
        faqDetail.style.display = 'flex';
        
        console.log('✅ FAQ detail displayed, classes:', {
            mainHidden: faqMain.classList.contains('hidden'),
            detailActive: faqDetail.classList.contains('active'),
            detailHTML: detailContent.innerHTML.substring(0, 100)
        });
    }

    showFAQList() {
        const faqMain = document.getElementById('faq-main');
        const faqDetail = document.getElementById('faq-detail');

        faqMain.classList.remove('hidden');
        faqDetail.classList.remove('active');
        // Reset inline styles
        faqMain.style.display = '';
        faqDetail.style.display = '';
    }

    showFAQError(message) {
        document.getElementById('faq-list').innerHTML = `
            <div class="chatbot-empty">
                <i class="fas fa-exclamation-triangle"></i>
                <p>${message}</p>
            </div>
        `;
    }

    // ============================================
    // AI METHODS
    // ============================================

    async sendAIMessage() {
        const input = document.getElementById('ai-input');
        const message = input.value.trim();

        if (!message || this.isAITyping) return;

        console.log('💬 Sending AI message:', message);

        // Add user message to UI
        this.addAIMessage('user', message);

        // Clear input
        input.value = '';
        input.style.height = 'auto';

        // Hide suggestions after first message
        document.getElementById('ai-suggestions').style.display = 'none';

        // Show typing indicator
        this.showAITyping(true);

        try {
            const response = await fetch(this.options.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=chat_ai&message=${encodeURIComponent(message)}&session_id=${this.aiSessionId}`
            });

            const data = await response.json();
            
            console.log('🔍 AI Response:', data);

            this.showAITyping(false);

            if (data.success) {
                this.addAIMessage('bot', data.response);

                // Show suggested products if any
                if (data.products && data.products.length > 0) {
                    this.showSuggestedProducts(data.products);
                }
            } else {
                console.error('❌ AI Backend Error:', data.error || 'Unknown error');
                this.addAIMessage('bot', 'Xin lỗi, tôi gặp sự cố. Vui lòng thử lại sau.');
            }

        } catch (error) {
            console.error('❌ AI Error:', error);
            this.showAITyping(false);
            this.addAIMessage('bot', 'Xin lỗi, không thể kết nối đến AI. Vui lòng thử lại sau.');
        }
    }

    addAIMessage(type, text) {
        const messagesContainer = document.getElementById('ai-messages');

        const messageHTML = `
            <div class="ai-message ${type}">
                <div class="ai-message-avatar"><i class="fas fa-${type === 'bot' ? 'robot' : 'user'}"></i></div>
                <div class="ai-message-content">
                    <div class="ai-message-bubble">${this.formatMessage(text)}</div>
                    <div class="ai-message-time">${this.getCurrentTime()}</div>
                </div>
            </div>`;

        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);

        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Save to messages array
        this.aiMessages.push({ type, text, time: new Date() });
    }

    // Format plain text into safe HTML with lightweight markdown
    formatMessage(text) {
        const escapeHtml = (str) => String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

        let safe = escapeHtml(text)
            // Collapse consecutive spaces/tabs
            .replace(/[ \t]{2,}/g, ' ')
            .trim();

        // Basic markdown
        safe = safe
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>');

        // Normalize bullets at start of line
        safe = safe.replace(/(^|\n)[\-•]\s+(.*)/g, (m, p1, item) => `${p1}• ${item}`);

        // Normalize newlines and trim line ends, then convert to <br>
        safe = safe
            .replace(/\\r\\n/g, '\n')
            .replace(/\r\n/g, '\n')
            .replace(/\\n/g, '\n')
            .split('\n')
            .map(line => line.replace(/[ \t]+/g, ' ').replace(/\s+$/g, ''))
            .join('\n')
            .replace(/(\n\s*){3,}/g, '\n\n')
            .replace(/\n/g, '<br>')
            // Trim leading/trailing <br> introduced by formatting
            .replace(/^(<br>\s*)+/, '')
            .replace(/(\s*<br>)+$/, '')
            // Collapse 3+ consecutive <br> to 2
            .replace(/(?:<br>\s*){3,}/g, '<br><br>')
            // Remove invisible BOM/ZWSP at edges
            .replace(/^[\u200B\uFEFF\u00A0]+/, '')
            .replace(/[\u200B\uFEFF\u00A0]+$/, '');

        return safe;
    }

    showAITyping(show) {
        const typingIndicator = document.getElementById('ai-typing');
        typingIndicator.style.display = show ? 'flex' : 'none';
        this.isAITyping = show;

        if (show) {
            const messagesContainer = document.getElementById('ai-messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }

    showSuggestedProducts(products) {
        // This can be enhanced to show product cards
        console.log('🛍️ Suggested products:', products);
    }

    async loadAIHistory() {
        // Load conversation history from server if needed
        console.log('📜 Loading AI conversation history...');
        // Implementation here if you want to persist chat history
    }

    // ============================================
    // UTILITY METHODS
    // ============================================

    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    getCurrentTime() {
        const now = new Date();
        return now.getHours().toString().padStart(2, '0') + ':' +
            now.getMinutes().toString().padStart(2, '0');
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Export for global access
window.UnifiedChatbot = UnifiedChatbot;

console.log('✅ UnifiedChatbot class loaded');