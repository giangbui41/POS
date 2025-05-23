<?php
class reportControl extends control {
    protected $reportModel;

    public function __construct() {

        $this->folder = "share";
        $this->reportModel = $this->model("reportModel");
    }

    public function index() {
        $this->salesReport();
    }

    public function salesReport() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['tk'])) {
            header('Location: /nhomhuongnoi/source/login');
            exit();
        }

        // Xử lý tham số thời gian
        $timeRange = $this->getTimeRange();
        list($startDate, $endDate) = $this->calculateDateRange($timeRange);

        // Lọc theo nhân viên: admin xem tất cả, nhân viên chỉ xem của mình
        $staffId = ($_SESSION['role'] === 'admin') ? null : ($_SESSION['manv'] ?? null);

        // Lấy dữ liệu
        $data = [
            'reportData' => $this->reportModel->getSalesReport($startDate, $endDate, $staffId),
            'orderList' => $this->reportModel->DSdonhang($startDate, $endDate, $staffId),
            'timeRange' => $timeRange,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'isAdmin' => ($_SESSION['role'] === 'admin'),
            'pageTitle' => 'Báo cáo bán hàng'
        ];

        $this->render('report', $data);
    }
    
    public function getOrderDetails() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['tk'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit();
        }
    
        if (empty($_GET['orderId'])) {
            echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin đơn hàng']);
            exit();
        }
    
        $orderId = $_GET['orderId'];
        
        // Lấy thông tin cơ bản đơn hàng
        $orderInfo = $this->reportModel->getOrderInfo($orderId);
        
        // Lấy chi tiết sản phẩm
        $orderItems = $this->reportModel->getOrderDetails($orderId);
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'orderDate' => $orderInfo['NGAYTAO'],
                'customerName' => $orderInfo['TENKH'],
                'items' => $orderItems
            ]
        ]);
    }

    public function getChartData() {
        header('Content-Type: application/json');
    
        if (!isset($_SESSION['tk'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit();
        }

        $startDate = $_GET['start'] ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $_GET['end'] ?? date('Y-m-d');

        // Thêm điều kiện lọc theo nhân viên nếu không phải admin
        $staffId = ($_SESSION['role'] !== 'admin') ? ($_SESSION['manv'] ?? null) : null;

        $data = $this->reportModel->getDailySalesData($startDate, $endDate, $staffId);
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    public function exportReport() {
        if (!isset($_SESSION['tk'])) {
        header('Location: /nhomhuongnoi/source/login');
        exit();
        }

        $startDate = $_GET['start'] ?? date('Y-m-d', strtotime('-7 days'));
        $endDate = $_GET['end'] ?? date('Y-m-d');

        // Thêm điều kiện lọc theo nhân viên nếu không phải admin
        $staffId = ($_SESSION['role'] !== 'admin') ? ($_SESSION['staffId'] ?? null) : null;

        $reportData = $this->reportModel->getSalesReport($startDate, $endDate, $staffId);
        $dailyData = $this->reportModel->getDailySalesData($startDate, $endDate, $staffId);
        $orderList = $this->reportModel->DSdonhang($startDate, $endDate, $staffId);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Bao_cao_ban_hang_' . $startDate . '_den_' . $endDate . '.xls"');
        
        // Tạo nội dung file Excel
        include 'views/reportExport.php';
        exit();
    }

    private function getTimeRange() {
        $validRanges = ['today', 'yesterday', '7days', 'thismonth', 'custom'];
        $range = $_GET['range'] ?? '7days';
        return in_array($range, $validRanges) ? $range : '7days';
    }

    private function calculateDateRange($range) {
        $today = date('Y-m-d');
        switch ($range) {
            case 'custom':
                $start = $_GET['start'] ?? date('Y-m-d', strtotime('-7 days'));
                $end = $_GET['end'] ?? $start; // Nếu không có end, dùng start thay vì today
                // Validate ngày
                $start = date('Y-m-d', strtotime($start));
                $end = date('Y-m-d', strtotime($end));
                if ($end < $start) {
                    $end = $start; // Đảm bảo end không nhỏ hơn start
                }
                return [$start, $end];
            default:
                return [date('Y-m-d', strtotime('-7 days')), $today];
        }
    }

    public function exportOrderPdf() {
        if (!isset($_SESSION['tk'])) {
            header('Location: /nhomhuongnoi/source/login');
            exit();
        }

        if (empty($_GET['orderId'])) {
            die('Thiếu thông tin đơn hàng');
        }

        $orderId = $_GET['orderId'];
        
        // Lấy thông tin đơn hàng
        $orderInfo = $this->reportModel->getOrderInfo($orderId);
        $orderItems = $this->reportModel->getOrderDetails($orderId);
        
        if (!$orderInfo || empty($orderItems)) {
            die('Không tìm thấy thông tin hóa đơn');
        }
        
        // Tính tổng tiền
        $total = 0;
        foreach ($orderItems as $item) {
            $total += $item['totalPrice'];
        }

        // Tạo nội dung PDF thủ công
        $pdfContent = $this->generatePdfContent($orderId, $orderInfo, $orderItems, $total);
        
        // Xuất file PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="Hoa_don_' . htmlspecialchars($orderId) . '.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($pdfContent));
        echo $pdfContent;
        exit();
    }

    private function generatePdfContent($orderId, $orderInfo, $orderItems, $total) {
        // ký tự tiếng Việt
        $trans = [
            'À'=>'A', 'Á'=>'A', 'Ả'=>'A', 'Ã'=>'A', 'Ạ'=>'A', 'Ă'=>'A', 'Ằ'=>'A', 'Ắ'=>'A', 'Ẳ'=>'A', 'Ẵ'=>'A', 'Ặ'=>'A',
            'Â'=>'A', 'Ầ'=>'A', 'Ấ'=>'A', 'Ẩ'=>'A', 'Ẫ'=>'A', 'Ậ'=>'A', 'Đ'=>'D', 'È'=>'E', 'É'=>'E', 'Ẻ'=>'E', 'Ẽ'=>'E',
            'Ẹ'=>'E', 'Ê'=>'E', 'Ề'=>'E', 'Ế'=>'E', 'Ể'=>'E', 'Ễ'=>'E', 'Ệ'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Ỉ'=>'I', 'Ĩ'=>'I',
            'Ị'=>'I', 'Ò'=>'O', 'Ó'=>'O', 'Ỏ'=>'O', 'Õ'=>'O', 'Ọ'=>'O', 'Ô'=>'O', 'Ồ'=>'O', 'Ố'=>'O', 'Ổ'=>'O', 'Ỗ'=>'O',
            'Ộ'=>'O', 'Ơ'=>'O', 'Ờ'=>'O', 'Ớ'=>'O', 'Ở'=>'O', 'Ỡ'=>'O', 'Ợ'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Ủ'=>'U', 'Ũ'=>'U',
            'Ụ'=>'U', 'Ư'=>'U', 'Ừ'=>'U', 'Ứ'=>'U', 'Ử'=>'U', 'Ữ'=>'U', 'Ự'=>'U', 'Ỳ'=>'Y', 'Ý'=>'Y', 'Ỷ'=>'Y', 'Ỹ'=>'Y',
            'Ỵ'=>'Y', 'à'=>'a', 'á'=>'a', 'ả'=>'a', 'ã'=>'a', 'ạ'=>'a', 'ă'=>'a', 'ằ'=>'a', 'ắ'=>'a', 'ẳ'=>'a', 'ẵ'=>'a',
            'ặ'=>'a', 'â'=>'a', 'ầ'=>'a', 'ấ'=>'a', 'ẩ'=>'a', 'ẫ'=>'a', 'ậ'=>'a', 'đ'=>'d', 'è'=>'e', 'é'=>'e', 'ẻ'=>'e',
            'ẽ'=>'e', 'ẹ'=>'e', 'ê'=>'e', 'ề'=>'e', 'ế'=>'e', 'ể'=>'e', 'ễ'=>'e', 'ệ'=>'e', 'ì'=>'i', 'í'=>'i', 'ỉ'=>'i',
            'ĩ'=>'i', 'ị'=>'i', 'ò'=>'o', 'ó'=>'o', 'ỏ'=>'o', 'õ'=>'o', 'ọ'=>'o', 'ô'=>'o', 'ồ'=>'o', 'ố'=>'o', 'ổ'=>'o',
            'ỗ'=>'o', 'ộ'=>'o', 'ơ'=>'o', 'ờ'=>'o', 'ớ'=>'o', 'ở'=>'o', 'ỡ'=>'o', 'ợ'=>'o', 'ù'=>'u', 'ú'=>'u', 'ủ'=>'u',
            'ũ'=>'u', 'ụ'=>'u', 'ư'=>'u', 'ừ'=>'u', 'ứ'=>'u', 'ử'=>'u', 'ữ'=>'u', 'ự'=>'u', 'ỳ'=>'y', 'ý'=>'y', 'ỷ'=>'y',
            'ỹ'=>'y', 'ỵ'=>'y'
        ];

        $replaceVietnamese = function ($text) use ($trans) {
            return strtr($text, $trans);
        };

        // Bắt đầu tạo nội dung PDF
        $content = "%PDF-1.4\n%âãÏÓ\n";
        
        // Danh sách các đối tượng
        $objects = [];
        $offsets = [];
        $pageIds = [];
        $contentIds = [];

        // Catalog
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        // Pages
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [] /Count 0 >>\nendobj\n";
        // Font
        $objects[] = "3 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>\nendobj\n";

        // Tạo nội dung trang
        $itemsPerPage = 20; // Số sản phẩm tối đa mỗi trang
        $pages = [];
        $currentPage = [];
        $yPos = 650;
        $pageIndex = 0;
        $itemIndex = 0;

        // Tiêu đề và thông tin hóa đơn
        $pageContent = "BT\n/F1 14 Tf\n100 800 Td\n(HOA DON BAN HANG) Tj\nET\n";
        $pageContent .= "BT\n/F1 10 Tf\n50 750 Td\n(Ma hoa don: " . htmlspecialchars($orderId) . ") Tj\nET\n";
        $pageContent .= "BT\n50 730 Td\n(Ngay: " . date('d/m/Y', strtotime($orderInfo['NGAYTAO'])) . ") Tj\nET\n";
        $pageContent .= "BT\n50 710 Td\n(Khach hang: " . $replaceVietnamese(htmlspecialchars($orderInfo['TENKH'] ?? 'Khach vang lai')) . ") Tj\nET\n";
        $pageContent .= "BT\n50 670 Td\n(STT  San pham                 So luong  Don gia    Thanh tien) Tj\nET\n";
        $pageContent .= "50 665 m\n450 665 l\nS\n";

        foreach ($orderItems as $index => $item) {
            if ($itemIndex >= $itemsPerPage) {
                // Lưu trang hiện tại
                $pages[$pageIndex] = $pageContent;
                $pageIndex++;
                $itemIndex = 0;
                $yPos = 650;
                $pageContent = "BT\n/F1 14 Tf\n100 800 Td\n(HOA DON BAN HANG - Trang " . ($pageIndex + 1) . ") Tj\nET\n";
                $pageContent .= "BT\n/F1 10 Tf\n50 750 Td\n(Ma hoa don: " . htmlspecialchars($orderId) . ") Tj\nET\n";
                $pageContent .= "BT\n50 730 Td\n(Ngay: " . date('d/m/Y', strtotime($orderInfo['NGAYTAO'])) . ") Tj\nET\n";
                $pageContent .= "BT\n50 710 Td\n(Khach hang: " . $replaceVietnamese(htmlspecialchars($orderInfo['TENKH'] ?? 'Khach vang lai')) . ") Tj\nET\n";
                $pageContent .= "BT\n50 670 Td\n(STT  San pham                 So luong  Don gia    Thanh tien) Tj\nET\n";
                $pageContent .= "50 665 m\n450 665 l\nS\n";
            }

            // Thêm sản phẩm
            $productName = strlen($item['productName']) > 20 ? substr($item['productName'], 0, 17) . '...' : $item['productName'];
            $productName = $replaceVietnamese(htmlspecialchars($productName));
            $line = sprintf("%-4s %-20s %-9s %-10s %-10s",
                           ($index + 1),
                           $productName,
                           $item['quantity'],
                           number_format($item['unitPrice']),
                           number_format($item['totalPrice']));
            
            $pageContent .= "BT\n50 " . $yPos . " Td\n(" . $line . ") Tj\nET\n";
            $yPos -= 20;
            $itemIndex++;
        }

        // Thêm tổng tiền vào trang cuối
        $pageContent .= "BT\n300 " . ($yPos - 20) . " Td\n(Tong cong: " . number_format($total) . ") Tj\nET\n";
        $pageContent .= "BT\n100 50 Td\n(Cam on quy khach da su dung dich vu!) Tj\nET\n";
        $pages[$pageIndex] = $pageContent;

        // Tạo các đối tượng Page và Content
        $objId = 4; // Bắt đầu từ ID 4 (1: Catalog, 2: Pages, 3: Font)
        foreach ($pages as $index => $pageContent) {
            // Page object
            $pageObj = "$objId 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 3 0 R >> >> /Contents " . ($objId + 1) . " 0 R /MediaBox [0 0 595 842] >>\nendobj\n";
            $objects[] = $pageObj;
            $pageIds[] = $objId;
            $objId++;

            // Content stream
            $stream = "$objId 0 obj\n<< /Length " . strlen($pageContent) . " >>\nstream\n" . $pageContent . "\nendstream\nendobj\n";
            $objects[] = $stream;
            $contentIds[] = $objId;
            $objId++;
        }

        // Cập nhật Pages object
        $kids = implode(' ', $pageIds) . ' 0 R';
        $objects[1] = "2 0 obj\n<< /Type /Pages /Kids [$kids] /Count " . count($pages) . " >>\nendobj\n";

        // Tạo nội dung PDF
        $content .= implode('', $objects);

        // Tạo xref
        $xrefOffset = strlen($content);
        $xref = "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
        $offset = strlen("%PDF-1.4\n%âãÏÓ\n");
        foreach ($objects as $obj) {
            $xref .= sprintf("%010d 00000 n \n", $offset);
            $offset += strlen($obj);
        }

        $trailer = "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF\n";
        $content .= $xref . $trailer;

        return $content;
    }
}