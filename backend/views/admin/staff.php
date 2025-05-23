<section class="content-header">
    <h3>QUẢN LÝ NHÂN VIÊN</h3>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <!-- box-header -->
                <div class="box-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <button class="btn btn-primary btn-sm" id="openAddModal">
                                <i class="fas fa-plus"></i> Tạo nhân viên mới
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="search">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="staff-list-container">
                        <?php require_once 'backend/views/admin/staff_table.php'; ?>
                    </div>
                </div>
                
                <!-- Modal tạo mới nhân viên -->
                <div class="modal fade" id="addStaffModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="form-add-staff" enctype="multipart/form-data">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title">Thêm nhân viên mới</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                <div class="form-group">
                                    <label>Họ tên đầy đủ</label>
                                    <input type="text" class="form-control" name="HOTEN" required>
                                </div>
                                <div class="form-group">
                                    <label>Địa chỉ Gmail</label>
                                    <input type="email" class="form-control" name="EMAIL" required placeholder="example@gmail.com">
                                    <small class="text-muted">Nhân viên sẽ nhận email kích hoạt tại địa chỉ này</small>
                                </div>
                                <input type="hidden" name="LOAI" value="Staff">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
    /* Đảm bảo các lớp cha không cản trở dropdown */
    #table1 tbody tr {
        position: relative;
        z-index: 1;
    }

    #table1 tbody tr:hover {
        z-index: 10;
    }

    .dropdown-menu {
        z-index: 1050 !important; /* Đảm bảo cao hơn các z-index mặc định */
        display: none; /* Đảm bảo hiển thị đúng */
    }

</style>



