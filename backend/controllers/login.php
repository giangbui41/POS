<?php

// Đảm bảo session được bắt đầu
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối với database
include_once(__DIR__ . '/../core/DB.php');
require_once(__DIR__ . '/../models/UserModel.php');
date_default_timezone_set('Asia/Ho_Chi_Minh');

class Login {
    public $db;
    public $con;
    public $error = "";
    public $success = "";
    public $token;

    public function __construct() {
        $this->db = new DB();
        $this->con = $this->db->con;
        $this->token = isset($_GET['token']) ? $_GET['token'] : null;
        if ($this->token) {
            $_SESSION['login_token'] = $this->token;
        }        
    }

    // Action index - Đăng nhập
    public function index() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['set_password'])) {
                $this->setPassword();
            } elseif (isset($_POST['login'])) {
                $this->login();
            } elseif (isset($_POST['forgot_password'])) {
                $this->forgotPassword();
            }
        }
        $this->render();
    }

    private function setPassword() {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
    
        if (empty($new_password) || empty($confirm_password)) {
            $this->error = "Vui lòng nhập đầy đủ mật khẩu!";
            return;
        }
        if ($new_password !== $confirm_password) {
            $this->error = "Mật khẩu không khớp!";
            return;
        }
        if (empty($this->token)) {
            $this->error = "Token không hợp lệ!";
            return;
        }
    
        error_log("setPassword: Token nhận được = '$this->token'");
    
        $userModel = new UserModel();
        $row = $userModel->validToken($this->token);
        if ($row) {
            error_log("setPassword: Token hợp lệ. TENDANGNHAP = {$row['TENDANGNHAP']}, EMAIL = {$row['EMAIL']}, Remaining seconds = {$row['remaining_seconds']}");
            
            $result = $userModel->doimatkhau($row['TENDANGNHAP'], $new_password);
            if ($result) {
                $userModel->clearToken($row['TENDANGNHAP']);
                unset($_SESSION['login_token']); // Xóa session token sau khi dùng
                error_log("setPassword: Đổi mật khẩu thành công cho TENDANGNHAP = {$row['TENDANGNHAP']}");
                $this->success = "Mật khẩu đã được đặt! Vui lòng đăng nhập.";
                header('location:login.php');
                exit();
            } else {
                error_log("setPassword: Lỗi cập nhật mật khẩu cho TENDANGNHAP = {$row['TENDANGNHAP']}");
                $this->error = "Lỗi khi cập nhật mật khẩu!";
            }
        } else {
            error_log("setPassword: Token không hợp lệ hoặc hết hạn. Token = '$this->token', Thời gian hiện tại = " . date('Y-m-d H:i:s'));
            $this->error = "Liên kết hết hạn hoặc không hợp lệ!";
        }
    }

    private function login() {
        $tk = $_POST['tk'];
        $mk = $_POST['mk'];

        $sql = "SELECT * FROM NHANVIEN WHERE TENDANGNHAP = ? AND MATKHAU = ?";
        $stmt = $this->con->prepare($sql);
        $stmt->bind_param("ss", $tk, $mk);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows <= 0) {
            $this->error = 'Tài khoản hoặc mật khẩu chưa đúng';
        } else {
            $row = $result->fetch_assoc();

            // Thêm kiểm tra trạng thái tài khoản
            if (strtolower($row['TRANGTHAI']) === 'locked') {
                $this->error = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.';
                return;
            }

            // CHẶN nhân viên mới đăng nhập thẳng
            if ((int)$row['NHANVIENMOI'] === 1) {
                // Nhân viên mới phải có token hợp lệ
                if (!isset($_SESSION['login_token'])) {
                    $this->error = 'Vui lòng đăng nhập qua link trong email!';
                    return;
                }
            
                $userModel = new UserModel();
                $userByToken = $userModel->validToken($_SESSION['login_token']);
            
                if (!$userByToken || $userByToken['TENDANGNHAP'] !== $tk) {
                    $this->error = 'Liên kết không hợp lệ hoặc đã hết hạn!';
                    return;
                }
            
                // Cho phép đăng nhập, sau đó chuyển sang đổi mật khẩu
                $_SESSION['tk'] = $tk;
                $_SESSION['manv'] = $row['MANV'];
                $_SESSION['role'] = strtolower($row['LOAI']);
                header('location: backend/views/force_change_password_view.php');
                exit();
            }
            $_SESSION['tk'] = $tk;
            $_SESSION['manv'] = $row['MANV'];
            $_SESSION['role'] = strtolower($row['LOAI']);
            if ($_SESSION['role'] == 'admin') {
                // Trang chủ admin
                header('Location: index.php?url=admin');
                exit();
            } else {
                header('location: index.php?url=saler'); // Trang nhân viên (hoặc vai trò khác)
                exit();
            }
        }
        $stmt->close();
    }

    private function forgotPassword() {
        if (empty($_POST['email'])) {
            $this->error = "Vui lòng nhập email đăng ký!";
            return;
        }

        $email = $_POST['email'];
        $userModel = new UserModel();
        $user = $userModel->getUserByEmail($email);

        if (empty($user)) {
            $this->error = "Email không tồn tại trong hệ thống!";
            return;
        }

        // Tạo token reset mật khẩu
        $token = bin2hex(random_bytes(16));
        $expiredAt = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // Lưu token vào database
        if ($userModel->updateToken("EMAIL", $email, $token, $expiredAt)) {
            // Gửi email reset mật khẩu
            $result = $userModel->sendResetPasswordEmail(
                $user[0]['HOTEN'],
                $email,
                $token
            );

            if ($result === true) {
                $this->success = "Đã gửi liên kết đặt lại mật khẩu đến email của bạn!";
            } else {
                $this->error = "Lỗi khi gửi email: " . $result;
            }
        } else {
            $this->error = "Lỗi hệ thống, vui lòng thử lại!";
        }
    }

    public function render() {
        include(__DIR__ . '/../views/login_view.php');
    }
    
}
?>