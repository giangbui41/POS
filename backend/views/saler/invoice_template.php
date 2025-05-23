<?php

require_once 'backend/core/db.php'; // file kết nối CSDL


$con = mysqli_connect("localhost", "root", "", "QUANLYBANHANG");
if (!$con) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

$phone = $_SESSION['cphone'] ?? '';
if ($phone === '') {
    die("Không có số điện thoại khách hàng.");
}

// Lấy MAKH từ bảng khachhang
$resultKH = mysqli_query($con, "SELECT MAKH, HOTEN FROM khachhang WHERE SDT='$phone' LIMIT 1");
if (!$resultKH || mysqli_num_rows($resultKH) == 0) {
    die("Không tìm thấy khách hàng.");
}
$customer = mysqli_fetch_assoc($resultKH);
$makh = $customer['MAKH'];

if (!isset($_SESSION['invoice_no'])) {
    die("Không có thông tin hóa đơn.");
}


$invoiceNo = $_SESSION['invoice_no'] ?? 'invoice_' . time();

$name = $_SESSION['name'] ?? '';
$phone = $_SESSION['cphone'] ?? '';
$paymentMode = $_SESSION['payment_mode'] ?? 'Chưa chọn';

$customerData = ['HOTEN' => $name, 'SDT' => $phone];
$result = mysqli_query($con, "SELECT * FROM khachhang WHERE SDT='$phone' LIMIT 1");
if ($result && mysqli_num_rows($result) > 0) {
    $customerData = mysqli_fetch_assoc($result);
}

$totalAmount = 0;
$products = $_SESSION['productItems'] ?? [];
?>

<!-- bắt đầu HTML hóa đơn -->
<h2 style="text-align:center;">Công ty TNHH Intronix</h2>
<p style="text-align:center;">19 Nguyễn Hữu Thọ, P.Tân Hưng, Q.7, TP.HCM</p>
<hr>
<h3>Hóa đơn #<?= $invoiceNo ?></h3>
<p><strong>Khách hàng:</strong> <?= $customerData['HOTEN'] ?></p>
<p><strong>SĐT:</strong> <?= $customerData['SDT'] ?></p>
<p><strong>Ngày lập:</strong> <?= date('d/m/Y') ?></p>
<p><strong>Mã nhân viên: </strong><?= $manv ?></p>
<p><strong>Thanh toán:</strong> <?= $paymentMode ?></p>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>#</th><th>Tên SP</th><th>Giá</th><th>SL</th><th>Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $i => $p): 
            $qty = intval($p['qty'] ?? $p['quantity']);
            $lineTotal = floatval($p['price']) * $qty;
            $totalAmount += $lineTotal;
        ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= $p['name'] ?></td>
            <td><?= number_format($p['price'], 0) ?></td>
            <td><?= $qty ?></td>
            <td><?= number_format($lineTotal, 0) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4" align="right"><strong>Tổng cộng:</strong></td>
            <td><strong><?= number_format($totalAmount, 0) ?></strong></td>
        </tr>
    </tbody>
</table>

<style>
    body {
        font-family: DejaVu Sans, sans-serif;
    }
</style>

