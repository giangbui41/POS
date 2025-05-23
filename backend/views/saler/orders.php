<?php
    // saler/orders.php

    include('backend/core/function.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productItems'])) {
        $_SESSION['productItems'] = json_decode($_POST['productItems'], true);
        $_SESSION['cphone'] = validate($_POST['cphone']);
        $_SESSION['payment_mode'] = validate($_POST['payment_mode']);
        $_SESSION['name'] = validate($_POST['name']);
        $_SESSION['address'] = validate($_POST['address']);
        $invoice_no = $_SESSION['invoice_no'] ?? generateUniqueInvoiceNo($con);
    
        header("Location: saler/orders");
        exit();
    }

    function generateUniqueInvoiceNo($con) {
        do {
            $invoice_no = 'HD-' . rand(111111, 999999);
            $check = mysqli_query($con, "SELECT 1 FROM hoadon WHERE MAHD = '$invoice_no' LIMIT 1");
        } while (mysqli_num_rows($check) > 0);
    
        return $invoice_no;
    }    
    $invoice_no = $_SESSION['invoice_no'] ?? generateUniqueInvoiceNo($con);
    $_SESSION['invoice_no'] = $invoice_no;

    if (isset($_POST['proceedToPlaceBtn'])) {
        $name = validate($_POST['name']);
        $address = validate($_POST['address']);
        $phone = validate($_POST['cphone']);
        $payment_mode = validate($_POST['payment_mode']);
        //$invoice_no = $_SESSION['invoice_no'] ?? generateUniqueInvoiceNo($con);

        $checkCustomer = mysqli_query($con, "SELECT * FROM khachhang WHERE SDT='$phone' LIMIT 1");
        if ($checkCustomer) {
            if (mysqli_num_rows($checkCustomer) > 0) {
                // Đã có khách hàng
                $_SESSION['invoice_no'] = $invoice_no;
                $_SESSION['cphone'] = $phone;
                $_SESSION['payment_mode'] = $payment_mode;
                jsonResponse(200, 'success', 'Đã tìm thấy khách hàng');
            } else {
                // Chưa có khách hàng, tiến hành thêm mới
                $newCustomerData = [
                    'HOTEN' =>$name ,
                    'SDT' => $phone,
                    'DIACHI' => $address
                ];
                $insertCustomer = insert('khachhang', $newCustomerData);
                if (!$insertCustomer) {
                    jsonResponse(500, 'error', 'Không thể thêm khách hàng mới!');
                }

                //$_SESSION['invoice_no'] = "HD-" . rand(111111, 999999);
                $_SESSION['cphone'] = $phone;

                $_SESSION['payment_mode'] = $payment_mode;
}
        }
        exit();
    }
    
    if (isset($_POST['saveOrder'])) {
        $name = validate($_POST['name']);
        $address = validate($_POST['address']);
        $phone = validate($_POST['cphone']);
        $payment_mode = validate($_POST['payment_mode']);
        $manv = validate($_SESSION['manv']);
        //$invoice_no = 'HD-' . rand(111111, 999999);
    
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
    
        if (!isset($_SESSION['productItems']) || empty($_SESSION['productItems'])) {
            jsonResponse(404, 'warning', 'Không có sản phẩm được thêm vào hóa đơn!');
        }
    
        $sessionProducts = $_SESSION['productItems'];
        $totalAmount = 0;
        foreach ($sessionProducts as $row) {
            $totalAmount += floatval($row['price']) * intval($row['qty'] ?? $row['quantity']);
        }
    
        $data = [
            'MAHD' => $invoice_no,
            'MAKH' => $customer_id,
            'MANV' => $manv,
            'NGAYTAO' => date('Y-m-d'),
            'TONGTIEN' => $totalAmount,
            'PHUONGTHUC' => $payment_mode,
        ];
        $result = insert('hoadon', $data);
    
        foreach ($sessionProducts as $row) {
            $productName = $row['name'];
            $price = floatval($row['price']);
            $quantity = intval($row['qty'] ?? $row['quantity']);
    
            $productQuery = mysqli_query($con, "SELECT MASP FROM sanpham WHERE TENSP = '".mysqli_real_escape_string($con, $productName)."' LIMIT 1");
            if (mysqli_num_rows($productQuery) > 0) {
                $productData = mysqli_fetch_assoc($productQuery);
                $masp = $productData['MASP'];
            } else {
                continue;
            }
    
            $dataOrderItem = [
                'ID' => rand(11111, 99999),
                'MAHD' => $invoice_no,
                'MASP' => $masp,
                'DONGIA' => $price,
                'SOLUONG' => $quantity,
                'TONGTIEN' => $price * $quantity,
            ];
            $insertItem = insert('cthd', $dataOrderItem);
        }
    
        unset($_SESSION['productItemIds']);
        unset($_SESSION['productItems']);
        unset($_SESSION['cphone']);
        unset($_SESSION['payment_mode']);
        unset($_SESSION['invoice_no']);
    
        ob_end_clean(); // Xóa mọi dữ liệu HTML đã sinh ra trước đó
        echo json_encode([
            'status' => 200,
            'message' => 'Lưu hóa đơn thành công'
        ]);
        exit;
    }
    
    
