<!-- Sidebar -->
<aside class="sidebar p-3" id="sidebar">
    <nav class="mt-2">
    <ul class="nav flex-column">
        <li class="nav-item mt-3">
        <h6 class="sidebar-heading px-3 text-dark">TÁC VỤ CHÍNH</h6>
        </li>
        <li class="nav-item">
        <a class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'saler/index') !== false) ? 'active' : ''; ?>" href="index.php?url=saler/index">
            <i class="fas fa-tachometer-alt mr-2"></i> Trang chủ
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link  <?php echo (strpos($_GET['url'] ?? '', 'saler/product') !== false) ? 'active' : ''; ?>" href="index.php?url=saler/product">
            <i class="fas fa-boxes mr-2"></i> Sản phẩm
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link  <?php echo (strpos($_GET['url'] ?? '', 'saler/xulygiaodich') !== false) ? 'active' : ''; ?>" href="index.php?url=saler/xulygiaodich">
            <i class="fas fa-stream mr-2"></i> Tạo giao dịch
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link  <?php echo (strpos($_GET['url'] ?? '', 'saler/orders') !== false) ? 'active' : ''; ?>" href="index.php?url=saler/view_orders">
            <i class="fas fa-boxes mr-2"></i> Lịch sử
        </a>
        </li>

        <li class="nav-item mt-3">
        <h6 class="sidebar-heading px-3 text-dark">KHÁC</h6>
        </li>
        <li class="nav-item">
        <a class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'saler/customer') !== false) ? 'active' : ''; ?>" href="index.php?url=saler/customer">
            <i class="fas fa-shopping-cart mr-2"></i> Khách hàng
        </a>

        <li class="nav-item">
        <a class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'reportControl') !== false) ? 'active' : ''; ?>" href="index.php?url=reportControl/index">
            <i class="fas fa-chart-bar mr-2"></i> Báo cáo
        </a>
        </li>
    </ul>
    </nav>
</aside>