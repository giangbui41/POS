<?php
    include('backend/core/function.php');
?>

<section class="content-header">
    <h3>Khách hàng</h3>
</section>
<div class="d-flex justify-content-end mb-3">
    <h4 class="mb-0 mr-2">
        <a href="saler/customer" class="btn btn-primary">Quay lại</a>
    </h4>
</div>
<div class="card-body">
    <?php alertMessage(); ?>
    <form action="" method="POST">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="">Họ tên</label>
                <input type="text" name="name" required class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label for="">Địa chỉ</label>
                <input type="text" name="address" required class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label for="">Số điện thoại</label>
                <input type="number" name="phone" required class="form-control">
            </div>
            <div class="col-md-6">
                <label>Trạng thái (Ẩn/Hiện) </label>
                <br/>
                <input type="checkbox" name="status" style="width:30px; height:30px;">
            </div>
            <div class="col-md-6 mb-3 text-end">
                <br />
                <button type="submit" name="saveCustomer" class="btn btn-primary">Lưu</button>
            </div>
        </div>
    </form>
</div>
<section class="content">

</section>

<?php

if(isset($_POST['saveCustomer'])){
    $id = generateNewCustomerId();
    $name = validate($_POST['name']);
    $address = validate($_POST['address']);
    $phone = validate($_POST['phone']);
    $status = isset($_POST['status']) ? 1 : 0;

    if($name != ''){
        $data = [
            'MAKH' => $id,
            'HOTEN'     => $name,
            'SDT'       => $phone,
            'DIACHI'    => $address,
            'TRANGTHAI' => $status
        ];

        $result = insert('khachhang',$data);
        if($result){
            redirect('/nhomhuongnoi/source/saler/customer','Thêm khách hàng thành công !');
        } else {
            redirect('/nhomhuongnoi/source/saler/customer','Có gì đó sai sai~');
        }

    } else {
        redirect('/nhomhuongnoi/source/saler/customer','Vui lòng điền những dòng bắt buộc');
    }
}

?>

<style>
.card-body{
    background-color: #e3f2fd;
    border-radius: 10px;
}
.content{
    background-color: white;
    color:black;
}
    /* ===== BASE STYLES ===== */
:root {
    --primary-color: #00008b;
    --secondary-color: #3f37c9;
    --accent-color: #4895ef;
    --text-color: #2b2d42;
    --light-bg: #f8f9fa;
    --white: #ffffff;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
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