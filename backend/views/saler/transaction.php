<?php
include('backend/core/function.php');

?>

<?php

if (!isset($_SESSION['productItems'])) {
    $_SESSION['productItems'] = [];
}
if (!isset($_SESSION['productItemIds'])) {
    $_SESSION['productItemIds'] = [];
}

if (isset($_POST['addItem'])) {
    $productId = validate($_POST['product_id']);
    $quantity = validate($_POST['quantity']);

    $checkProduct = mysqli_query($con, "SELECT * FROM sanpham WHERE MASP='$productId' LIMIT 1");
    if ($checkProduct) {
        if (mysqli_num_rows($checkProduct) > 0) {
            $row = mysqli_fetch_assoc($checkProduct);
            if ($row['SOLUONG'] < $quantity) {
                redirect('/nhomhuongnoi/source/saler/transaction', 'Chỉ còn ' . $row['SOLUONG'] . ' sản phẩm!');
            }

            $productData = [
                'product_id' => $row['MASP'],
                'name' => $row['TENSP'],
                'image' => $row['ANHSP'],
                'price' => isset($row['GIABANLE']) ? $row['GIABANLE'] : 0,
                'quantity' => $quantity,
            ];

            if (!in_array($row['MASP'], $_SESSION['productItemIds'])) {
                array_push($_SESSION['productItemIds'], $row['MASP']);
                array_push($_SESSION['productItems'], $productData);
            } else {
                foreach ($_SESSION['productItems'] as $key => $prodSessionItem) {
                    if ($prodSessionItem['product_id'] == $row['MASP']) {

                        $newQuantity = $prodSessionItem['quantity'] + $quantity;

                        $productData = [
                            'product_id' => $row['MASP'],
                            'name' => $row['TENSP'],
                            'image' => $row['ANHSP'],
                            'price' => isset($row['GIABANLE']) ? $row['GIABANLE'] : 0,
                            'quantity' => $newQuantity,
                        ];
                        $_SESSION['productItems'][$key] = $productData;
                    }
                }
            }
            redirect('/nhomhuongnoi/source/saler/transaction', 'Sản phẩm đã được thêm ' . $row['TENSP']);
        } else {
            redirect('/nhomhuongnoi/source/saler/transaction', "Không tìm thấy sản phẩm");
        }
    } else {
        redirect('/nhomhuongnoi/source/saler/transaction', "Có gì đó sai sai ~");
    }
}

if(isset($_POST['productIncDec'])){

    $productId = validate($_POST['product_id']);
    $quantity = validate($_POST['quantity']);

    $flag = false;
    foreach($_SESSION['productItems'] as $key => $item){
        if($item['product_id'] == $productId){

            $flag = true;
            $_SESSION['productItems'][$key]['quantity'] = $quantity;
        }
    }

    if($flag){
        // hàm này trong file function
        jsonResponse(200,'success','Đã cập nhật số lượng');
    } else {
        jsonResponse(500,'error','Có gì đó sai sai. Vui lòng tải lại');
    }

}

// Xóa sp
$paramResult = checkParamId('index');
if(is_numeric($paramResult)){
    $indexValue = validate($paramResult);

    if(isset($_SESSION['productItems']) && isset($_SESSION['productItemIds'])){
        unset($_SESSION['productItems'][$indexValue]);
        unset($_SESSION['productItemIds'][$indexValue]);

        redirect('/nhomhuongnoi/source/saler/transaction','Đã xóa sản phẩm');

    } else {
        redirect('/nhomhuongnoi/source/saler/transaction','Không có sản phẩm');
    }
}

