<?php
/**
 * Unified Chatbot Widget - UC3.47 & UC3.48
 * Kết hợp cả FAQ Chatbot và AI Chatbot trong 1 widget
 * Sử dụng tab switching để chuyển đổi giữa 2 chế độ
 */
?>

<!-- Unified Chatbot Widget Container -->
<div id="unified-chatbot-container"></div>

<!-- Unified Chatbot CSS -->
<link rel="stylesheet" href="<?= ASSETS_URL ?>css/chatbot-unified.css?v=7">

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Chatbot Configuration -->
<script>
    // Unified Chatbot Configuration
    window.unifiedChatbotConfig = {
        baseUrl: '<?= BASE_URL ?>',
        assetsUrl: '<?= ASSETS_URL ?>',
        ajaxUrl: '<?= BASE_URL ?>ajax/chatbot_ajax.php',
        userId: <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>,
        position: 'bottom-right',
        autoOpen: false,
        theme: 'light',
        defaultTab: 'faq', // 'faq' or 'ai'
        
        // FAQ Settings
        faq: {
            welcomeMessage: 'Xin chào! Chọn câu hỏi bạn muốn hỏi:',
            maxFaqs: 10,
            searchMinLength: 2,
            showCategories: true,
            enableSearch: true
        },
        
        // AI Settings
        ai: {
            welcomeMessage: 'Xin chào! Tôi có thể giúp gì cho bạn hôm nay? 😊',
            maxMessageLength: 500,
            responseTimeout: 60000,
            showSuggestedProducts: true,
            maxSuggestedProducts: 5
        }
    };
</script>

<!-- Unified Chatbot JavaScript -->
<script src="<?= ASSETS_URL ?>js/chatbot-unified.js?v=9"></script>

<script>
    // Initialize Unified Chatbot when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for all dependencies to load
        setTimeout(() => {
            if (typeof UnifiedChatbot !== 'undefined') {
                window.unifiedChatbot = new UnifiedChatbot(window.unifiedChatbotConfig);
                console.log('✅ Unified Chatbot initialized successfully');
            } else {
                console.error('❌ UnifiedChatbot class not found');
            }
        }, 500);
    });
</script>
