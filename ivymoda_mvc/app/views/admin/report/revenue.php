<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\views\admin\report\revenue.php

require_once ROOT_PATH . 'app/views/shared/admin/header.php';
require_once ROOT_PATH . 'app/views/shared/admin/sidebar.php';
?>

<div class="admin-content-right">
    <div class="admin-content-right-main">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Báo cáo doanh thu</h1>
            </div>

            <?php if (isset($_SESSION['report_error'])): ?>
                <div class="alert alert-danger" style="padding: 10px; margin-bottom: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
                    <?= htmlspecialchars($_SESSION['report_error']) ?>
                    <?php unset($_SESSION['report_error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="get" action="" class="report-filter-form" onsubmit="return validateReportDates(this);">
                <input type="hidden" name="url" value="admin/report/revenue" />
                <div>
                    <label>Kiểu</label>
                    <select name="type" id="report_type">
                        <option value="day" <?= $type==='day'?'selected':'' ?>>Ngày</option>
                        <option value="month" <?= $type==='month'?'selected':'' ?>>Tháng</option>
                        <option value="year" <?= $type==='year'?'selected':'' ?>>Năm</option>
                    </select>
                </div>
                <div id="date_from_div" <?= $type==='day'?'':'style="display:none"' ?>>
                    <label>Từ ngày</label>
                    <input type="date" name="from" id="date_from" value="<?= htmlspecialchars($from) ?>" />
                    <small id="date_error" style="color: #dc3545; display: none;">Ngày bắt đầu không được lớn hơn ngày kết thúc</small>
                </div>
                <div id="date_to_div" <?= $type==='day'?'':'style="display:none"' ?>>
                    <label>Đến ngày</label>
                    <input type="date" name="to" id="date_to" value="<?= htmlspecialchars($to) ?>" />
                </div>
                <div <?= $type!=='day'?'':'style="display:none"' ?>>
                    <label>Năm</label>
                    <input type="number" name="year" min="2000" max="2100" value="<?= (int)$year ?>" />
                </div>
                <div <?= $type==='month'?'':'style="display:none"' ?>>
                    <label>Tháng</label>
                    <input type="number" name="month" min="1" max="12" value="<?= (int)$month ?>" />
                </div>
                <div>
                    <button type="submit">Lọc</button>
                </div>
            </form>

            <div class="row report-cards">
                <div class="card report-card">
                    <div>Doanh thu hôm nay</div>
                    <strong><?= number_format((float)$summary['today']) ?> ₫</strong>
                </div>
                <div class="card report-card">
                    <div>Doanh thu tháng này</div>
                    <strong><?= number_format((float)$summary['this_month']) ?> ₫</strong>
                </div>
                <div class="card report-card">
                    <div>Doanh thu năm nay</div>
                    <strong><?= number_format((float)$summary['this_year']) ?> ₫</strong>
                </div>
            </div>

            <div class="card shadow-sm report-chart">
                <canvas id="revenueChart" height="120"></canvas>
            </div>

            <div class="card shadow-sm report-table">
                <h3>Bảng dữ liệu</h3>
                <table border="1" cellpadding="8" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th><?= $type==='year'?'Tháng':'Ngày' ?></th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tableRows)) { foreach ($tableRows as $r) { ?>
                            <tr>
                                <td><?= htmlspecialchars($r['label']) ?></td>
                                <td><?= number_format((float)$r['revenue']) ?> ₫</td>
                            </tr>
                        <?php } } else { ?>
                            <tr><td colspan="2">Không có dữ liệu</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Validate report dates - from must be <= to
    function validateReportDates(form) {
        const type = document.getElementById('report_type').value;
        
        // Only validate when type is 'day'
        if (type === 'day') {
            const fromDate = document.getElementById('date_from').value;
            const toDate = document.getElementById('date_to').value;
            const errorMsg = document.getElementById('date_error');
            
            if (fromDate && toDate) {
                if (new Date(fromDate) > new Date(toDate)) {
                    errorMsg.style.display = 'block';
                    document.getElementById('date_from').focus();
                    return false;
                } else {
                    errorMsg.style.display = 'none';
                }
            }
        }
        
        return true;
    }
    
    // Real-time validation when dates change
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('report_type');
        const fromInput = document.getElementById('date_from');
        const toInput = document.getElementById('date_to');
        const errorMsg = document.getElementById('date_error');
        const dateFromDiv = document.getElementById('date_from_div');
        const dateToDiv = document.getElementById('date_to_div');
        
        // Show/hide date inputs based on type
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                if (this.value === 'day') {
                    dateFromDiv.style.display = '';
                    dateToDiv.style.display = '';
                } else {
                    dateFromDiv.style.display = 'none';
                    dateToDiv.style.display = 'none';
                    errorMsg.style.display = 'none';
                }
            });
        }
        
        // Real-time validation
        if (fromInput && toInput) {
            function checkDates() {
                if (typeSelect.value === 'day' && fromInput.value && toInput.value) {
                    if (new Date(fromInput.value) > new Date(toInput.value)) {
                        errorMsg.style.display = 'block';
                    } else {
                        errorMsg.style.display = 'none';
                    }
                }
            }
            
            fromInput.addEventListener('change', checkDates);
            toInput.addEventListener('change', checkDates);
        }
    });
    
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const labels = <?= json_encode(array_values($chartLabels)) ?>;
    const data = <?= json_encode(array_values($chartValues)) ?>;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (₫)',
                data: data,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php require_once ROOT_PATH . 'app/views/shared/admin/footer.php'; ?>


