<?php include('includes/header.php'); ?>
<body class="admin-layout">

  <div class="d-flex flex-column min-vh-100">
    
    <!-- Navbar -->
    <?php include('includes/navbar.php'); ?>

    <div class="d-flex flex-grow-1">
      <!-- Sidebar -->
      <?php include('includes/sidebar-sa.php'); ?>

      <!-- Main Content -->
      <main class="flex-fill">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper"> 
          <!-- CONTEN HERE -->
          <div data-spy="scroll" data-target="#navbar-example" data-offset="0" style=" overflow-y: auto; position: relative;">
            <div class="main-content" id="main-content">
              <?php echo $content; ?>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Footer -->
    <?php include('includes/footer.php'); ?>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

  <!-- alerity -->
  <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/default.min.css"/>
  <script>
    $(document).ready(function() {
      $('.dropdown-toggle').dropdown();
    });

    document.addEventListener("DOMContentLoaded", function() {
      const toggleBtn = document.getElementById("toggleSidebar");
      const sidebar = document.getElementById("sidebar");

      toggleBtn.addEventListener("click", function() {
        if (window.innerWidth < 992) {
          sidebar.classList.toggle('show');
        } else {
          sidebar.classList.toggle('hide');
        }
      });

      window.addEventListener("resize", function() {
        if (window.innerWidth >= 992) {
          sidebar.classList.remove('show');
        } else {
          sidebar.classList.remove('hide');
        }
      });
    });
  </script>
</body>
</html>
