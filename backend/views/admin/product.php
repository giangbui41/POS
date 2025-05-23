<section class="content-header">
    <h3>Sản phẩm</h3>
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
                                <i class="fas fa-plus"></i> Thêm sản phẩm
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

                    <!-- bảng hiển thị -->
                    <div id="product-list-container">
                        <?php require_once 'backend/views/admin/product_table.php'; ?>
                    </div>
                </div>
                

                <!-- Modal Thêm sản phẩm -->
                <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <form id="form-add-product" enctype="multipart/form-data">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title">Thêm sản phẩm mới</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                <div class="form-group">
                                    <label>Danh mục</label>
                                        <select class="form-control" id="danhmuc" required>
                                            <option value="">-- Chọn danh mục --</option>
                                            <?php 
                                            foreach($categories as $category) {
                                                echo '<option value="'.$category['MADM'].'">'.$category['TENDANHMUC'].'</option>';
                                            }
                                            ?>
                                        </select>
                                </div>
                                <div class="form-group">
                                    <label>Barcode</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="barcode" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" id="generateBarcode">
                                                <i class="fas fa-barcode"></i> Tạo tự động
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2" id="barcodePreview"></div>
                                </div>
                                <div class="form-group">
                                    <label>Tên sản phẩm</label>
                                    <input type="text" class="form-control" id="ten" required>
                                </div>
                                <div class="form-group">
                                    <label>Giá gốc</label>
                                    <input type="number" class="form-control" id="giagoc" required>
                                </div>
                                <div class="form-group">
                                    <label>Giá bán lẻ</label>
                                    <input type="number" class="form-control" id="giabanle" required>
                                </div>
                                <div class="form-group">
                                    <label>Số lượng</label>
                                    <input type="number" class="form-control" id="soluong" required>
                                </div>
                                <div class="form-group">
                                    <label>Ảnh</label>
                                    <input type="file" class="form-control-file" id="image" accept="image/*">
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
                <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form id="form-edit-product" enctype="multipart/form-data">
                    <input type="hidden" name="masp" id="edit-masp">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title">Chỉnh sửa sản phẩm</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                        <div class="form-group">
                            <label>Danh mục</label>
                                <select class="form-control" id="edit-danhmuc" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php 
                                    foreach($categories as $category) {
                                        echo '<option value="'.$category['MADM'].'">'.$category['TENDANHMUC'].'</option>';
                                    }
                                    ?>
                                </select>
                        </div>
                        <div class="form-group">
                            <label>Barcode</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="edit-barcode" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="edit-generateBarcode">
                                        <i class="fas fa-barcode"></i> Tạo tự động
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2" id="edit-barcodePreview"></div>
                        </div>
                        <div class="form-group">
                            <label>Tên sản phẩm</label>
                            <input type="text" class="form-control"  id="edit-ten" required>
                        </div>
                        <div class="form-group">
                            <label>Giá gốc</label>
                            <input type="number" class="form-control"  id="edit-giagoc" required>
                        </div>
                        <div class="form-group">
                            <label>Giá bán lẻ</label>
                            <input type="number" class="form-control"  id="edit-giabanle" required>
                        </div>
                        <div class="form-group">
                            <label>Số lượng</label>
                            <input type="number" class="form-control" id="edit-soluong" required>
                        </div>
                        <div class="form-group">
                            <label>Ảnh</label>
                            <input type="file" class="form-control-file" id="edit-image" accept="image/*">
                            <img id="preview-image" src="" style="width: 50px; margin-top: 5px;" />
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
<style>
    .barcode {
    max-width: 100%;
    height: 40px;
    margin: 0 auto;
    display: block;
}
</style>
<link rel="stylesheet" href="frontend/css/admin.css">
<!-- jQuery trước -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome cho icon -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Barcode -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<script>
    // Hàm tạo barcode tự động
function generateRandomBarcode() {
    // Tạo barcode 12 số theo chuẩn EAN-13 (bỏ số cuối là checksum)
    let barcode = '8' + Math.floor(Math.random() * 90000000000 + 10000000000).toString().substring(0, 11);
    return barcode;
}

