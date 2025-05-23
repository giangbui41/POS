<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
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

        form input[type="text"],
        form input[type="password"],
        form input[type="email"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
        }

        form input[type="checkbox"] {
            margin-right: 8px;
        }

        form button,
        form input[type="submit"],
        form input[type="reset"] {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            margin-top: 10px;
        }

        form input[type="submit"] {
            background-color: #4a90e2;
            color: white;
            margin-right: 10px;
        }

        form input[type="reset"] {
            background-color: #e74c3c;
            color: white;
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

        #forgotPasswordModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
        }

    </style>
</head>
<body>
<?php $isResetPassword = isset($_GET['token']); ?>
<div class="container">
    <?php if ($this->error) : ?>
        <p class="error"><?php echo $this->error; ?></p>
    <?php endif; ?>

    <?php if ($this->success) : ?>
        <p class="success"><?php echo $this->success; ?></p>
    <?php endif; ?>

    <h2>Đăng nhập hệ thống</h2>

    
        <form method="post">
            <label>Tài khoản</label>
            <input type="text" name="tk" required />

            <label>Mật khẩu</label>
            <input type="password" name="mk" required />

            <label><input type="checkbox" name="check" checked /> Ghi nhớ</label>

            <input type="submit" name="login" value="Đăng nhập" />
            <input type="reset" value="Làm mới" />
            <label><a href="#" class="btn btn-link" id="forgotPasswordBtn">Quên mật khẩu?</a></label>
        </form>
    
</div>

<!-- Modal quên mật khẩu -->
<div id="forgotPasswordModal">
    <div class="modal-content">
        <h2>Quên mật khẩu</h2>
        <form method="post" action="login.php">
            <label>Email đăng ký:</label>
            <input type="email" name="email" required />
            
            <div style="display:flex; justify-content:space-between; margin-top:20px;">
                <button type="submit" name="forgot_password" style="background-color:#4CAF50; color:white;">Gửi yêu cầu</button>
                <button type="button" id="cancelForgotPassword" style="background-color:#e74c3c; color:white;">Hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('forgotPasswordBtn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('forgotPasswordModal').style.display = 'flex';
});

document.getElementById('cancelForgotPassword').addEventListener('click', function() {
    document.getElementById('forgotPasswordModal').style.display = 'none';
});
</script>
</body>
</html>