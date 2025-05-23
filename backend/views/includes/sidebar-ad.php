<!-- Sidebar -->
<aside class="sidebar p-3" id="sidebar">
    <nav class="mt-2">
    <ul class="nav flex-column">
        <li class="nav-item mt-3">
        <h6 class="sidebar-heading px-3 text-dark">TÁC VỤ CHÍNH</h6>
        </li>
        <li class="nav-item">
        <a class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'admin/index') !== false) ? 'active' : ''; ?>" href="index.php?url=admin/index">
            <i class="fas fa-tachometer-alt mr-2"></i> Trang chủ
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link  <?php echo (strpos($_GET['url'] ?? '', 'admin/category') !== false) ? 'active' : ''; ?>" href="index.php?url=admin/category">
            <i class="fas fa-stream mr-2"></i> Danh mục
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link  <?php echo (strpos($_GET['url'] ?? '', 'admin/product') !== false) ? 'active' : ''; ?>" href="index.php?url=admin/product">
            <i class="fas fa-boxes mr-2"></i> Sản phẩm
        </a>
        </li>

        <li class="nav-item mt-3">
        <h6 class="sidebar-heading px-3 text-dark">QUẢN LÝ</h6>
        </li>
        <li class="nav-item">
        <a class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'admin/customer') !== false) ? 'active' : ''; ?>" href="index.php?url=admin/customer">
            <i class="fas fa-shopping-cart mr-2"></i> Khánh hàng
        </a>

        <li class="nav-item">
        <a class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'admin/staff') !== false) ? 'active' : ''; ?>" href="index.php?url=admin/staff">
            <i class="fas fa-users mr-2"></i> Nhân viên
        </a>
        </li>
        <li class="nav-item">
        <a class="nav-link <?php echo (strpos($_GET['url'] ?? '', 'reportControl') !== false) ? 'active' : ''; ?>" href="index.php?url=reportControl/index">
            <i class="fas fa-chart-bar mr-2"></i> Báo cáo
        </a>
        </li>
    </ul>
    </nav>
</aside>