?>

<section class="content-header">
    <h3>Tạo hóa đơn</h3>
</section>
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"></h4>
                    <a href="saler/xulygiaodich" class="btn btn-primary float-end">Quay lại Tạo giao dịch</a>
                </div>
                <div class="card-body">
                    <?php alertMessage(); ?>

                    <div id="myBillingArea">
                        <?php
                        if (isset($_SESSION['cphone'])) {
                            $name = validate($_SESSION['name']);
                            $phone = validate($_SESSION['cphone']);
                            $invoiceNo = $_SESSION['invoice_no'];

                            $customerQuery = mysqli_query($con, "SELECT * FROM khachhang WHERE SDT='$phone' LIMIT 1");
                            if ($customerQuery) {
                                if (mysqli_num_rows($customerQuery) > 0) {
                                    $cRowData = mysqli_fetch_assoc($customerQuery);
                                    ?>
                                    <table style="width: 100%; margin-bottom: 20px">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center;">
                                                    <h4 style="font-size: 23px; line-height: 30px; margin: 2px; padding: 0">Công ty TNHH Intronix</h4>
                                                    <p style="font-size: 16px; line-height: 24px; margin: 2px; padding: 0">19, Nguyễn Hữu Thọ, P.Tân Hưng, Q7, TP.Hồ Chí Minh</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h5 style="font-size: 20px; line-height: 30px; margin: 8px; padding: 0">Thông tin Khách hàng</h4>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Tên Khách hàng: <?= $cRowData['HOTEN'] ?> </p>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Sđt Khách hàng: <?= $cRowData['SDT'] ?></p>
                                                </td>
                                                <td align="end">
                                                    <h5 style="font-size: 23px; line-height: 30px; margin: 2px; padding: 0">Chi tiết Hóa đơn</h4>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Số HĐ: <?= $invoiceNo ?> </p>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Ngày lập HĐ: <?= date('d M Y') ?> </p>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Phương thức thanh toán: <?= $_SESSION['payment_mode'] ?? 'Chưa chọn'; ?></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php
                                } else {
                                    ?>
                                    <table style="width: 100%; margin-bottom: 20px">
                                        <tbody>
                                            <tr>
                                                <td style="text-align: center;">
                                                    <h4 style="font-size: 23px; line-height: 30px; margin: 2px; padding: 0">Công ty TNHH Intronix</h4>
                                                    <p style="font-size: 16px; line-height: 24px; margin: 2px; padding: 0">19, Nguyễn Hữu Thọ, P.Tân Hưng, Q7, TP.Hồ Chí Minh</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <h5 style="font-size: 20px; line-height: 30px; margin: 8px; padding: 0">Thông tin Khách hàng</h4>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Tên Khách hàng: <?= $name?> </p>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Sđt Khách hàng: <?= $phone?></p>
                                                </td>
                                                <td align="end">
                                                    <h5 style="font-size: 23px; line-height: 30px; margin: 2px; padding: 0">Chi tiết Hóa đơn</h4>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Số HĐ: <?= $invoiceNo ?> </p>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Ngày lập HĐ: <?= date('d M Y') ?> </p>
                                                    <p style="font-size: 14px; line-height: 20px; margin: 0px; padding: 0">Phương thức thanh toán: <?= $_SESSION['payment_mode'] ?? 'Chưa chọn'; ?></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php
                                }
                            }
                        }
                        ?>

                        <?php
                        if (isset($_SESSION['productItems'])) {
                            $sessionProducts = $_SESSION['productItems'];
                            ?>
                            <div class="table-responsive mb-3">
                                <table style="width: 100%;" cellpadding="5">
                                    <thead>
                                        <tr>
                                            <th align="start" style="border-bottom: 1px solid #ccc" width="5%">Mã</th>
                                            <th align="start" style="border-bottom: 1px solid #ccc">Tên SP</th>
                                            <th align="start" style="border-bottom: 1px solid #ccc" width="10%">Giá</th>
                                            <th align="start" style="border-bottom: 1px solid #ccc" width="10%">Số lượng</th>
                                            <th align="start" style="border-bottom: 1px solid #ccc" width="10%">Tổng tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $totalAmount = 0;

                                        foreach ($sessionProducts as $key => $row) :
                                            $totalAmount += floatval($row['price']) * intval($row['qty'] ?? $row['quantity']);
                                            ?>
                                            <tr>
                                                <td style="border-bottom: 1px solid #ccc"><?= $i++; ?></td>
                                                <td style="border-bottom: 1px solid #ccc"><?= $row['name']; ?></td>
                                                <td style="border-bottom: 1px solid #ccc"><?= number_format($row['price'], 0) ?></td>
                                                <td style="border-bottom: 1px solid #ccc"><?= $row['qty'] ?? $row['quantity'] ?></td>
                                                <td style="border-bottom: 1px solid #ccc" class="fw-bold">
                                                    <?= number_format(floatval($row['price']) * intval($row['qty'] ?? $row['quantity']), 0) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="4" align="end" style="font-weight: bold">Tổng cộng: </td>
                                            <td colspan="1" style="font-weight: bold"><?= number_format($totalAmount, 0); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        } else {
                            echo '<h5 class="text-center">Hóa đơn đang trống, vui lòng quay lại tạo giao dịch </h5>';
                        }
                        ?>
                    </div>

                    <div class="mt-4 text-end">
                        <?php if (isset($_SESSION['productItems']) && !empty($_SESSION['productItems']) && isset($_SESSION['cphone']) && isset($_SESSION['payment_mode'])) : ?>
                            <form action="" method="POST">
                                <input type="hidden" name="invoice_no" value="<?= $_SESSION['invoice_no'] ?? ''; ?>">
                                <input type="hidden" name="cphone" value="<?= $_SESSION['cphone'] ?? ''; ?>">
                                <input type="hidden" name="payment_mode" value="<?= $_SESSION['payment_mode'] ?? ''; ?>">
                                <button type="submit" name="saveOrder" class="btn btn-success px-4 mx-1" id="saveOrderBtn">Lưu hóa đơn</button>
                            </form>
                            <form action="saler/export_invoice" method="post">
                                <button type="submit" class="btn btn-primary"> Xuất PDF Hóa đơn</button>
                            </form>
                        <?php else : ?>
                            <button type="button" class="btn btn-success px-4 mx-1" disabled>Lưu hóa đơn</button>
                            <form action="saler/export_invoice" method="post">
                                <button type="submit" class="btn btn-primary"> Xuất PDF Hóa đơn</button>
                            </form>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ?>

