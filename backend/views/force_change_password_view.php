

<!DOCTYPE html>
<html lang="vi">
<head>
      <meta charset="UTF-8">
      <title>Đổi mật khẩu lần đầu</title>
      <link rel="stylesheet" href="login.css">
      <style>
            body {
                  margin: 0;
                  font-family: 'Segoe UI', sans-serif;
                  background: #f5f7fb;
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  height: 100vh;
            }
            .container {
                  background: #fff;
                  padding: 40px 30px;
                  border-radius: 12px;
                  box-shadow: 0 0 10px rgba(0,0,0,0.1);
                  width: 400px;
                  position: relative;
            }
            h2 {
                  text-align: center;
                  color: #2c3e50;
                  margin-bottom: 30px;
            }
            form label {
                  display: block;
                  margin: 12px 0 5px;
                  font-weight: bold;
                  color: #34495e;
            }
            form input[type="password"] {
                  width: 100%;
                  padding: 10px 12px;
                  margin-bottom: 15px;
                  border: 1px solid #ccc;
                  border-radius: 8px;
                  font-size: 15px;
                  box-sizing: border-box;
            }
            form button {
                  padding: 10px 18px;
                  background-color: #4CAF50;
                  color: white;
                  border: none;
                  border-radius: 8px;
                  cursor: pointer;
                  font-size: 15px;
            }
            .error {
                  color: red;
                  text-align: center;
                  font-weight: bold;
                  margin-bottom: 10px;
            }
            .success {
                  color: green;
                  text-align: center;
                  font-weight: bold;
                  margin-bottom: 10px;
            }
      </style>
</head>
<body>
<?php include __DIR__ . '/../controllers/force_change_password.php'; ?>


<div class="container">
      <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
            <div style="text-align:center; margin-top:15px;">
                  <a href="login.php" style="color: #2980b9;">Quay về trang đăng nhập</a>
            </div>
      <?php else: ?>
            <h2>Đổi mật khẩu lần đầu</h2>
            <form method="post">
                  <label>Mật khẩu mới:</label>
                  <input type="password" name="new_password" required>

                  <label>Xác nhận mật khẩu:</label>
                  <input type="password" name="confirm_password" required>

                  <button type="submit" name="set_new_password">Xác nhận</button>
            </form>
      <?php endif; ?>
</div>
</body>
</html>
