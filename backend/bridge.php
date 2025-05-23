<?php

require_once "./backend/core/app.php";
require_once "./backend/core/control.php";
require_once "./backend/core/db.php";

$db = new DB();
$con = $db->con;

// Thiết lập vai trò

if (isset($_SESSION['tk'])) {
    $username = $_SESSION['tk'];
    $sql = "SELECT LOAI FROM NHANVIEN WHERE TENDANGNHAP = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['role'] = strtolower($user['LOAI']); // Chuyển về chữ thường để so sánh dễ dàng hơn
    } else {
        $_SESSION['role'] = '';
        // Xử lý nếu không tìm thấy user, có thể log lỗi hoặc thông báo
        unset($_SESSION['tk']);
        header("Location: nhomhuongnoi/source/login.php?error=usernotfound");
        exit();
    }
    $stmt->close();
}


// Khởi chạy ứng dụng
$app = new App();
?>