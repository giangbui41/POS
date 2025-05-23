<table id="table1" class="table table-bordered table-striped">
    <thead>
        <tr id="tbheader">
            <th>STT</th>
            <th>Mã</th>
            <th>Tên danh mục</th>
            <th>mô tả</th>
            <!-- <th>Tổng sản phẩm</th> -->
            <th>người tạo</th>
            <th>ngày tạo</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($categories)): ?>
            <tr>
                <td colspan="9" class="text-center text-muted">
                    <i class="fas fa-info-circle"></i> Không tìm thấy danh mục phù hợp
                </td>
            </tr>
        <?php else: ?>
        <?php foreach ($categories as $i => $category): ?>
            
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= $category['MADM'] ?></td>
            <td><?= $category['TENDANHMUC'] ?></td>
            <td><?= $category['MOTA'] ?></td>
            <!-- <td><?= count(array_filter($products, function($p) use ($category) { return isset($p['DANHMUC']) && $p['DANHMUC'] == $category['MADM'];})) ?></td> -->
            <td><?= $category['NGUOITAO'] ?></td>
            <td><?= $category['NGAYTAO'] ?></td>
            <td class="text-center">
                <span class="btn btn-primary btn-sm">Chỉnh sửa</span>
                <span class="btn btn-danger btn-sm">Xóa</span>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>