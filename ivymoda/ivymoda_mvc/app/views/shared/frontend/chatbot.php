<?php
/**
 * Chatbot Widget Include - UC3.47 & UC3.48
 * Include cả chatbot AI và FAQ
 */
?>

<!-- Chatbot Scripts -->
<script src="<?= ROOT_URL ?>assets/js/chatbot.js"></script>
<script src="<?= ROOT_URL ?>assets/js/chatbot-ai.js"></script>

<!-- Chatbot Widget -->
<?php include ROOT_PATH . 'app/views/frontend/chatbot/widget.php'; ?>

<script>
// Initialize both chatbots when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for all scripts to load
    setTimeout(() => {
        // Initialize FAQ Chatbot (UC3.48)
        if (typeof ChatbotWidget !== 'undefined') {
            window.chatbot = new ChatbotWidget({
                position: 'bottom-right',
                autoOpen: false,
                autoOpenDelay: 0,
                maxFaqs: 10,
                searchMinLength: 2,
                showCategories: true,
                enableSearch: true,
                theme: 'light',
                welcomeMessage: 'Xin chào! Chọn câu hỏi bạn muốn hỏi:',
                baseUrl: '<?= ROOT_URL ?>'
            });
        }
        
        // Initialize AI Chatbot (UC3.47)
        if (typeof ChatbotAI !== 'undefined') {
            window.chatbotAI = new ChatbotAI({
                position: 'bottom-right',
                autoOpen: false,
                autoOpenDelay: 0,
                maxMessageLength: 500,
                responseTimeout: 30000,
                theme: 'light',
                welcomeMessage: 'Xin chào! Tôi có thể giúp gì cho bạn hôm nay? 😊',
                baseUrl: '<?= ROOT_URL ?>'
            });
        }
        
        // Set default mode to FAQ
        if (window.chatbot) {
            window.chatbot.open();
        }
    }, 1000);
});
</script>