// Nút tiến hàng đặt
if(isset($_POST['proceedToPlaceBtn']))
{
    $phone = validate($_POST['cphone']);
    $payment_mode = validate($_POST['payment_mode']);

    // Kiểm tra khách hàng
    $checkCustomer = mysqli_query($con,"SELECT * FROM khachhang WHERE SDT='$phone' LIMIT 1");
    if($checkCustomer){
        if(mysqli_num_rows($checkCustomer) > 0){
            $_SESSION['invoice_no'] = "HD-".rand(111111,999999);
            $_SESSION['cphone'] = $phone;
            $_SESSION['payment_mode'] = $payment_mode;
            jsonResponse(200,'success','Đã tìm thấy khách hàng');

        } else {
            $_SESSION['cphone'] = $phone;
            jsonResponse(404,'warning','Không tìm thấy khách hàng');

        }
    } else {
        jsonResponse(500,'error','Có gì đó sai sai');
    }
}






?>

<section class="content-header">
    <h3>Tạo giao dịch</h3>
</section>

<section class="content">
    <div class="container-fluid px-4">
        <div class="card-body">
            <?php alertMessage(); ?>
            <form action="" method="post">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="">Chọn sản phẩm</label>
                        <select name="product_id" class="form-select mySelect2">
                            <option value="">--Chọn sản phẩm --</option>
                            <?php
                            if (isset($products) && is_array($products) && count($products) > 0) {
                                foreach ($products as $prodItem) {
                                    ?>
                                    <option value="<?= htmlspecialchars($prodItem['MASP']) ?>"><?= htmlspecialchars($prodItem['TENSP']) ?></option>
                                    <?php
                                }
                            } else {
                                echo '<option value="">' . (isset($products) ? 'Không tìm thấy sản phẩm' : 'Không có dữ liệu sản phẩm') . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="">Số lượng</label>
                        <input type="number" name="quantity" value="1" class="form-control" />
                    </div>
                    <div class="col-md-3 mb-3 text-end">
                        <br/>
                        <button type="submit" name="addItem" class="btn btn-primary">Thêm sản phẩm</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="mb-0">Sản phẩm</h4>
            </div>
            <div class="card-body">
                <?php
                if (isset($_SESSION['productItems'])) {
                    $sessionProducts = $_SESSION['productItems'];

                    if(empty($sessionProducts)){
                        unset($_SESSION['productItemIds']);
                        unset($_SESSION['productItems']);

                    }

                    ?>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên SP</th>
                                    <th>Giá bán</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                    <th>Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i = 1;
                                    foreach ($sessionProducts as $key => $item) :
                                ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($item['name'] ?? '') ?></td>
                                        <td class="product-price"><?= htmlspecialchars(number_format($item['price'] ?? 0, 0)) ?></td>
                                        <td>
                                            <div class="input-group qtyBox">
                                                <input type="hidden" value="<?= $item['product_id']; ?>" class="prodId" />
                                                <button class="input-group-text decrement">-</button>
                                                <input type="text" value="<?= htmlspecialchars($item['quantity'] ?? 1); ?>" class="qty quantityInput" data-product-id="<?= htmlspecialchars($item['product_id']) ?>" />
                                                <button class="input-group-text increment">+</button>
                                            </div>
                                        </td>
                                        <td class="product-total"><?= number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0) ?></td>
                                        <td>
                                            <a href="saler/transaction?index=<?= $key; ?>" class="btn btn-danger">
                                                Xóa
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <label>Chọn phương thức thanh toán</label>
                                <select id="payment_mode" class="form-select">
                                    <option value="">--Chọn phương thức--</option>
                                    <option value="Tiền mặt">Tiền mặt</option>
                                    <option value="Chuyển khoản">Chuyển khoản</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Nhập số điện thoại khách hàng</label>
                                <input type="number" id="cphone" class="form-control" value='<?= $_SESSION['cphone'] ?? ''; ?>' />
                            </div>
                            <div class="col-md-4">
                                <br/>
                                <button type="button"  class="btn btn-warning w-100 proceedToPlaceBtn">Tiến hành đặt hàng</button>

                            </div>
                        </div>
                    </div>

                    

                    <?php
                } else {
                    echo '<h5> Không có sản phẩm được thêm </h5>';
                }
                ?>
            </div>
        </div>
    </div>

</section>


