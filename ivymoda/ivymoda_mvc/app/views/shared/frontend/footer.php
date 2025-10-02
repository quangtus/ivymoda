<?php
// filepath: c:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\shared\frontend\footer.php
?>
    <footer>
        <div class="footer-container">
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
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> IVY moda All rights reserved</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?= BASE_URL ?>assets/js/script.js"></script>
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
    </script>
</body>
</html>