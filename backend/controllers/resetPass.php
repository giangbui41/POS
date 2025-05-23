<?php
require_once __DIR__ . '/../models/UserModel.php';
session_start();

$error = '';
$success = '';
$token = $_GET['token'] ?? ($_POST['token'] ?? '');

$userModel = new UserModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $newPass = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$token) {
        $error = 'Liên kết không hợp lệ!';
    } elseif (strlen($newPass) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } elseif ($newPass !== $confirm) {
        $error = 'Xác nhận mật khẩu không đúng.';
    } else {
        $user = $userModel->validToken($token);
        if ($user) {
            $result = $userModel->doimatkhau($user['TENDANGNHAP'], $newPass);
            if ($result) {
                $userModel->clearToken($user['TENDANGNHAP']);
                $success = 'Đặt lại mật khẩu thành công. Bạn có thể đăng nhập ngay.';
            } else {
                $error = 'Lỗi khi cập nhật mật khẩu.';
            }
        } else {
            $error = 'Liên kết không hợp lệ hoặc đã hết hạn.';
        }
    }
}

// truyền biến cho view
require_once __DIR__ . '/../views/resetPass_view.php';
