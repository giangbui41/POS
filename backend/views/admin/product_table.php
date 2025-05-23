<table id="table1" class="table table-bordered table-striped">
    <thead>
        <tr id="tbheader">
            <th>STT</th>
            <th>Danh mục</th>
            <th>Mã SP</th>
            <th>Barcode</th>
            <th>Tên sản phẩm</th>
            <th>Ảnh</th>
            <th>Giá gốc</th>
            <th>Giá bán lẻ</th>
            <th>Số lượng</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($products)): ?>
            <tr>
                <td colspan="10" class="text-center text-muted">
                    <i class="fas fa-info-circle"></i> Không tìm thấy sản phẩm nào
                </td>
            </tr>
        <?php else: ?>
            <?php
                $categoryMap = [];
                foreach ($categories as $cat) {
                    $categoryMap[$cat['MADM']] = $cat['TENDANHMUC'];
                }
                ?>
            <?php foreach ($products as $i => $product): ?>
                <tr data-masp="<?php echo $product['MASP'] ?>" data-madm="<?php echo $product['DANHMUC'] ?>" data-image="<?php echo $product['ANHSP'] ?>">
                    <td><?php echo $i + 1 ?></td>
                    <td> 
                    <?php 
                            $madm = $product['DANHMUC'];
                            echo isset($categoryMap[$madm]) ? $categoryMap[$madm] : '<i class="text-muted">Không rõ</i>';
                        ?>
                    </td>
                    <!-- <td><?php echo $product['DANHMUC']?></td> -->
                    <td><?php echo $product['MASP'] ?></td>
                    <td>
                        <svg class="barcode" data-barcode="<?php echo $product['BARCODE'] ?>"></svg>
                    </td>
                    <td><?php echo $product['TENSP'] ?></td>
                    <td><img style="width: 50px" src="<?php echo $product['ANHSP'] ?>"></td>
                    <td><?php echo number_format($product['GIAGOC']) ?></td>
                    <td><?php echo number_format($product['GIABANLE']) ?></td>
                    <td><?php echo $product['SOLUONG'] ?></td>
                    <td><?php echo $product['NGAYTAO'] ?></td>
                    <td class="text-center">
                        <span class="btn btn-primary btn-sm btn-edit">Chỉnh sửa</span>
                        <span class="btn btn-danger btn-sm btn-delete">Xóa</span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
