<?php 
    include('backend/core/function.php');
?>

<section class="content-header">
    <h3>Lịch sử bán hàng</h3>
</section>

    <div class="card-body">
        <?php alertMessage(); ?>

        <?php
        $customer = getAll('hoadon');
        if(!$customer) {
            echo '<h4> Có gì đó sai sai </h4>';
            return false;
        }

        if(mysqli_num_rows($customer) > 0){
            ?>
            <div class="table-responsive">
                <table class="table table-striped table-bodered">
                    <thead>
                        <tr>
                            <th>Mã HĐ</th>
                            <th>Mã KH</th>
                            <th>Mã NV</th>
                            <th>Ngày tạo</th>
                            <th>Tổng tiền</th>
                            <th>Phương thức</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($customer as $item): ?>
                        <tr>
                            <td><?= $item['MAHD'] ?></td>
                            <td><?= $item['MAKH'] ?></td>
                            <td><?= $item['MANV'] ?></td>
                            <td><?= $item['NGAYTAO'] ?></td>
                            <td><?= $item['TONGTIEN'] ?></td>
                            <td><?= $item['PHUONGTHUC'] ?></td>
                            
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>    
            <?php
            }
            else
            {
                ?>
                    <h4 class="mb-8">Không tìm thấy bản ghi nào</h4>
                <?php
            }
        ?>

    </div>
<section class="content">

</section>
<?php 




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