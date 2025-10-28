<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\shared\frontend\footer.php
?>
    <footer>
        <div class="footer-container">
            <div class = "footer-line">
                <div class="app-container">
                    <p>Tải ứng dụng IVY moda</p>
                    <div class="app-google">
                        <img src="<?= ASSETS_URL ?>images/appstore.png" alt="">
                        <img src="<?= ASSETS_URL ?>images/googleplay.png" alt="">
                    </div>
                </div>
                <div class="footer-row">
                    <div class="footer-column">
                        <h3>Giới thiệu</h3>
                        <ul>
                            <li><a href="#">Về IVY moda</a></li>
                            <li><a href="#">Tuyển dụng</a></li>
                            <li><a href="#">Hệ thống cửa hàng</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Dịch vụ khách hàng</h3>
                        <ul>
                            <li><a href="#">Chính sách điều khoản</a></li>
                            <li><a href="#">Hướng dẫn mua hàng</a></li>
                            <li><a href="#">Chính sách thanh toán</a></li>
                            <li><a href="#">Chính sách đổi trả</a></li>
                            <li><a href="#">Chính sách bảo hành</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Liên hệ</h3>
                        <ul>
                            <li>Hotline: <a href="tel:0246662343">0246 662 3434</a></li>
                            <li>Email: <a href="mailto:support@ivymoda.com">support@ivymoda.com</a></li>
                        </ul>
                    </div>
                </div>
                <div class="news-update">
                <p>Nhận thông tin các chương trình của IVY moda</p>
                <form id="register-form">
                    <input id="email-subscribe" type="text" placeholder="Nhập địa chỉ Email">
                    <input id="btn-submit" type="submit" value="Đăng ký">
                </form>
            </div>
        </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> IVY moda All rights reserved</p>
            </div>
        </div>
    </footer>
    
    <!-- Chatbot CSS -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>css/chatbot.css">
    
    <!-- Include Chatbot Widget (FAQ - UC3.48) -->
    <?php include ROOT_PATH . 'app/views/shared/frontend/chatbot.php'; ?>
    
    <!-- Include Chatbot AI Widget (Tư vấn sản phẩm - UC3.47) -->
    <?php include ROOT_PATH . 'app/views/shared/frontend/chatbot-ai.php'; ?>
    
    <!-- Expose base URLs to JS and verify footer inclusion -->
    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
        window.ASSETS_URL = '<?= ASSETS_URL ?>';
        console.log('✅ Footer loaded', { href: window.location.href, BASE_URL: window.BASE_URL, ASSETS_URL: window.ASSETS_URL });
    </script>

    <!-- JavaScript -->
    <script src="<?= ASSETS_URL ?>js/script.js?v=1"></script>
    <script src="<?= ASSETS_URL ?>js/slider.js?v=8"></script>
    <script src="<?= ASSETS_URL ?>js/chatbot.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sửa tất cả các liên kết thiếu phần /public/
        const allLinks = document.querySelectorAll('a[href^="/ivymoda/ivymoda_mvc/"]');
        allLinks.forEach(link => {
            if (!link.href.includes('/public/')) {
                link.href = link.href.replace('/ivymoda/ivymoda_mvc/', '/ivymoda/ivymoda_mvc/public/');
            }
        });
        
        // Initialize chatbot
        if (typeof window.chatbotEnabled === 'undefined' || window.chatbotEnabled) {
            // Chatbot will auto-initialize from chatbot.js
        }
    });
    </script>
</body>
</html>