<style>
    .card-body {
        background-color: #e3f2fd;
        border-radius: 10px;
    }

    .content {
        background-color: white;
        color: black;
    }

    /* ===== BASE STYLES ===== */
    :root {
        --primary-color: #00008b;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --text-color: #2b2d42;
        --light-bg: #f8f9fa;
        --white: #ffffff;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12);
        --shadow-md: 0 4px 6px rgba(0,0, 0, 0.1);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* ===== MAIN LAYOUT ===== */
    .content {
        padding: 2rem;
        min-height: calc(100vh - 100px);
        /* background: linear-gradient(135deg, var(--light-bg) 0%, #e9ecef 100%); */
        display: flex;
        flex-direction: column; /* Thay đổi hướng hiển thị các phần tử con theo cột */
        align-items: center;
        /* justify-content: center; */ /* Có thể bỏ hoặc điều chỉnh tùy theo ý muốn */
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="frontend/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/alertify.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/css/themes/default.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/AlertifyJS/1.13.1/alertify.min.js"></script>

<script>
    $(document).ready(function () {
        // Sự kiện click cho nút "Lưu hóa đơn"
        $(document).on('click', '#saveOrderBtn', function (e) {
            e.preventDefault(); // Ngăn chặn hành động submit mặc định của form

            $.ajax({
                type: "POST",
                url: 'saler/orders',
                data: {
                    'saveOrder': true,
                    'invoice_no': $('input[name="invoice_no"]').val(),
                    'cphone': $('input[name="cphone"]').val(),
                    'payment_mode': $('input[name="payment_mode"]').val()
                },
                dataType: "json", // Expect JSON response
                success: function (response) {
                    if (response.status == 200) {
                        Swal.fire(
                            'Thành công!',
                            response.message,
                            'success'
                        ).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "saler/view_orders";
                            }
                        });
                    } else {
                        Swal.fire(
                            'Lỗi!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function (xhr, status, error) {
                    
                    window.location.href = "saler/xulygiaodich";
                }
            });
        });
    });
</script>