// Hàm hiển thị barcode
function displayBarcode(barcode, containerId) {
    // Xóa container trước khi tạo mới
    $('#' + containerId).empty();
    
    // Tạo SVG element mới
    $('#' + containerId).append('<svg class="barcode-preview"></svg>');
    
    // Tạo barcode
    JsBarcode('#' + containerId + ' .barcode-preview', barcode, {
        format: "EAN13",
        lineColor: "#000",
        width: 2,
        height: 60,
        displayValue: true,
        fontSize: 16,
        margin: 10
    });
}
$(document).ready(function () {
    // Hàm tải lại danh sách sản phẩm
    function loadProductList() {
        $.ajax({
            url: 'admin/handleAction',
            type: 'POST',
            data: {
                module: 'product',
                name: 'get'
            },
            success: function(response) {
                $('#product-list-container').html(response);
                bindEvents();

                $('.barcode').each(function() {
                    var barcode = $(this).data('barcode');
                    if (barcode) {
                        JsBarcode(this, barcode, {
                            format: "EAN13",
                            lineColor: "#000",
                            width: 1,
                            height: 40,
                            displayValue: true,
                            fontSize: 12,
                            margin: 5
                        });
                    }
                });
            },
            
            error: function(xhr, status, error) {
                alert('Lỗi khi tải dữ liệu: ' + error);
            }
        });
    }

    // Hàm tìm kiếm sản phẩm
    function performSearch() {
        var keyword = $('#searchInput').val().trim();
        
        if(keyword === '') {
            loadProductList();
            return;
        }

        $.ajax({
            url: 'admin/handleAction',
            type: 'POST',
            data: {
                module: 'product',
                name: 'search',
                keyword: keyword
            },
            success: function(response) {
                $('#product-list-container').html(response);
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
                        loadProductList();
                        $(this).remove();
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Lỗi tìm kiếm:", error);
            }
        });
    }

    $('#generateBarcode').click(function() {
        var barcode = generateRandomBarcode();
        $('#barcode').val(barcode);
        displayBarcode(barcode, 'barcodePreview');
    });
    
    // Sự kiện tạo barcode tự động cho modal sửa
    $('#edit-generateBarcode').click(function() {
        var barcode = generateRandomBarcode();
        $('#edit-barcode').val(barcode);
        displayBarcode(barcode, 'edit-barcodePreview');
    });

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
        // Mở modal thêm sản phẩm
        $('#openAddModal').off('click').on('click', function() {
            $('#addProductModal').modal('show');
        });

        // Mở modal sửa sản phẩm
        $('.btn-edit').off('click').on('click', function() {
            var row = $(this).closest('tr');
            var cells = row.find('td');
            var masp = row.data('masp');
            
            $('#edit-masp').val(cells.eq(2).text().trim()); // Lấy MASP từ cột thứ 2
            $('#edit-danhmuc').val(cells.eq(1).text().trim()); // Lấy DANHMUC từ cột thứ 1
            $('#edit-ten').val(cells.eq(4).text().trim()); // Lấy TENSP từ cột thứ 3
            $('#edit-giagoc').val(cells.eq(6).text().replace(/[^\d]/g, '')); // Lấy GIAGOC từ cột thứ 5
            $('#edit-giabanle').val(cells.eq(7).text().replace(/[^\d]/g, '')); // Lấy GIABANLE từ cột thứ 6
            $('#edit-soluong').val(cells.eq(8).text().trim()); // Lấy SOLUONG từ cột thứ 7
            $('#edit-barcode').val(cells.eq(3).text().trim());
            
            // Hiển thị ảnh hiện tại
            var currentImage = cells.eq(4).find('img').attr('src'); // Lấy ANHSP từ cột thứ 4
            if (currentImage) {
                $('#preview-image').attr('src', currentImage).show();
            } else {
                $('#preview-image').hide();
            }
            
            $('#editProductModal').modal('show');
        });

        // Xử lý xóa sản phẩm
        $('.btn-delete').off('click').on('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                var row = $(this).closest('tr');
                var masp = $(this).closest('tr').find('td').eq(2).text();
                var currentImage = row.data('image');
                
                $.ajax({
                    type: 'POST',
                    url: 'admin/handleAction',
                    data: {
                        name: 'del',
                        module: 'product',
                        MASP: masp,
                        CURRENT_IMAGE: currentImage
                    },
                    success: function(response) {
                        if (response.trim() === 'success') {
                            loadProductList();
                        } else {
                            alert(response); // Hiển thị thông báo lỗi từ server
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Lỗi khi xóa sản phẩm: ' + error);
                    }
                });
            }
        });
    }

    // Form thêm sản phẩm
    $('#form-add-product').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData();
        formData.append('module', 'product');
        formData.append('name', 'add');
        formData.append('BARCODE', $('#barcode').val());
        formData.append('TENSP', $('#ten').val());
        formData.append('GIAGOC', $('#giagoc').val());
        formData.append('GIABANLE', $('#giabanle').val());
        formData.append('SOLUONG', $('#soluong').val());
        formData.append('DANHMUC', $('#danhmuc').val());
        formData.append('ANHSP', $('#image')[0].files[0]);
        
        // Validate dữ liệu
        if (!$('#ten').val() || !$('#giagoc').val() || !$('#giabanle').val() || !$('#danhmuc').val()) {
            alert('Vui lòng nhập đầy đủ thông tin!');
            return;
        }
        
        if (!$('#image')[0].files[0]) {
            alert('Vui lòng chọn ảnh sản phẩm!');
            return;
        }

        $.ajax({
            type: 'POST',
            url: 'admin/handleAction',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.trim() === 'success') {
                    $('#addProductModal').modal('hide');
                    $('#form-add-product')[0].reset();
                    loadProductList();
                } else {
                    alert(response);
                }
            },
            error: function(xhr, status, error) {
                alert('Lỗi khi thêm sản phẩm: ' + error);
            }
        });
    });

    // Form sửa sản phẩm
    $('#form-edit-product').submit(function(e) {
    e.preventDefault();
    var formData = new FormData();
    formData.append('module', 'product');
    formData.append('name', 'edit');
    formData.append('MASP', $('#edit-masp').val().trim());
    formData.append('BARCODE', $('#edit-barcode').val().trim());
    formData.append('TENSP', $('#edit-ten').val().trim());
    formData.append('GIAGOC', $('#edit-giagoc').val().trim());
    formData.append('GIABANLE', $('#edit-giabanle').val().trim());
    formData.append('SOLUONG', $('#edit-soluong').val().trim());
    formData.append('DANHMUC', $('#edit-danhmuc').val().trim());
    formData.append('CURRENT_IMAGE', $('#preview-image').attr('src')); // Thêm CURRENT_IMAGE

    var fileInput = $('#edit-image')[0];
    if (fileInput.files.length > 0) {
        formData.append('ANHSP', fileInput.files[0]);
    }
    $('#editProductModal').on('show.bs.modal', function() {
        var barcode = $('#edit-barcode').val();
        if (barcode) {
            displayBarcode(barcode, 'edit-barcodePreview');
        }
    });
    

    // Validate dữ liệu
    if (!formData.get('MASP') || !formData.get('TENSP') || !formData.get('GIAGOC') || 
        !formData.get('GIABANLE') || !formData.get('SOLUONG') || !formData.get('DANHMUC')) {
        alert('Vui lòng nhập đầy đủ thông tin!');
        return;
    }

    $.ajax({
        url: 'admin/handleAction',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Phản hồi từ server:', response); // Debug
            if (response.trim() === 'success') {
                $('#editProductModal').modal('hide');
                loadProductList();
                alert('Cập nhật thành công!');
            } else {
                alert('Cập nhật thất bại: ' + response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Lỗi AJAX:', error); // Debug
            alert('Lỗi khi cập nhật: ' + error);
        }
    });
});
    // Xem trước ảnh khi chọn file
    $('#edit-image').change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    

    // Tải dữ liệu ban đầu
    loadProductList();
});
</script>