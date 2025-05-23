<?php
class infoModel extends DB {
    public function updateProfile($username, $data) {
        $sql = "UPDATE nhanvien SET HOTEN = ?, SDT = ? WHERE TENDANGNHAP = ?";
        $stmt = $this->con->prepare($sql);
    
        if (!$stmt) {
            error_log("Lỗi prepare: " . $this->con->error);
            return false;
        }
    
        $stmt->bind_param("sss", $data['HOTEN'], $data['SDT'], $username);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function getUserByUsername($username) {
        $sql = "SELECT * FROM nhanvien WHERE TENDANGNHAP = ?";
        $stmt = $this->con->prepare($sql);

        if (!$stmt) {
            error_log("Lỗi prepare: " . $this->con->error);
            return false;
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Nếu không có ảnh đại diện, gán ảnh mặc định
        if (empty($user['ANHDAIDIEN'])) {
            $user['ANHDAIDIEN'] = 'frontend/images/anhdaidien/defaultAvatar.jpg';
        }
        return $user;
    }

    public function updateAvatar($username, $avatarPath) {
        $sql = "UPDATE nhanvien SET ANHDAIDIEN = ? WHERE TENDANGNHAP = ?";
        $stmt = $this->con->prepare($sql);

        if (!$stmt) {
            error_log("Lỗi prepare: " . $this->con->error);
            return false;
        }

        $stmt->bind_param("ss", $avatarPath, $username);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function doimatkhau($username, $mkcu, $mkmoi) {
        $sql = "SELECT MATKHAU FROM nhanvien WHERE TENDANGNHAP = ?";
        $stmt = $this->con->prepare($sql);
    
        if (!$stmt) {
            error_log("Lỗi prepare: " . $this->con->error);
            return "Lỗi hệ thống";
        }
    
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return "Người dùng không tồn tại";
        }
    
        $user = $result->fetch_assoc();
        $storedPassword = $user['MATKHAU'];
        
        // So sánh mật khẩu plain text (không dùng password_verify)
        if ($mkcu !== $storedPassword) {
            return "Mật khẩu hiện tại không đúng";
        }
    
        // Lưu mật khẩu mới dạng plain text (không hash)
        $updateSql = "UPDATE nhanvien SET MATKHAU = ? WHERE TENDANGNHAP = ?";
        $updateStmt = $this->con->prepare($updateSql);
        
        if (!$updateStmt) {
            error_log("Lỗi prepare: " . $this->con->error);
            return "Lỗi hệ thống";
        }
    
        $updateStmt->bind_param("ss", $mkmoi, $username);
        $success = $updateStmt->execute();
        $updateStmt->close();
        
        return $success ? true : "Lỗi khi cập nhật mật khẩu";
    }
}