<style>
    .quantityInput {
        width: 50px !important;
        padding: 6px 3px;
        text-align: center;
        border: 1px solid #cfb1b1;
        outline: 0;
        margin-right: 1px;
    }
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
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* ===== MAIN LAYOUT ===== */
    .content {
        padding: 2rem;
        min-height: calc(100vh - 100px);
        /* background: linear-gradient(135deg, var(--light-bg) 0%, #e9ecef 100%); */
        display: flex;
        flex-direction: column;
        /* Thay đổi hướng hiển thị các phần tử con theo cột */
        align-items: center;
        /* justify-content: center; */
        /* Có thể bỏ hoặc điều chỉnh tùy theo ý muốn */
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="frontend/css/admin.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {


    $('.increment').off('click').on('click', function() {
        var qtyInput = $(this).siblings('.quantityInput');
        var currentQty = parseInt(qtyInput.val());
        if (!isNaN(currentQty)) {
            qtyInput.val(currentQty + 1);
            updateTotal(qtyInput);
        }
    });

    $('.decrement').off('click').on('click', function() {
        var qtyInput = $(this).siblings('.quantityInput');
        var currentQty = parseInt(qtyInput.val());
        if (!isNaN(currentQty) && currentQty > 1) {
            qtyInput.val(currentQty - 1);
            updateTotal(qtyInput);
        }
    });

    $('.quantityInput').off('change').on('change', function() {
        updateTotal($(this));
    });

    function updateTotal(qtyInput) {
        var quantity = parseInt(qtyInput.val());
        if (isNaN(quantity) || quantity < 1) {
            quantity = 1;
            qtyInput.val(quantity);
        }
        var row = qtyInput.closest('tr');
        var price = parseFloat(row.find('.product-price').text().replace(/,/g, ''));
        var total = price * quantity;
        row.find('.product-total').text(numberFormat(total));

        var productId = qtyInput.data('product-id');
        $.ajax({
            type: "POST",
            url: "", // Current page URL
            data: {
                'productIncDec': true,
                'product_id': productId,
                'quantity': quantity
            },
            dataType: "json",
            success: function(response) {
                if (response.status == 200) {
                    alertify.success(response.message); // Hiển thị thông báo thành công
                } else {
                    alertify.error(response.message); // Hiển thị thông báo lỗi
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + error);
                alertify.error("Đã có lỗi xảy ra khi cập nhật số lượng!");
            }
        });
        qtyInput.data('prev-qty', quantity); // Store current quantity for potential rollback
    }

    function numberFormat(number) {
        return number.toLocaleString('vi-VN');
    }

    $('.quantityInput').each(function() {
        $(this).data('prev-qty', parseInt($(this).val()));
    });

    // Nút tiến hàng đặt hàng
    $(document).on('click','.proceedToPlaceBtn',function(){

        var cphone = $('#cphone').val();
        var payment_mode = $('#payment_mode').val();

        if(payment_mode == ''){
            Swal.fire(
                'Chọn phương thức thanh toán',
                'Vui lòng chọn phương thức thanh toán của khách hàng',
                'warning'
            );
            return false;
        }

        if(cphone == '' || !$.isNumeric(cphone)){
            Swal.fire(
                'Nhập số điện thoại',
                'Vui lòng nhập đúng số điện thoại khách hàng',
                'warning'
            );
            return false;
        }

        var data ={
            'proceedToPlaceBtn': true,
            'cphone': cphone,
            'payment_mode': payment_mode,
        };

        $.ajax({
            type: "POST",
            url: "", // Current page URL
            data: data,
            success: function(response) {
                if (response.status == 200) {
                    window.location.href = "saler/orders";
                } else if (response.status == 404) {
                    Swal.fire({
                        title: response.message,
                        text: 'Bạn có muốn thêm khách hàng mới?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Có, thêm ngay!',
                        cancelButtonText: 'Không'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "saler/customer";
                        }
                    });
                } else {
                    alert('Lỗi: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + error);
                alert('Đã có lỗi xảy ra khi xử lý yêu cầu.');
            }
        });


    });

    


});
</script>
