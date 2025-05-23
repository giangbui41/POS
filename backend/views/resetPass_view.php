<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
        input[type=password],
        input[type=submit] {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type=submit] {
            background-color: #4a90e2;
            color: white;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Đặt lại mật khẩu</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (empty($success)): ?>
        <form method="POST" action="">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>" />
            <label>Mật khẩu mới:</label>
            <input type="password" name="new_password" required />

            <label>Xác nhận mật khẩu:</label>
            <input type="password" name="confirm_password" required />

            <input type="submit" name="reset_password" value="Đặt lại mật khẩu" />
        </form>
    <?php endif; ?>
</div>
</body>
</html>
