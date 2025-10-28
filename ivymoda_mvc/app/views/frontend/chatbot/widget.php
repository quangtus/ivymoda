<?php
/**
 * Chatbot Widget View - UC3.47 & UC3.48
 * Tích hợp cả chatbot AI và FAQ
 */

// Kiểm tra xem có nên hiển thị chatbot không
$showChatbot = true; // Có thể thêm logic điều kiện ở đây
?>

<?php if ($showChatbot): ?>
<!-- Chatbot Widget Container -->
<div id="chatbot-widgets">
    <!-- FAQ Chatbot (UC3.48) -->
    <div id="chatbot-faq-container"></div>
    
    <!-- AI Chatbot (UC3.47) -->
    <div id="chatbot-ai-container"></div>
    
    <!-- Mode Switcher -->
    <div class="chatbot-mode-switcher" id="chatbot-mode-switcher">
        <button class="mode-btn active" data-mode="faq" onclick="switchChatbotMode('faq')">
            <i class="fas fa-question-circle"></i>
            <span>FAQ</span>
        </button>
        <button class="mode-btn" data-mode="ai" onclick="switchChatbotMode('ai')">
            <i class="fas fa-robot"></i>
            <span>AI Tư vấn</span>
        </button>
    </div>
</div>

<style>
.chatbot-mode-switcher {
    position: fixed;
    bottom: 90px;
    right: 20px;
    z-index: 9998;
    display: flex;
    gap: 5px;
    background: white;
    border-radius: 25px;
    padding: 5px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.3s ease;
}

.chatbot-mode-switcher.show {
    opacity: 1;
    transform: translateY(0);
}

.mode-btn {
    background: #f8f9fa;
    border: none;
    border-radius: 20px;
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: #666;
    transition: all 0.3s ease;
}

.mode-btn:hover {
    background: #e9ecef;
}

.mode-btn.active {
    background: #667eea;
    color: white;
}

.mode-btn i {
    font-size: 14px;
}

@media (max-width: 480px) {
    .chatbot-mode-switcher {
        bottom: 80px;
        right: 10px;
    }
    
    .mode-btn span {
        display: none;
    }
    
    .mode-btn {
        padding: 8px;
    }
}
</style>

<script>
// Global chatbot mode management
let currentChatbotMode = 'faq';

function switchChatbotMode(mode) {
    if (mode === currentChatbotMode) return;
    
    // Hide current chatbot
    if (currentChatbotMode === 'faq' && window.chatbot) {
        window.chatbot.close();
    } else if (currentChatbotMode === 'ai' && window.chatbotAI) {
        window.chatbotAI.close();
    }
    
    // Update active button
    document.querySelectorAll('.mode-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-mode="${mode}"]`).classList.add('active');
    
    // Set new mode
    currentChatbotMode = mode;
    
    // Show appropriate chatbot
    if (mode === 'faq' && window.chatbot) {
        window.chatbot.open();
    } else if (mode === 'ai' && window.chatbotAI) {
        window.chatbotAI.open();
    }
}

// Show mode switcher when any chatbot is open
function showModeSwitcher() {
    const switcher = document.getElementById('chatbot-mode-switcher');
    if (switcher) {
        switcher.classList.add('show');
    }
}

// Hide mode switcher when no chatbot is open
function hideModeSwitcher() {
    const switcher = document.getElementById('chatbot-mode-switcher');
    if (switcher) {
        switcher.classList.remove('show');
    }
}

// Override chatbot open/close methods to show/hide mode switcher
document.addEventListener('DOMContentLoaded', function() {
    // Wait for chatbots to be initialized
    setTimeout(() => {
        if (window.chatbot) {
            const originalOpen = window.chatbot.open.bind(window.chatbot);
            const originalClose = window.chatbot.close.bind(window.chatbot);
            
            window.chatbot.open = function() {
                originalOpen();
                if (currentChatbotMode === 'faq') {
                    showModeSwitcher();
                }
            };
            
            window.chatbot.close = function() {
                originalClose();
                hideModeSwitcher();
            };
        }
        
        if (window.chatbotAI) {
            const originalOpen = window.chatbotAI.open.bind(window.chatbotAI);
            const originalClose = window.chatbotAI.close.bind(window.chatbotAI);
            
            window.chatbotAI.open = function() {
                originalOpen();
                if (currentChatbotMode === 'ai') {
                    showModeSwitcher();
                }
            };
            
            window.chatbotAI.close = function() {
                originalClose();
                hideModeSwitcher();
            };
        }
    }, 1000);
});
</script>
<?php endif; ?>
