<?php
class infoControl extends control {
    protected $infoModel;

    public function __construct() {
        $this->folder = "share";
        $this->infoModel = $this->model("infoModel");
    }

    public function index() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['tk'])) {
            header('Location: /nhomhuongnoi/source/login');
            exit();
        }
        $this->profile();
    }

    public function profile() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['tk'])) {
            header('Location: /nhomhuongnoi/source/login');
            exit();
        }

        $username = $_SESSION['tk'];
        $user = $this->infoModel->getUserByUsername($username);

        if (!$user) {
            $_SESSION['error'] = "Không tìm thấy thông tin người dùng";
            header('Location: /');
            exit();
        }

        $data = [
            'user' => $user,
            'pageTitle' => 'Thông tin cá nhân'
        ];

        $this->render("profile", $data);
    }

    public function uploadAvatar() {
        header('Content-Type: application/json'); 

        if (!isset($_SESSION['tk'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit();
        }

        $username = $_SESSION['tk'];

        if (empty($_FILES['avatar']['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng chọn ảnh đại diện']);
            exit();
        }

        // Kiểm tra file upload
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
            echo json_encode(['status' => 'error', 'message' => 'Chỉ chấp nhận file ảnh JPG, PNG hoặc GIF']);
            exit();
        }

        // Tạo thư mục nếu chưa tồn tại
        $uploadDir = 'frontend/images/anhdaidien/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Tạo tên file mới
        $fileExt = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $newFilename = 'avatar_' . $username . '_' . time() . '.' . $fileExt;
        $targetPath = $uploadDir . $newFilename;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
            if ($this->infoModel->updateAvatar($username, $targetPath)) {
                echo json_encode(['status' => 'success', 'avatarPath' => $targetPath]);
            } else {
                unlink($targetPath);
                echo json_encode(['status' => 'error', 'message' => 'Lỗi khi cập nhật ảnh đại diện']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi upload ảnh đại diện']);
        }
    }

    public function updateProfile() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['tk'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit();
        }

        $username = $_SESSION['tk'];
        $requiredFields = ['HOTEN', 'SDT'];

        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin']);
                exit();
            }
        }

        // Validate số điện thoại
        if (!preg_match('/^[0-9]{10,11}$/', $_POST['SDT'])) {
            echo json_encode(['status' => 'error', 'message' => 'Số điện thoại không hợp lệ']);
            exit();
        }

        $data = [
            'HOTEN' => $_POST['HOTEN'],
            'SDT' => $_POST['SDT']
        ];

        $result = $this->infoModel->updateProfile($username, $data);

        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi cập nhật thông tin']);
        }
    }

    public function changePassword() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['tk'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit();
        }
    
        $username = $_SESSION['tk'];
        $currentPassword = $_POST['currentPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        
        error_log("Yêu cầu đổi mật khẩu từ: " . $username);
        // Validate input
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin']);
            exit();
        }
    
        if ($newPassword !== $confirmPassword) {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu mới không khớp']);
            exit();
        }
    
        if (strlen($newPassword) < 5 ) {
            echo json_encode(['status' => 'error', 'message' => 'Mật khẩu phải có ít nhất 5 ký tự']);
            exit();
        }
    
        // Verify current password and update
        $result = $this->infoModel->doimatkhau($username, $currentPassword, $newPassword);
    
        if ($result === true) {
            echo json_encode(['status' => 'success', 'message' => 'Đổi mật khẩu thành công']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $result]);
        }
    }
}