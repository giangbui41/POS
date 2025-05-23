<?php
require_once 'backend/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Kết nối DB
$con = mysqli_connect("localhost", "root", "", "QUANLYBANHANG");
if (!$con) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Lấy dữ liệu từ session
$products = $_SESSION['productItems'] ?? [];
$phone = $_SESSION['cphone'] ?? '';
$paymentMode = $_SESSION['payment_mode'] ?? 'Chưa chọn';
$manv = $_SESSION['manv'];
$totalAmount = 0;
$customer_name = $_SESSION['name'] ?? ''; // Lấy tên từ session
$customer_address = $_SESSION['address'] ?? ''; // Lấy địa chỉ từ session
//$invoice_no = $_SESSION['invoice_no'] ?? generateUniqueInvoiceNo($con);

$sql_check_customer = "SELECT MAKH FROM khachhang WHERE SDT = '$phone'";
$result_check_customer = $con->query($sql_check_customer);

if (!($row_check_customer = $result_check_customer->fetch_assoc())) {
    // Không tìm thấy khách hàng, tiến hành insert với thông tin đã lấy
    // Lấy MAKH lớn nhất hiện có và cộng thêm 1
    $sql_max_makh = "SELECT MAX(MAKH) AS max_makh FROM khachhang";
    $result_max_makh = $con->query($sql_max_makh);
    $row_max_makh = $result_max_makh->fetch_assoc();
    $next_makh = ($row_max_makh['max_makh'] !== null) ? $row_max_makh['max_makh'] + 1 : 1;

    $sql_insert_customer = "INSERT INTO khachhang (MAKH, HOTEN, SDT, DIACHI) VALUES ('$next_makh', '$customer_name', '$phone', '$customer_address')";
    if ($con->query($sql_insert_customer) === TRUE) {
        $customer_id = $next_makh; // Sử dụng $next_makh làm customer_id
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi tạo khách hàng mới: " . $con->error]);
        return;
    }
} else {
    // Tìm thấy khách hàng, lấy customer_id từ kết quả truy vấn
    $customer_id = $row_check_customer['MAKH'];
}

// Tính tổng tiền
foreach ($products as $p) {
    $qty = intval($p['qty'] ?? $p['quantity']);
    $price = floatval($p['price']);
    $totalAmount += $qty * $price;
}

// Lấy thông tin khách hàng
$resultKH = mysqli_query($con, "SELECT MAKH FROM khachhang WHERE SDT='$phone' LIMIT 1");
if (!$resultKH || mysqli_num_rows($resultKH) == 0) {
    die("Không tìm thấy khách hàng.");
}
$customer = mysqli_fetch_assoc($resultKH);
$makh = $customer['MAKH'];

// Hàm tạo MAHD duy nhất
function generateUniqueInvoiceNo($con) {
    do {
        $invoiceNo = 'HD-' . rand(111111, 999999);
        $check = mysqli_query($con, "SELECT 1 FROM hoadon WHERE MAHD = '$invoiceNo' LIMIT 1");
    } while (mysqli_num_rows($check) > 0);

    return $invoiceNo;
}

// Dùng lại MAHD nếu đã có từ trước (saveOrder), nếu không thì tạo mới
$invoiceNo = $_SESSION['invoice_no'] ?? generateUniqueInvoiceNo($con);

// Lưu lại vào session nếu vừa tạo mới
$_SESSION['invoice_no'] = $invoiceNo;

// Thêm hóa đơn nếu chưa có
$checkHD = mysqli_query($con, "SELECT 1 FROM hoadon WHERE MAHD = '$invoiceNo' LIMIT 1");
if (mysqli_num_rows($checkHD) == 0) {
    $ngaylap = date('Y-m-d');
    mysqli_query($con, "INSERT INTO hoadon (MAHD, MAKH, MANV, NGAYTAO, TONGTIEN, PHUONGTHUC) 
        VALUES ('$invoiceNo','$makh','$manv', '$ngaylap', '$totalAmount', '$paymentMode')");
}

// Lưu chi tiết hóa đơn (có thể xoá chi tiết cũ nếu muốn cập nhật lại)
foreach ($products as $p) {
    $masp = $p['id']; // hoặc $p['masp']
    $qty = intval($p['qty'] ?? $p['quantity']);
    $price = floatval($p['price']);
    $id = rand(11111,99999);
    mysqli_query($con, "INSERT INTO cthd (ID, MAHD, MASP, SOLUONG, DONGIA, TONGTIEN) 
        VALUES ('$id', '$invoiceNo', '$masp', '$qty', '$price', '$totalAmount')");
}

// Tạo PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->set_option('defaultFont', 'DejaVu Sans');

// Bắt đầu lấy nội dung HTML
ob_start();
include 'invoice_template.php'; // Gửi vào template
$html = ob_get_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Tạo thư mục nếu chưa có
$invoiceFolder = __DIR__ . '/invoice';
if (!is_dir($invoiceFolder)) {
    mkdir($invoiceFolder, 0777, true);
}

// Lưu PDF
$pdfFilePath = $invoiceFolder . '/' . $invoiceNo . '.pdf';
file_put_contents($pdfFilePath, $dompdf->output());

// Xoá session liên quan sau khi hoàn tất
unset($_SESSION['invoice_no']);
unset($_SESSION['productItems']);
unset($_SESSION['cphone']);
unset($_SESSION['payment_mode']);

// Thông báo
echo "Hóa đơn đã được lưu tại: $invoiceNo.pdf<br>";
echo "<a href='saler/xulygiaodich' class='btn btn-primary float-end'>Quay lại Tạo giao dịch</a>";
?>
