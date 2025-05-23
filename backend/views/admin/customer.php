<section class="content-header">
    <h3>Khách Hàng</h3>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <!-- box-header -->
                <div class="box-body">
                    <!-- bảng hiển thị -->
                    <table id="table1" class="table table-bordered table-striped">
                        <thead>
                            <tr id="tbheader">
                                <th>STT</th>
                                <th>Mã Khách Hàng</th>
                                <th>Họ tên</th>
                                <th>SĐT</th>
                                <th>Địa chỉ</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php foreach ($khachhang as $i => $kh): ?>
                                    <tr>
                                        <td><?php echo $i + 1 ?></td>
                                        <td><?php echo $kh['MAKH']?></td>
                                        <td><?php echo $kh['HOTEN']?></td>
                                        <td><?php echo $kh['SDT'] ?></td>
                                        <td><?php echo $kh['DIACHI'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<link rel="stylesheet" href="frontend/css/admin.css">
<!-- jQuery trước -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome cho icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
