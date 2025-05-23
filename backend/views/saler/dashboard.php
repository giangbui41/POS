<section class="content-header">
    <h3>TRANG CHỦ</h3>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <!-- Welcome outside box -->
            <div class="welcome-box p-3 mb-4">
                <h4>Chào mừng đến với hệ thống POS Intronix</h4>
                <h5>👋 Xin chào, người dùng: <strong><?php echo htmlspecialchars($admin['HOTEN']); ?></strong></h5>
                <p>Hôm nay là <span id="current-date"></span>, giờ hiện tại là <span id="current-time"></span></p>
                <p>Vai trò: <strong><?php echo $admin['LOAI'] ?></strong></p>
                <p>Chúc bạn có một ngày làm việc vui vẻ !!!</p>
            </div>
        </div>
    </div>
</section>
<style>
    /* ===== BASE STYLES ===== */
:root {
  --primary-color: #00008b;
  --secondary-color: #3f37c9;
  --accent-color: #4895ef;
  --text-color: #2b2d42;
  --light-bg: #f8f9fa;
  --white: #ffffff;
  --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
  --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
  --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
  --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

/* ===== MAIN LAYOUT ===== */
.content {
  padding: 2rem;
  min-height: calc(100vh - 100px);
  background: linear-gradient(135deg, var(--light-bg) 0%, #e9ecef 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
}

/* ===== WELCOME CARD ===== */
.welcome-box {
  background: var(--white);
  padding: 3rem;
  border-radius: 16px;
  box-shadow: var(--shadow-lg);
  transition: var(--transition);
  max-width: 900px;
  width: 100%;
  position: relative;
  overflow: hidden;
  z-index: 1;
  text-align: center;
  animation: fadeIn 0.6s ease-out forwards;
}

.welcome-box::before {
  content: "";
  position: absolute;
  top: -50px;
  right: -50px;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, rgba(67,97,238,0.1) 0%, rgba(67,97,238,0) 70%);
  z-index: -1;
}

.welcome-box::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
  background-size: 200% auto;
  animation: gradientBG 3s linear infinite;
}

/* ===== TYPOGRAPHY ===== */
.welcome-box h4 {
  font-size: 1.8rem;
  color: var(--text-color);
  font-weight: 700;
  margin-bottom: 1.5rem;
  line-height: 1.3;
  position: relative;
  display: inline-block;
}

.welcome-box h4:first-child {
  font-size: 2rem;
  margin-bottom: 2rem;
}

.welcome-box h4::after {
  content: "";
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 3px;
  background: var(--primary-color);
  border-radius: 3px;
}

.welcome-box p {
  font-size: 1.1rem;
  color: var(--text-color);
  margin-bottom: 1rem;
  line-height: 1.6;
}

.welcome-box strong {
  color: var(--primary-color);
  font-weight: 600;
}

#current-date, #current-time {
  color: var(--secondary-color);
  font-weight: 600;
  position: relative;
}

/* ===== INTERACTIVE ELEMENTS ===== */
.welcome-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

/* ===== ANIMATIONS ===== */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes gradientBG {
  0% { background-position: 0% center; }
  100% { background-position: 200% center; }
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
  .content {
    padding: 1.5rem;
  }
  
  .welcome-box {
    padding: 2rem;
  }
  
  .welcome-box h4 {
    font-size: 1.5rem;
  }
  
  .welcome-box h4:first-child {
    font-size: 1.7rem;
  }
}

@media (max-width: 576px) {
  .content {
    padding: 1rem;
  }
  
  .welcome-box {
    padding: 1.5rem;
  }
  
  .welcome-box h4 {
    font-size: 1.3rem;
  }
  
  .welcome-box h4:first-child {
    font-size: 1.5rem;
  }
  
  .welcome-box p {
    font-size: 1rem;
  }
  
  .welcome-box::before {
    display: none;
  }
}
</style>
<!-- Styles -->
<link rel="stylesheet" href="frontend/css/admin.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- JS: Date/Time -->
<script>
    function updateDateTime() {
        const now = new Date();
        const date = now.toLocaleDateString('vi-VN');
        const time = now.toLocaleTimeString('vi-VN');
        $('#current-date').text(date);
        $('#current-time').text(time);
    }

    $(document).ready(function () {
        updateDateTime();
        setInterval(updateDateTime, 1000);
    });
</script>
