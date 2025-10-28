<?php
// filepath: C:\xampp\htdocs\ivymoda\ivymoda_mvc\app\controllers\admin\ReportController.php
namespace admin;

class ReportController extends \Controller {
    private $reportModel;

    public function __construct() {
        // Yêu cầu đăng nhập admin
        if(!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 3)) {
            $this->redirect('admin/auth/login');
            exit;
        }

        $this->reportModel = $this->model('ReportModel');
    }

    // Trang thống kê doanh thu (ngày/tháng/năm) + biểu đồ
    public function revenue() {
        $type = isset($_GET['type']) ? $_GET['type'] : 'day'; // day | month | year
        $today = date('Y-m-d');
        $currentMonth = date('m');
        $currentYear = date('Y');

        $from = isset($_GET['from']) ? $_GET['from'] : $today;
        $to = isset($_GET['to']) ? $_GET['to'] : $today;
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)$currentYear;
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)$currentMonth;

        $summary = [
            'today' => $this->reportModel->getTotalRevenue($today, $today),
            'this_month' => $this->reportModel->getTotalRevenue(date('Y-m-01'), date('Y-m-t')),
            'this_year' => $this->reportModel->getTotalRevenue($currentYear . '-01-01', $currentYear . '-12-31')
        ];

        $chartLabels = [];
        $chartValues = [];
        $tableRows = [];

        if ($type === 'day') {
            // Doanh thu theo ngày trong một khoảng (from-to)
            $from = $from ?: $today;
            $to = $to ?: $today;
            $rows = $this->reportModel->getDailyRevenue($from, $to);
            foreach ($rows as $row) {
                $label = is_object($row) ? $row->ngay : $row['ngay'];
                $value = (float)(is_object($row) ? $row->doanh_thu : $row['doanh_thu']);
                $chartLabels[] = $label;
                $chartValues[] = $value;
                $tableRows[] = ['label' => $label, 'revenue' => $value];
            }
        } elseif ($type === 'month') {
            // Doanh thu theo từng ngày trong tháng
            $rows = $this->reportModel->getDailyRevenueForMonth($year, $month);
            foreach ($rows as $row) {
                $label = is_object($row) ? $row->day : $row['day'];
                $value = (float)(is_object($row) ? $row->revenue : $row['revenue']);
                $chartLabels[] = (string)$label;
                $chartValues[] = $value;
                $tableRows[] = ['label' => (string)$label, 'revenue' => $value];
            }
        } else { // year
            // Doanh thu theo tháng trong năm
            $rows = $this->reportModel->getMonthlyRevenue($year);
            foreach ($rows as $row) {
                $label = (int)(is_object($row) ? $row->month : $row['month']);
                $value = (float)(is_object($row) ? $row->revenue : $row['revenue']);
                $chartLabels[] = 'Tháng ' . $label;
                $chartValues[] = $value;
                $tableRows[] = ['label' => 'Tháng ' . $label, 'revenue' => $value];
            }
        }

        $data = [
            'title' => 'Báo cáo doanh thu',
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'year' => $year,
            'month' => $month,
            'summary' => $summary,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'tableRows' => $tableRows
        ];

        $this->view('admin/report/revenue', $data);
    }

    // Trang sản phẩm bán chạy (lọc theo ngày/tháng/năm)
    public function topSelling() {
        $type = isset($_GET['type']) ? $_GET['type'] : 'day';
        $today = date('Y-m-d');
        $from = isset($_GET['from']) ? $_GET['from'] : $today;
        $to = isset($_GET['to']) ? $_GET['to'] : $today;
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

        if ($type === 'day') {
            $fromDate = $from;
            $toDate = $to;
        } elseif ($type === 'month') {
            $fromDate = sprintf('%04d-%02d-01', $year, $month);
            $toDate = date('Y-m-t', strtotime($fromDate));
        } else { // year
            $fromDate = sprintf('%04d-01-01', $year);
            $toDate = sprintf('%04d-12-31', $year);
        }

        $products = $this->reportModel->getTopSellingProducts($limit, $fromDate, $toDate);

        $data = [
            'title' => 'Sản phẩm bán chạy',
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'year' => $year,
            'month' => $month,
            'limit' => $limit,
            'products' => $products
        ];

        $this->view('admin/report/top_selling', $data);
    }
}


