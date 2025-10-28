document.addEventListener('DOMContentLoaded', function() {
    // Làm menu có thể click được
    const userMenuTriggers = document.querySelectorAll('.user-menu-trigger');

    userMenuTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;

            // Toggle hiển thị submenu
            if (submenu.style.display === 'block') {
                submenu.style.display = 'none';
            } else {
                // Ẩn tất cả các submenu khác
                document.querySelectorAll('.submenu').forEach(menu => {
                    menu.style.display = 'none';
                });

                submenu.style.display = 'block';
            }
        });
    });

    // Đóng menu khi click ra ngoài
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.top-menu-icons li')) {
            document.querySelectorAll('.submenu').forEach(menu => {
                menu.style.display = '';
            });
        }
    });
});