<link rel="stylesheet" href="frontend/css/admin.css">
<!-- jQuery trước -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome cho icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- JavaScript -->
<script>
$(document).ready(function () {
    function loadStaffList(){
        $.ajax({
            url: 'admin/handleAction',
            type: 'POST',
            data: {
                module: 'staff',
                name: 'get'
            },
            success: function(response){
                $('#staff-list-container').html(response);
                bindEvents();
                $('.dropdown-toggle').dropdown();
            },
            error: function(xhr, status, error) {
                alert('Lỗi khi tải dữ liệu: ' + error);
            }
        });
    }

    // Hàm tìm kiếm nhân viên
    function performSearch(){
        var keyword = $('#searchInput').val().trim();
        
        if(keyword === '') {
            loadStaffList();
            return;
        }

        $.ajax({
            url: 'admin/handleAction',
            type: 'POST',
            data: {
                module: 'staff',
                name: 'search',
                keyword: keyword
            },
            success: function(response) {
                $('#staff-list-container').html(response);
                bindEvents();
                // Reinitialize Bootstrap dropdowns after search
                $('.dropdown-toggle').dropdown();
                
                // Hiển thị nút reset tìm kiếm
                if($('#resetSearch').length === 0) {
                    $('#searchButton').after(
                        '<button id="resetSearch" class="btn btn-outline-danger ml-2">' +
                        '<i class="fas fa-times"></i> Xóa tìm kiếm' +
                        '</button>'
                    );
                    $('#resetSearch').click(function() {
                        $('#searchInput').val('');
                        loadStaffList();
                        $(this).remove();
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Lỗi tìm kiếm:", error);
            }
        });
    }

    // Sự kiện click nút tìm kiếm
    $('#searchButton').click(performSearch);

    // Tìm kiếm khi nhấn Enter
    $('#searchInput').keypress(function(e) {
        if(e.which === 13) {
            performSearch();
        }
    });

    function bindEvents(){
        // Mở modal thêm nhân viên
        $('#openAddModal').off('click').on('click', function() {
            $('#addStaffModal').modal('show');
        });

        //Xử lý xóa nhân viên
        $(document).off('click', '.btn-delete').on('click', '.btn-delete', function(e) {
            e.preventDefault();
            var staffId = $(this).data('id');

            if (!confirm('Bạn có chắc muốn xóa nhân viên này không?')) {
                return;
            }

            $.ajax({
                url: 'admin/handleAction',
                type: 'POST',
                data: {
                    module: 'staff',
                    name: 'delete',
                    MANV: staffId
                },
                success: function(response) {
                    if (response.trim() === 'success') {
                        alert('Xóa nhân viên thành công!');
                        loadStaffList(); // Reload lại danh sách nhân viên
                    } else {
                        alert('Lỗi: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Lỗi khi gửi yêu cầu: ' + error);
                }
            });
        }); 
        // Xử lý thay đổi trạng thái
        $(document).off('click', '.change-status').on('click', '.change-status', function(e) {
            e.preventDefault();
            var newStatus = $(this).data('status');
            var staffId = $(this).closest('tr').data('id');
            
            // Gửi yêu cầu AJAX để cập nhật trạng thái
            $.ajax({
                url: 'admin/handleAction',
                type: 'POST',
                data: {
                    module: 'staff',
                    name: newStatus === 'Unlocked' ? 'unlock' : 'lock',
                    MANV: staffId
                },
                success: function(response) {
                    console.log('Raw response:', '[' + response + ']', 'length:', response.length);
                    if (response.trim() === 'success') {
                        // Cập nhật giao diện ngay lập tức
                        console.log('Thành công, chuẩn bị gọi setTimeout...');
                        var $row = $('tr[data-id="' + staffId + '"]');
                        var newButtonHtml = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-${newStatus === 'Unlocked' ? 'success' : 'danger'} btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                ${newStatus}
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item change-status" href="#" data-status="Unlocked">Unlocked</a>
                                <a class="dropdown-item change-status" href="#" data-status="Locked">Locked</a>
                            </div>
                        </div>
                        `;

                        $row.find('.btn-group').replaceWith(newButtonHtml);
                        bindEvents(); // Gán lại event vì mới thay HTML
                        setTimeout(function() {
                            console.log('Reload triggered');
                            location.reload();
                        }, 200); // delay nhẹ để tránh chặn bởi alert

                        //alert('Cập nhật trạng thái thành công!');
                        
                    } else {
                        alert('Lỗi: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Lỗi khi cập nhật trạng thái: ' + error);
                }
            });
        });
    }
    
    // Form tạo nhân viên mới
    $('#form-add-staff').submit(function(e) {
        e.preventDefault();
    
        // Chỉ cho phép tạo nhân viên loại Staff
        $('select[name="LOAI"]').val('Staff');
        
        $.ajax({
            url: 'admin/handleAction',
            type: 'POST',
            data: {
                module: 'staff',
                name: 'add',
                HOTEN: $('input[name="HOTEN"]').val(),
                EMAIL: $('input[name="EMAIL"]').val(),
                LOAI: 'Staff'
            },
            success: function(response) {
                if (response.trim() === 'success') {
                    alert('Thêm nhân viên thành công! Email đã được gửi đến nhân viên.');
                    $('#addStaffModal').modal('hide');
                    $('#form-add-staff')[0].reset();
                    loadStaffList();
                } else {
                    alert(response);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra khi thêm nhân viên!');
            }
        });
    });   

    // Xử lý nút gửi lại mã
    $(document).on('click', '.btn-resend', function(e) {
        e.preventDefault();
        var staffId = $(this).data('id');
        
        if (!confirm('Bạn có chắc muốn gửi lại mã đăng nhập cho nhân viên này?')) {
            return;
        }
        
        $.ajax({
            url: 'admin/handleAction',
            type: 'POST',
            data: {
                module: 'staff',
                name: 'resend',
                MANV: staffId
            },
            success: function(response) {
                if (response.includes('thành công') || response === 'success') {
                    alert('Đã gửi lại thông tin đăng nhập thành công!');
                } else {
                    alert('Lỗi: ' + response);
                }
            },
            error: function(xhr, status, error) {
                alert('Lỗi khi gửi yêu cầu: ' + error);
            }
        });
    });

    // Tải dữ liệu ban đầu
    loadStaffList();
    

});
</script>