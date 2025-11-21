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
                <form id="register-form" onsubmit="return validateEmailSubscribe(this);">
                    <input id="email-subscribe" type="email" placeholder="Nhập địa chỉ Email" required>
                    <input id="btn-submit" type="submit" value="Đăng ký">
                </form>
            </div>
        </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> IVY moda All rights reserved</p>
            </div>
        </div>
    </footer>
    
    <!-- Include Unified Chatbot Widget (FAQ + AI - UC3.47 & UC3.48) -->
    <?php include ROOT_PATH . 'app/views/shared/frontend/chatbot-unified.php'; ?>
    
    <!-- Expose base URLs to JS and verify footer inclusion -->
    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
        window.ASSETS_URL = '<?= ASSETS_URL ?>';
        console.log('✅ Footer loaded', { href: window.location.href, BASE_URL: window.BASE_URL, ASSETS_URL: window.ASSETS_URL });
    </script>

    <!-- JavaScript -->
    <script src="<?= ASSETS_URL ?>js/script.js?v=1"></script>
    <script src="<?= ASSETS_URL ?>js/slider.js?v=9"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sửa tất cả các liên kết thiếu phần /public/
        const allLinks = document.querySelectorAll('a[href^="/ivymoda/ivymoda_mvc/"]');
        allLinks.forEach(link => {
            if (!link.href.includes('/public/')) {
                link.href = link.href.replace('/ivymoda/ivymoda_mvc/', '/ivymoda/ivymoda_mvc/public/');
            }
        });
    });
    
    // Validate search form - không cho phép tìm kiếm với từ khóa rỗng
    function validateSearch(form) {
        const searchInput = form.querySelector('input[name="q"]');
        if (searchInput) {
            const keyword = searchInput.value.trim();
            if (keyword === '') {
                alert('Vui lòng nhập từ khóa tìm kiếm');
                searchInput.focus();
                return false;
            }
            if (keyword.length < 1) {
                alert('Từ khóa tìm kiếm phải có ít nhất 1 ký tự');
                searchInput.focus();
                return false;
            }
        }
        return true;
    }
    
    // Validate email subscribe form
    function validateEmailSubscribe(form) {
        const emailInput = document.getElementById('email-subscribe');
        if (emailInput) {
            const email = emailInput.value.trim();
            if (email === '') {
                alert('Vui lòng nhập địa chỉ email');
                emailInput.focus();
                return false;
            }
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Vui lòng nhập địa chỉ email hợp lệ');
                emailInput.focus();
                return false;
            }
        }
        return true;
    }
    </script>
</body>
</html>