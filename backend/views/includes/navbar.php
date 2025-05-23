
<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #e3f2fd;">
  <div class="container-fluid">
    <!-- Hamburger -->
    <button class="btn btn-light mr-2" style="background-color: #00008b; color:white;" id="toggleSidebar">
      <i class="fas fa-bars"></i>
    </button>

    <!-- Logo -->
    <a class="navbar-brand font-weight-bold  mb-0" style="color: #00008b;">
      <i class="fas fa-cash-register mr-2 " style="color: #00008b;"></i>Intronix - Nhân viên
    </a>
    
    <!-- Staff Info -->
    <div class="ml-auto d-flex align-items-center">
      <div class="staff-info mr-3 d-flex align-items-center">
        <div class="avatar-circle mr-2">
          <?php 
          $avatarPath = !empty($user['ANHDAIDIEN']) ? $user['ANHDAIDIEN'] : 'frontend/images/anhdaidien/defaultAvatar.png';
          ?>
          <img src="<?php echo $avatarPath; ?>" alt="User Avatar" class="rounded-circle" width="40" height="40" onerror="this.src='frontend/images/anhdaidien/defaultAvatar.png'">
        </div>
        <span class="text-black"><?php echo htmlspecialchars($user['HOTEN'] ?? 'Guest'); ?></span>
      </div>
      
      <!-- User dropdown -->
      <div class="dropdown">
        <button class="btn btn-light dropdown-toggle" type="button" id="adminMenu" data-toggle="dropdown">
          <i class="fas fa-user-cog mr-1"></i> Tài khoản
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="index.php?url=infoControl/index">
            <i class="fas fa-user mr-2"></i>Thông tin
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item text-danger" href="logout.php">
            <i class="fas fa-sign-out-alt mr-2"></i>Đăng xuất
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>