// CODE GỐC
// <?php 
// // Kiểm tra session đã mở chưa
 if (session_status() === PHP_SESSION_NONE) {
       session_start();
   }
//include('core/db.php'); 
// dùng DIR để xác định đường dẫn tương đối đến file vì đang dùng index điều hướng các trang 
 include_once(__DIR__ . '/../core/db.php');

// Dat mui gio
date_default_timezone_set('Asia/Ho_Chi_Minh'); 
$db = new DB();
$con = $db->con;

$error = "";
$success = "";
$token = isset($_GET['token']) ? $_GET['token'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
       if (isset($_POST['set_password'])) {
             // Nhan vien moi dat lai mat khau
             $new_password = $_POST['new_password'];
             $confirm_password = $_POST['confirm_password'];

             if (empty($new_password) || empty($confirm_password)) {
                   $error = "Vui lòng nhập đầy đủ mật khẩu!";
            } elseif ($new_password !== $confirm_password) {
                   $error = "Mật khẩu không khớp!";
             } else {
                   if (empty($token)) {
                         die("Token không hợp lệ!");
                   }
                  
                   // Kiem tra token hop le/                   
                   $sql = "SELECT * FROM NHANVIEN WHERE TOKEN_DANG_NHAP = ? AND TOKEN_HET_HAN > NOW()";
                   $stmt = $db->prepare($sql);
                   $stmt->bind_param("s", $token);
                   $stmt->execute();
                   $result = $stmt->get_result();

                   if ($row = $result->fetch_assoc()) {
                   // Cap nhat mat khau moi & xoa token
                         $update_sql = "UPDATE NHANVIEN SET MAT_KHAU = ?, TOKEN_DANG_NHAP = NULL, TOKEN_HET_HAN = NULL WHERE EMAIL = ?";
                         $stmt_update = $db->prepare($update_sql);
                         $stmt_update->bind_param("ss", $new_password, $row['EMAIL']);

                         if ($stmt_update->execute()) {
                               $success = "Mật khẩu đã được đặt! Vui lòng đăng nhập.";                               header('location:login.php');
                         } else {
                               $error = "Lỗi khi cập nhật mật khẩu!";
                        }
                        $stmt_update->close();
                   } else {
                         $error = "Liên kết hết hạn hoặc không hợp lệ!";
                   }
                   $stmt->close();
             }
       } elseif (isset($_POST['login'])) {
             // Nhan vien cu dang nhap binh thuong
             $tk = $_POST['tk'];
             $mk = $_POST['mk'];
            
             $sql = "SELECT * FROM NHANVIEN WHERE TEN_DANG_NHAP = ? AND MAT_KHAU = ?";
             $stmt = $con->prepare($sql);
             $stmt->bind_param("ss", $tk, $mk);
             $stmt->execute();
             $result = $stmt->get_result();

             if ($result->num_rows <= 0) {
                   $error = 'Tài khoản hoặc mật khẩu chưa đúng';
             } else {
                   $_SESSION['tk'] = $tk;
                  
                   if ($tk == 'admin') {
                         header('location:admin_dashboard.php'); // Trang chu admin
                         exit();
                   } else {
                         header('location:dashboard.php'); // Trang nhan vien
                         exit();
                   }
            }
             $stmt->close();
       }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="container">
      <?php if ($error) : ?>
            <p class="error"><?php echo $error; ?></p>
      <?php endif; ?>

      <?php if ($success) : ?>
            <p class="success"><?php echo $success; ?></p>
      <?php endif; ?>

      <?php if ($token) : ?>
            <!-- Form dat mat khau moi -->
            <form method="POST" action="login.php?token=<?php echo htmlspecialchars($token); ?>">
                  <label>Mật khẩu mới:</label>
                  <input type="password" name="new_password" required>
                  <label>Xác nhận mật khẩu:</label>
                  <input type="password" name="confirm_password" required>
                  <button type="submit" name="set_password">Xác nhận</button>
            </form>
      <?php else: ?>
            <!-- Form dang nhap binh thuong -->
            <form method="post">
                  <div id="form-login">
                  <h2>Đăng nhập hệ thống</h2>
                  <center><span style="color:red;"><?php echo $error; ?></span></center>
                  <ul>
                        <li><label>Tài khoản</label><input type="text" name="tk" required /></li>
                        <li><label>Mật khẩu</label><input type="password" name="mk" required /></li>
                        <li><label>Ghi nhớ</label><input type="checkbox" name="check" checked="checked" /></li>
                        <li><input type="submit" name="login" value="Đăng nhập" /> <input type="reset" value="Làm mới" /></li>
                  </ul>
                  </div>
            </form>
      <?php endif; ?>
</div>
</body>
</html>  
