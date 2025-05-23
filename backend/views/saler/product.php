<section class="content-header">
    <h3>Sản phẩm</h3>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center text-muted">
                <i class="fas fa-info-circle"></i> Không tìm thấy sản phẩm nào
            </div>
        <?php else: ?>
            <?php foreach ($products as $i => $product): ?>
                <div class="col-md-4 col-sm-6 col-12 mb-4">
                    <div class="card product-card" data-masp="<?php echo $product['MASP'] ?>" data-madm="<?php echo $product['DANHMUC'] ?>" data-image="<?php echo $product['ANHSP'] ?>">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0"><?php echo $product['TENSP'] ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="<?php echo $product['ANHSP'] ?>" class="img-fluid" style="max-height: 150px;">
                            </div>
                            <div class="product-details">
                                <p><strong>Mã SP:</strong> <?php echo $product['MASP'] ?></p>
                                <div class="barcode-container mb-2">
                                    <svg class="barcode" data-barcode="<?php echo $product['BARCODE'] ?>"></svg>
                                </div>
                                <p><strong>Giá bán:</strong> <?php echo number_format($product['GIABANLE']) ?> đ</p>
                                <p><strong>Số lượng:</strong> <?php echo $product['SOLUONG'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<link rel="stylesheet" href="frontend/css/admin.css">
<!-- jQuery trước -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome cho icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

<script>
$(document).ready(function() {
    // Tạo barcode cho các phần tử có class 'barcode'
    $('.barcode').each(function() {
        var barcodeValue = $(this).data('barcode');
        if (barcodeValue) {
            JsBarcode(this, barcodeValue, {
                format: "CODE128",
                lineColor: "#000",
                width: 1,
                height: 40,
                displayValue: true
            });
        }
    });
});
</script>

<style>
.product-card {
    transition: all 0.3s ease;
    height: 100%;
}
.product-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-5px);
}
.barcode-container {
    overflow: hidden;
    text-align: center;
}
.card-title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>