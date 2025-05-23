<?php

require_once __DIR__ . '/../models/UserModel.php';




session_start();

$error = '';
$success = '';


$userModel = new UserModel();
$userId = $_SESSION['tk'] ?? null;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_new_password'])) {
      $newPass = $_POST['new_password'] ?? '';
      $confirm = $_POST['confirm_password'] ?? '';

      if (strlen($newPass) < 6) {
            $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
      } elseif ($newPass !== $confirm) {
            $error = 'Xác nhận mật khẩu không đúng.';
      } else {
            if ($userModel->doimatkhau($userId, $newPass)) {
                  unset($_SESSION['tk']);
                  $success = 'Đổi mật khẩu thành công. Bạn có thể đăng nhập ngay.';
            } else {
                  $error = 'Lỗi khi cập nhật mật khẩu.';
            }
      }
}


