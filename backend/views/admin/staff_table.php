<table id="table1" class="table-bordered">

    <thead>
        <tr id="tbheader">
            <th>STT</th>
            <th>Mã nhân viên</th>
            <th>Avatar</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Loại</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($staff)): ?>
            <tr>
                <td colspan="10" class="text-center text-muted">
                    <i class="fas fa-info-circle"></i> Không tìm thấy ..
                </td>
            </tr>
        <?php else: ?>
        <?php foreach ($staff as $i => $nv): ?>
            <tr data-id="<?php echo $nv['MANV'] ?>" data-image="<?php echo $nv['ANHDAIDIEN'] ?>">
                <td><?php echo $i + 1 ?></td>
                <td><?php echo $nv['MANV']?></td>
                <td><img style="width: 50px" src="<?php echo $nv['ANHDAIDIEN'] ?>"></td>
                <td class="editable" data-field="HOTEN"><?php echo $nv['HOTEN']?></td>
                <td class="editable" data-field="EMAIL"><?php echo $nv['EMAIL'] ?></td>
                <td class="editable" data-field="SDT"><?php echo $nv['SDT'] ?></td>
                <td class="editable" data-field="LOAI"><?php echo $nv['LOAI'] ?></td>
                <td>
                <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle 
                        <?= $nv['TRANGTHAI'] === 'Unlocked' ? 'btn-success' : 'btn-danger' ?>" 
                        type="button" 
                        id="dropdownMenuButton<?= $i ?>" 
                        data-toggle="dropdown" 
                        aria-haspopup="true" 
                        aria-expanded="false">
                        <?= $nv['TRANGTHAI'] ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton<?= $i ?>">
                        <a class="dropdown-item change-status" href="#" data-status="Unlocked">Unlocked</a>
                        <a class="dropdown-item change-status" href="#" data-status="Locked">Locked</a>
                    </div>
                </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-danger btn-delete ml-2" data-id="<?php echo $nv['MANV'] ?>">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                    <button class="btn btn-sm btn-info btn-resend" data-id="<?php echo $nv['MANV'] ?>">
                        <i class="fas fa-paper-plane"></i> Gửi lại mã
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
