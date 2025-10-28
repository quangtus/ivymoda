/**
 * Chatbot AI Widget - UC3.47
 * Chatbot tư vấn sản phẩm với Gemini AI
 * 
 * Features:
 * - Chat với Gemini AI
 * - Gợi ý sản phẩm
 * - Lưu sở thích người dùng
 * - Lịch sử hội thoại
 * - Responsive design
 */

class ChatbotAI {
    constructor(options = {}) {
        this.options = {
            // Default settings
            position: 'bottom-right',
            autoOpen: false,
            autoOpenDelay: 0,
            maxMessageLength: 500,
            responseTimeout: 30000,
            theme: 'light',
            welcomeMessage: 'Xin chào! Tôi có thể giúp gì cho bạn hôm nay? 😊',
            baseUrl: window.location.origin + '/ivymoda/ivymoda_mvc/public/',
            ...options
        };

        this.isOpen = false;
        this.messages = [];
        this.sessionId = this.generateSessionId();
        this.isTyping = false;

        this.init();
    }

    init() {
        this.createWidget();
        this.bindEvents();
        this.loadConversationHistory();

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
            <div id="chatbot-ai-widget" class="chatbot-ai-widget ${this.options.theme} ${this.options.position}">
                <!-- Toggle Button -->
                <div class="chatbot-ai-toggle" onclick="chatbotAI.toggle()">
                    <i class="fas fa-robot"></i>
                    <span class="chatbot-ai-badge" id="chatbot-ai-badge" style="display: none;">0</span>
                </div>
                
                <!-- Chatbot Panel -->
                <div class="chatbot-ai-panel" id="chatbot-ai-panel">
                    <div class="chatbot-ai-header">
                        <div class="chatbot-ai-title">
                            <i class="fas fa-robot"></i>
                            <span>AI Tư vấn</span>
                        </div>
                        <button class="chatbot-ai-close" onclick="chatbotAI.close()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="chatbot-ai-body">
                        <!-- Messages Container -->
                        <div class="chatbot-ai-messages" id="chatbot-ai-messages">
                            <!-- Welcome Message -->
                            <div class="chatbot-ai-welcome">
                                <div class="bot-message">
                                    <div class="message-avatar">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="message-content">
                                        <div class="message-text">${this.options.welcomeMessage}</div>
                                        <div class="message-time">${this.getCurrentTime()}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Typing Indicator -->
                        <div class="chatbot-ai-typing" id="chatbot-ai-typing" style="display: none;">
                            <div class="typing-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <span class="typing-text">AI đang trả lời...</span>
                        </div>
                        
                        <!-- Input Area -->
                        <div class="chatbot-ai-input-area">
                            <div class="chatbot-ai-input-container">
                                <textarea 
                                    id="chatbot-ai-input" 
                                    placeholder="Nhập câu hỏi của bạn..."
                                    rows="1"
                                    maxlength="${this.options.maxMessageLength}"></textarea>
                                <button id="chatbot-ai-send" class="chatbot-ai-send-btn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div class="chatbot-ai-suggestions" id="chatbot-ai-suggestions">
                                <button class="suggestion-btn" onclick="chatbotAI.sendSuggestion('Tôi muốn tìm áo sơ mi nam')">Tìm áo sơ mi nam</button>
                                <button class="suggestion-btn" onclick="chatbotAI.sendSuggestion('Gợi ý sản phẩm bán chạy')">Sản phẩm bán chạy</button>
                                <button class="suggestion-btn" onclick="chatbotAI.sendSuggestion('Tư vấn size phù hợp')">Tư vấn size</button>
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
            .chatbot-ai-widget {
                position: fixed;
                z-index: 9999;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            }
            
            .chatbot-ai-widget.bottom-right {
                bottom: 20px;
                right: 20px;
            }
            
            .chatbot-ai-widget.bottom-left {
                bottom: 20px;
                left: 20px;
            }
            
            .chatbot-ai-toggle {
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
            
            .chatbot-ai-toggle:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 25px rgba(102, 126, 234, 0.6);
            }
            
            .chatbot-ai-toggle i {
                color: white;
                font-size: 24px;
            }
            
            .chatbot-ai-badge {
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
            
            .chatbot-ai-panel {
                position: absolute;
                bottom: 80px;
                right: 0;
                width: 380px;
                height: 600px;
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
                overflow: hidden;
                transform: translateY(20px) scale(0.95);
                opacity: 0;
                transition: all 0.3s ease;
                display: none;
                flex-direction: column;
            }
            
            .chatbot-ai-widget.bottom-left .chatbot-ai-panel {
                right: auto;
                left: 0;
            }
            
            .chatbot-ai-panel.open {
                transform: translateY(0) scale(1);
                opacity: 1;
                display: flex;
            }
            
            .chatbot-ai-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-shrink: 0;
            }
            
            .chatbot-ai-title {
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
                font-size: 16px;
            }
            
            .chatbot-ai-title i {
                font-size: 18px;
            }
            
            .chatbot-ai-close {
                background: none;
                border: none;
                color: white;
                font-size: 18px;
                cursor: pointer;
                padding: 5px;
                border-radius: 50%;
                transition: background 0.3s ease;
            }
            
            .chatbot-ai-close:hover {
                background: rgba(255, 255, 255, 0.2);
            }
            
            .chatbot-ai-body {
                flex: 1;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }
            
            .chatbot-ai-messages {
                flex: 1;
                padding: 20px;
                overflow-y: auto;
                display: flex;
                flex-direction: column;
                gap: 15px;
            }
            
            .chatbot-ai-welcome {
                margin-bottom: 20px;
            }
            
            .bot-message, .user-message {
                display: flex;
                gap: 10px;
                max-width: 85%;
                animation: fadeInUp 0.3s ease;
            }
            
            .user-message {
                align-self: flex-end;
                flex-direction: row-reverse;
            }
            
            .message-avatar {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            
            .bot-message .message-avatar {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            
            .user-message .message-avatar {
                background: #f8f9fa;
                color: #667eea;
                border: 2px solid #667eea;
            }
            
            .message-content {
                flex: 1;
            }
            
            .message-text {
                background: #f8f9fa;
                padding: 12px 15px;
                border-radius: 18px;
                font-size: 14px;
                line-height: 1.4;
                color: #333;
                word-wrap: break-word;
            }
            
            .user-message .message-text {
                background: #667eea;
                color: white;
            }
            
            .message-time {
                font-size: 11px;
                color: #999;
                margin-top: 5px;
                text-align: right;
            }
            
            .user-message .message-time {
                text-align: left;
            }
            
            .suggested-products {
                margin-top: 10px;
            }
            
            .suggested-product {
                background: white;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                padding: 10px;
                margin-bottom: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .suggested-product:hover {
                border-color: #667eea;
                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
            }
            
            .suggested-product h6 {
                margin: 0 0 5px 0;
                font-size: 13px;
                color: #333;
            }
            
            .suggested-product p {
                margin: 0;
                font-size: 12px;
                color: #667eea;
                font-weight: 600;
            }
            
            .chatbot-ai-typing {
                padding: 10px 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .typing-dots {
                display: flex;
                gap: 4px;
            }
            
            .typing-dots span {
                width: 8px;
                height: 8px;
                background: #667eea;
                border-radius: 50%;
                animation: typing 1.4s infinite ease-in-out;
            }
            
            .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
            .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
            
            .typing-text {
                font-size: 12px;
                color: #999;
            }
            
            .chatbot-ai-input-area {
                padding: 15px 20px;
                border-top: 1px solid #e0e0e0;
                background: white;
                flex-shrink: 0;
            }
            
            .chatbot-ai-input-container {
                display: flex;
                gap: 10px;
                align-items: flex-end;
            }
            
            .chatbot-ai-input-container textarea {
                flex: 1;
                border: 1px solid #e0e0e0;
                border-radius: 20px;
                padding: 10px 15px;
                font-size: 14px;
                resize: none;
                outline: none;
                max-height: 100px;
                font-family: inherit;
            }
            
            .chatbot-ai-input-container textarea:focus {
                border-color: #667eea;
            }
            
            .chatbot-ai-send-btn {
                background: #667eea;
                border: none;
                color: white;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                flex-shrink: 0;
            }
            
            .chatbot-ai-send-btn:hover {
                background: #5a6fd8;
                transform: scale(1.05);
            }
            
            .chatbot-ai-send-btn:disabled {
                background: #ccc;
                cursor: not-allowed;
                transform: none;
            }
            
            .chatbot-ai-suggestions {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 10px;
            }
            
            .suggestion-btn {
                background: #f8f9fa;
                border: 1px solid #e0e0e0;
                color: #666;
                padding: 6px 12px;
                border-radius: 15px;
                font-size: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .suggestion-btn:hover {
                background: #667eea;
                color: white;
                border-color: #667eea;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes typing {
                0%, 80%, 100% {
                    transform: scale(0);
                }
                40% {
                    transform: scale(1);
                }
            }
            
            /* Dark theme */
            .chatbot-ai-widget.dark .chatbot-ai-panel {
                background: #2c3e50;
                color: white;
            }
            
            .chatbot-ai-widget.dark .message-text {
                background: #34495e;
                color: white;
            }
            
            .chatbot-ai-widget.dark .chatbot-ai-input-container textarea {
                background: #34495e;
                color: white;
                border-color: #4a5f7a;
            }
            
            .chatbot-ai-widget.dark .suggested-product {
                background: #34495e;
                border-color: #4a5f7a;
                color: white;
            }
            
            /* Responsive */
            @media (max-width: 480px) {
                .chatbot-ai-panel {
                    width: 320px;
                    height: 500px;
                }
                
                .chatbot-ai-widget.bottom-right {
                    bottom: 10px;
                    right: 10px;
                }
                
                .chatbot-ai-widget.bottom-left {
                    bottom: 10px;
                    left: 10px;
                }
            }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    bindEvents() {
        const input = document.getElementById('chatbot-ai-input');
        const sendBtn = document.getElementById('chatbot-ai-send');

        // Auto-resize textarea
        input.addEventListener('input', (e) => {
            e.target.style.height = 'auto';
            e.target.style.height = Math.min(e.target.scrollHeight, 100) + 'px';
        });

        // Send message on Enter (Shift+Enter for new line)
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Send button click
        sendBtn.addEventListener('click', () => {
            this.sendMessage();
        });

        // Input focus
        input.addEventListener('focus', () => {
            this.open();
        });
    }

    async sendMessage(message = null) {
        const input = document.getElementById('chatbot-ai-input');
        const messageText = message || input.value.trim();

        if (!messageText) return;

        // Clear input
        if (!message) {
            input.value = '';
            input.style.height = 'auto';
        }

        // Add user message
        this.addMessage(messageText, 'user');

        // Show typing indicator
        this.showTyping();

        try {
            const response = await fetch(`${this.options.baseUrl}ajax/chatbot_ajax.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'chat_with_ai',
                    message: messageText,
                    session_id: this.sessionId
                })
            });

            const data = await response.json();

            this.hideTyping();

            if (data.success) {
                // Add bot response
                this.addMessage(data.response, 'bot');

                // Show suggested products if any
                if (data.suggested_products && data.suggested_products.length > 0) {
                    this.showSuggestedProducts(data.suggested_products);
                }

                // Update badge
                this.updateBadge();
            } else {
                this.addMessage(data.message || 'Xin lỗi, tôi không thể trả lời ngay bây giờ.', 'bot');
            }

        } catch (error) {
            this.hideTyping();
            this.addMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.', 'bot');
            console.error('Chat error:', error);
        }
    }

    sendSuggestion(message) {
        this.sendMessage(message);
    }

    addMessage(text, sender) {
        const messagesContainer = document.getElementById('chatbot-ai-messages');
        const messageElement = document.createElement('div');

        messageElement.className = `${sender}-message`;
        messageElement.innerHTML = `
            <div class="message-avatar">
                <i class="fas fa-${sender === 'bot' ? 'robot' : 'user'}"></i>
            </div>
            <div class="message-content">
                <div class="message-text">${this.formatMessage(text)}</div>
                <div class="message-time">${this.getCurrentTime()}</div>
            </div>
        `;

        messagesContainer.appendChild(messageElement);
        this.scrollToBottom();

        // Store message
        this.messages.push({
            text,
            sender,
            timestamp: new Date().toISOString()
        });
    }

    showSuggestedProducts(products) {
            const messagesContainer = document.getElementById('chatbot-ai-messages');
            const productsElement = document.createElement('div');

            productsElement.className = 'suggested-products';
            productsElement.innerHTML = `
            <div class="bot-message">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-text">
                        <strong>Sản phẩm gợi ý:</strong>
                        ${products.map(product => `
                            <div class="suggested-product" onclick="chatbotAI.viewProduct(${product.sanpham_id})">
                                <h6>${product.sanpham_tieude}</h6>
                                <p>${this.formatPrice(product.sanpham_gia)} - ${product.danhmuc_ten}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
        
        messagesContainer.appendChild(productsElement);
        this.scrollToBottom();
    }
    
    formatMessage(text) {
        // Basic formatting
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }
    
    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }
    
    getCurrentTime() {
        return new Date().toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    showTyping() {
        const typingElement = document.getElementById('chatbot-ai-typing');
        typingElement.style.display = 'flex';
        this.isTyping = true;
        this.scrollToBottom();
    }
    
    hideTyping() {
        const typingElement = document.getElementById('chatbot-ai-typing');
        typingElement.style.display = 'none';
        this.isTyping = false;
    }
    
    scrollToBottom() {
        const messagesContainer = document.getElementById('chatbot-ai-messages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    updateBadge() {
        const badge = document.getElementById('chatbot-ai-badge');
        const unreadCount = this.messages.filter(msg => msg.sender === 'bot').length;
        
        if (unreadCount > 0 && !this.isOpen) {
            badge.style.display = 'flex';
            badge.textContent = unreadCount;
        } else {
            badge.style.display = 'none';
        }
    }
    
    generateSessionId() {
        return 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    async loadConversationHistory() {
        try {
            const response = await fetch(`${this.options.baseUrl}ajax/chatbot_ajax.php?action=get_conversation_history&session_id=${this.sessionId}&limit=5`);
            const data = await response.json();
            
            if (data.success && data.history.length > 0) {
                // Load recent conversation history
                data.history.reverse().forEach(conversation => {
                    this.addMessage(conversation.user_message, 'user');
                    this.addMessage(conversation.bot_response, 'bot');
                    
                    if (conversation.suggested_products && conversation.suggested_products.length > 0) {
                        this.showSuggestedProducts(conversation.suggested_products);
                    }
                });
            }
        } catch (error) {
            console.error('Error loading conversation history:', error);
        }
    }
    
    viewProduct(productId) {
        // Redirect to product page
        window.open(`${this.options.baseUrl}../product/detail/${productId}`, '_blank');
    }
    
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
    
    open() {
        const panel = document.getElementById('chatbot-ai-panel');
        if (panel) {
            panel.classList.add('open');
            this.isOpen = true;
            
            // Clear badge
            const badge = document.getElementById('chatbot-ai-badge');
            badge.style.display = 'none';
            
            // Focus input
            setTimeout(() => {
                const input = document.getElementById('chatbot-ai-input');
                input.focus();
            }, 300);
        }
    }
    
    close() {
        const panel = document.getElementById('chatbot-ai-panel');
        if (panel) {
            panel.classList.remove('open');
            this.isOpen = false;
        }
    }
}

// Initialize chatbot AI when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Create global chatbot AI instance
    window.chatbotAI = new ChatbotAI({
        // Configuration options
        position: 'bottom-right',
        autoOpen: false,
        autoOpenDelay: 0,
        maxMessageLength: 500,
        responseTimeout: 30000,
        theme: 'light',
        welcomeMessage: 'Xin chào! Tôi có thể giúp gì cho bạn hôm nay? 😊',
        baseUrl: window.location.origin + '/ivymoda/ivymoda_mvc/public/'
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ChatbotAI;
}