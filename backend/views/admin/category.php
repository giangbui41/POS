
<section class="content-header">
    <h3>DANH MỤC SẢN PHẨM</h3>
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
                                <i class="fas fa-plus"></i> Thêm danh mục
                            </button>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Tìm theo tên, mô tả...">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
        
                    <!-- bảng hiển thị -->
                    <div id="category-list-container">
                        <?php require_once 'backend/views/admin/category_table.php'; ?>
                    </div>
                </div>
                <!-- Modal Thêm Danh mục -->
                <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <form id="form-add-category" enctype="multipart/form-data">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title">Thêm danh mục mới</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                <div class="form-group">
                                    <label>Tên danh mục</label>
                                    <input type="text" class="form-control" id="tenDM" required>
                                </div>
                                <div class="form-group">
                                    <label>Mô tả</label>
                                    <input type="text" class="form-control" id="mota" required>
                                </div>
                                <div class="form-group">
                                    <label>Người tạo</label>
                                    <input type="text" class="form-control" id="nguoitao" required>
                                </div>
                                </div>
                                <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Lưu</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal Chỉnh sửa -->
                <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form id="form-edit-category" enctype="multipart/form-data">
                    <input type="hidden" name="madm" id="edit-madm">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title">Chỉnh sửa danh mục</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                        <div class="form-group">
                            <label>Tên danh mục</label>
                            <input type="text" class="form-control" id="edit-ten" required>
                        </div>
                        <div class="form-group">
                            <label>Mô tả</label>
                            <input type="text" class="form-control"  id="edit-mota" required>
                        </div>
                        <div class="form-group">
                            <label>người tạo</label>
                            <input type="text" class="form-control"  id="edit-nguoitao" required>
                        </div>
                        </div>
                        <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        </div>
                    </div>
                    </form>
                </div>
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

<script>
$(document).ready(function () {
    // Hàm tải lại danh sách danh mục
    function loadCategoryList() {
        $.ajax({
            url: 'admin/handleAction',
            type: 'POST',
            data: {
                module: 'category',
                name: 'get' // Thêm action get
            },
            success: function(response) {
                $('#category-list-container').html(response);
                bindEvents();
            },
            error: function(xhr, status, error) {
                alert('Lỗi khi tải dữ liệu: ' + error);
            }
        });
    }

    function performSearch() {
    var keyword = $('#searchInput').val().trim();
    
    if(keyword === '') {
        loadCategoryList();
        return;
    }

    $.ajax({
        url: 'admin/handleAction',
        type: 'POST',
        data: {
            module: 'category',
            name: 'search',
            keyword: keyword
        },
        success: function(response) {
            $('#category-list-container').html(response);
            bindEvents();
            // Hiển thị nút reset tìm kiếm
            if($('#resetSearch').length === 0) {
                $('#searchButton').after(
                    '<button id="resetSearch" class="btn btn-outline-danger ml-2">' +
                    '<i class="fas fa-times"></i> Xóa tìm kiếm' +
                    '</button>'
                );
                $('#resetSearch').click(function() {
                    $('#searchInput').val('');
                    loadCategoryList();
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
    
    // Gắn các sự kiện
    function bindEvents() {
        // Mở modal thêm
        $('#openAddModal').on('click', function() {

            $('#addCategoryModal').modal('show');
        });

        // Mở modal sửa
        $('span.btn-primary').off('click').on('click', function() {
            var row = $(this).closest('tr');
            var cells = row.find('td');
            
            $('#edit-madm').val(cells.eq(1).text());
            $('#edit-ten').val(cells.eq(2).text());
            $('#edit-mota').val(cells.eq(3).text());
            $('#edit-nguoitao').val(cells.eq(4).text());
            
            $('#editCategoryModal').modal('show');
        });

        // Xử lý xóa
        $('span.btn-danger').off('click').on('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa danh mục này?')) {
                var madm = $(this).closest('tr').find('td').eq(1).text();
                
                $.ajax({
                    type: 'POST',
                    url: 'admin/handleAction',
                    data: {
                        name: 'del',
                        module: 'category',
                        MADM: madm
                    },
                    success: function(response) {
                        if (response.trim() === 'success') {
                            loadCategoryList();
                            alert('Xóa thành công!');
                        } 
                        else {
                            alert(response);
                        }
                    }
                });
            }
        });
    }

    // Form thêm danh mục
    $('#form-add-category').submit(function(e) {
        e.preventDefault();
        
        var formData = {
            name: 'add',
            module: 'category',
            TENDANHMUC: $('#tenDM').val(),
            MOTA: $('#mota').val(),
            NGUOITAO: $('#nguoitao').val()
        };

        if (!formData.TENDANHMUC || !formData.MOTA || !formData.NGUOITAO) {
            alert('Vui lòng nhập đầy đủ thông tin!');
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'admin/handleAction',
            data: formData,
            success: function(response) {
                console.log('Phản hồi từ server:', response);
                if (response.trim() === 'success') {
                    $('#addCategoryModal').modal('hide');
                    $('#form-add-category')[0].reset();
                    loadCategoryList();
                    // alert('Thêm thành công!');
                } 
                // else {
                //     alert(response);
                // }
            }
        });
    });

    // Form sửa danh mục
    $('#form-edit-category').submit(function(e) {
        e.preventDefault();
        
        var formData = {
            name: 'edit',
            module: 'category',
            MADM: $('#edit-madm').val(),
            TENDANHMUC: $('#edit-ten').val(),
            MOTA: $('#edit-mota').val(),
            NGUOITAO: $('#edit-nguoitao').val()
        };

        if (!formData.TENDANHMUC || !formData.MOTA || !formData.NGUOITAO) {
            alert('Vui lòng nhập đầy đủ thông tin!');
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'admin/handleAction',
            data: formData,
            success: function(response) {
                if (response.trim() === 'success') {
                    $('#editCategoryModal').modal('hide');
                    loadCategoryList();
                    alert('Cập nhật thành công!');
                } 
                else {
                    alert(response);
                }
            }
        });
    });

    // Tải dữ liệu ban đầu
    loadCategoryList();
});
</script>