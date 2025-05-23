<div class="report-container">
    <h2 class="text-center mb-4">Báo cáo bán hàng</h2>

    <!-- Bộ lọc thời gian -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="reportFilterForm" class="row align-items-end">
                <!-- Phần chọn ngày tùy chỉnh -->
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Từ ngày</label>
                            <div class="input-group">
                                <input type="text" class="form-control datepicker" 
                                       name="start" 
                                       id="startDate"
                                       value="<?= $startDate ?>"
                                       autocomplete="off"
                                       readonly>
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Đến ngày</label>
                            <div class="input-group">
                                <input type="text" class="form-control datepicker" 
                                       name="end" 
                                       id="endDate"
                                       value="<?= $endDate ?>"
                                       autocomplete="off"
                                       readonly>
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Áp dụng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="card-title">Doanh thu</h5>
                    <h2 class="card-text"><?= number_format($reportData['tongdoanhthu']) ?> ₫</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="card-title">Đơn hàng</h5>
                    <h2 class="card-text"><?= number_format($reportData['tonghoadon']) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="card-title">Sản phẩm</h5>
                    <h2 class="card-text"><?= number_format($reportData['tongsp']) ?></h2>
                </div>
            </div>
        </div>
        <?php if ($isAdmin): ?>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <h5 class="card-title">Lợi nhuận</h5>
                    <h2 class="card-text"><?= number_format($reportData['loinhuan']) ?> ₫</h2>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Biểu đồ và báo cáo -->
    <div class="card mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="chart-tab" data-bs-toggle="tab" data-bs-target="#chart-tab-pane" type="button">Biểu đồ</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="category-tab" data-bs-toggle="tab" data-bs-target="#category-tab-pane" type="button">Theo danh mục</button>
                </li>
            </ul>
            <div class="tab-content" id="reportTabsContent">
                <div class="tab-pane fade show active" id="chart-tab-pane" role="tabpanel">
                    <canvas id="salesChart" height="300"></canvas>
                </div>
                <div class="tab-pane fade" id="category-tab-pane" role="tabpanel">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Danh mục</th>
                                <th>Sản phẩm</th>
                                <th>Doanh thu</th>
                                <?php if ($isAdmin): ?><th>Lợi nhuận</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData['byCategory'] as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['category']) ?></td>
                                <td><?= number_format($category['products']) ?></td>
                                <td><?= number_format($category['revenue']) ?> ₫</td>
                                <?php if ($isAdmin): ?><td><?= number_format($category['profit']) ?> ₫</td><?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách đơn hàng -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Danh sách đơn hàng</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày</th>
                            <th>Khách hàng</th>
                            <th>Nhân viên</th>
                            <th>Số SP</th>
                            <th>Tổng tiền</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderList as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['orderId']) ?></td>
                            <td><?= date('d/m/Y', strtotime($order['orderDate'])) ?></td>
                            <td><?= htmlspecialchars($order['customerName'] ?? 'Khách vãng lai') ?></td>
                            <td><?= htmlspecialchars($order['staffName'] ?? 'N/A') ?></td>
                            <td><?= $order['productCount'] ?></td>
                            <td><?= number_format($order['totalAmount']) ?> ₫</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary view-details" 
                                        data-order-id="<?= htmlspecialchars($order['orderId']) ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- Modal hiển thị chi tiết -->
                <div class="modal fade" id="orderDetailModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Chi tiết hóa đơn <span id="modalOrderId"></span></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Ngày tạo:</strong> <span id="orderDate"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Khách hàng:</strong> <span id="customerName"></span>
                                    </div>
                                </div>
                                
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>STT</th>
                                            <th>Sản phẩm</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orderItems">
                                        <!-- Dữ liệu sẽ được load bằng AJAX -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Tổng cộng:</th>
                                            <th id="orderTotal"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i> Đóng
                                </button>
                                <button type="button" class="btn btn-success" id="exportOrderPdf">
                                    <i class="fas fa-file-excel"></i> Xuất PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal chi tiết đơn hàng -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết đơn hàng <span id="modalOrderId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- nội dung -->
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.vi.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- JavaScript -->
<script>
(function($) {
    $(document).ready(function() {
        // Khởi tạo datepicker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'vi',
            endDate: new Date(),
            orientation: 'bottom auto',
            showOnFocus: true
        });

        // Hiển thị lịch khi nhấp vào biểu tượng lịch
        $('.input-group-text').click(function() {
            $(this).prev('input.datepicker').datepicker('show');
        });

        // Xử lý validate ngày
        $('#startDate').datepicker().on('changeDate', function(e) {
            const endDate = $('#endDate').datepicker('getDate');
            if (e.date > endDate) {
                $('#endDate').datepicker('setDate', e.date);
            }
        });

        $('#endDate').datepicker().on('changeDate', function(e) {
            const startDate = $('#startDate').datepicker('getDate');
            if (e.date < startDate) {
                $('#startDate').datepicker('setDate', e.date);
            }
        });

        // Xử lý submit form
        $('#reportFilterForm').submit(function(e) {
            e.preventDefault();
            
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            
            if (!startDate || !endDate) {
                alert('Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc');
                return;
            }
            
            window.location.href = 'reportControl/salesReport?range=custom&start=' + startDate + '&end=' + endDate;
        });

        // Khởi tạo biểu đồ
        const ctx = document.getElementById('salesChart');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Doanh thu',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Đơn hàng',
                        data: [],
                        type: 'line',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 2,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Doanh thu (₫)' },
                        ticks: { callback: value => value.toLocaleString() + ' ₫' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Số đơn hàng' },
                        grid: { drawOnChartArea: false }
                    }
                }
            }
        });

        // Load dữ liệu biểu đồ
        function loadChartData() {
            $.get('reportControl/getChartData', {
                start: '<?= $startDate ?>',
                end: '<?= $endDate ?>'
            }, function(response) {
                if (response.status === 'success') {
                    const labels = response.data.map(item => item.date);
                    const revenue = response.data.map(item => item.revenue);
                    const orders = response.data.map(item => item.orders);
                    
                    chart.data.labels = labels;
                    chart.data.datasets[0].data = revenue;
                    chart.data.datasets[1].data = orders;
                    chart.update();
                }
            });
        }
        
        // Xử lý xem chi tiết đơn hàng
        $('.view-details').click(function() {
            const orderId = $(this).data('order-id');
            $('#modalOrderId').text(orderId);
            
            $.get('reportControl/getOrderDetails', { orderId }, function(response) {
                if (response.status === 'success') {
                    let html = '<table class="table"><thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead><tbody>';
                    
                    response.data.forEach(item => {
                        html += `<tr>
                            <td>${item.productName}</td>
                            <td>${item.quantity}</td>
                            <td>${item.unitPrice.toLocaleString()} ₫</td>
                            <td>${item.totalPrice.toLocaleString()} ₫</td>
                        </tr>`;
                    });
                    
                    html += '</tbody></table>';
                    $('#orderDetailsContent').html(html);
                    $('#orderDetailsModal').modal('show');
                }
            });
        });
        
        $('.view-details').click(function() {
        const orderId = $(this).data('order-id');
        $('#modalOrderId').text(orderId);
        
        // Hiển thị modal
        const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
        modal.show();
        
        // Load dữ liệu bằng AJAX
        $.ajax({
            url: 'reportControl/getOrderDetails',
            type: 'GET',
            data: { orderId: orderId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Hiển thị thông tin cơ bản
                    $('#orderDate').text(response.data.orderDate || 'N/A');
                    $('#customerName').text(response.data.customerName || 'Khách vãng lai');
                    
                    // Hiển thị chi tiết sản phẩm
                    let html = '';
                    let total = 0;
                    
                    response.data.items.forEach((item, index) => {
                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.productName}</td>
                                <td>${item.quantity}</td>
                                <td>${formatCurrency(item.unitPrice)}</td>
                                <td>${formatCurrency(item.totalPrice)}</td>
                            </tr>
                        `;
                        total += item.totalPrice;
                    });
                    
                    $('#orderItems').html(html);
                    $('#orderTotal').text(formatCurrency(total));
                } else {
                    $('#orderItems').html('<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>');
                }
            },
            error: function() {
                $('#orderItems').html('<tr><td colspan="5" class="text-center text-danger">Lỗi khi tải dữ liệu</td></tr>');
            }
        });
    });
    
    // Hàm định dạng tiền tệ
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { 
            style: 'currency', 
            currency: 'VND' 
        }).format(amount);
    }

    // Xử lý nút đóng
    $('#orderDetailModal').on('hidden.bs.modal', function () {
        // Reset nội dung modal khi đóng
        $('#orderItems').html('<tr><td colspan="5" class="text-center">Đang tải dữ liệu...</td></tr>');
        $('#orderTotal').text('0 ₫');
    });

    // Xử lý xuất Excel
    $('#exportOrderPdf').click(function() {
        const orderId = $('#modalOrderId').text();
        if (!orderId) return;
        
        // Tạo form tạm để submit
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = 'reportControl/exportOrderPdf';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'orderId';
        input.value = orderId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });
        // Tải dữ liệu ban đầu
        loadChartData();
    });
})(jQuery);
</script>