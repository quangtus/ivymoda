<?php
/**
 * Chatbot AI Widget - UC3.47
 * Chatbot tư vấn sản phẩm với Gemini AI
 */
?>

<!-- Chatbot AI Widget Container -->
<div id="chatbot-ai-container"></div>

<!-- Chatbot AI CSS -->
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/chatbot-ai.css">

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Chatbot AI JavaScript -->
<script>
    // Chatbot AI Configuration
    window.chatbotAIConfig = {
        baseUrl: '<?= BASE_URL ?>',
        ajaxUrl: '<?= BASE_URL ?>ajax/chatbot_ajax.php',
        userId: <?= isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'null' ?>,
        position: 'bottom-right',
        autoOpen: false,
        autoOpenDelay: 0,
        theme: 'light',
        welcomeMessage: 'Xin chào! Tôi là trợ lý AI của IVY Moda. Tôi có thể giúp bạn tìm kiếm và tư vấn sản phẩm phù hợp. Hãy cho tôi biết bạn đang tìm kiếm gì? 😊',
        maxMessageLength: 500,
        responseTimeout: 30000,
        showSuggestedProducts: true,
        maxSuggestedProducts: 5
    };
</script>
<script src="<?= BASE_URL ?>assets/js/chatbot-ai.js"></script>

<script>
    // Initialize Chatbot AI when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Create global chatbot instance
        window.chatbotAI = new ChatbotAI(window.chatbotAIConfig);
        
        console.log('✅ Chatbot AI initialized successfully');
    });
</script>
