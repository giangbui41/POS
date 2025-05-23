<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/DB.php';
 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
date_default_timezone_set('Asia/Ho_Chi_Minh');

class userModel extends DB {

      // Lấy thông tin người dùng theo tên đăng nhập
      public function getUserByUsername($username) {
            return $this->select('*', 'NHANVIEN', 'TENDANGNHAP = ?', [$username]);
      }

      // Cập nhật token
      public function updateToken($whereColumn, $whereValue, $token, $expiredAt) {
            // $sql = "UPDATE NHANVIEN SET TOKEN_DANGNHAP = ?, TOKEN_HETHAN = ? WHERE $whereColumn = ?";
            // return $this->update($sql, [$token, $expiredAt, $whereValue]);

            $sql = "UPDATE NHANVIEN SET TOKEN_DANGNHAP = ?, TOKEN_HETHAN = ? WHERE $whereColumn = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("sss", $token, $expiredAt, $whereValue);
            return $stmt->execute();
      }

      // Xóa token
      public function clearToken($username) {
            //return $this->update("UPDATE NHANVIEN SET TOKEN_DANGNHAP = NULL, TOKEN_HETHAN = NULL WHERE TENDANGNHAP = ?", [$username]);
            $sql = "UPDATE NHANVIEN SET TOKEN_DANGNHAP = NULL, TOKEN_HETHAN = NULL WHERE TENDANGNHAP = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("s", $username);
            return $stmt->execute();
      }

      // Kiểm tra token hợp lệ /dùng
      public function validToken($token) {
            $sql = "SELECT *, TIMESTAMPDIFF(SECOND, NOW(), TOKEN_HETHAN) as remaining_seconds 
                        FROM NHANVIEN 
                        WHERE TOKEN_DANGNHAP = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row && $row['remaining_seconds'] > 0) {
                  error_log("Token valid. Remaining seconds: " . $row['remaining_seconds']);
                  return $row;
            }
            
            error_log("Token invalid or expired. Remaining seconds: " . ($row['remaining_seconds'] ?? 'N/A'));
            return false;
      }

      // Lấy danh sách nhân viên /dùng
      public function getStaffList() {
            return $this->select('*', 'NHANVIEN');
      }

      // Sinh mã nhân viên mới /dùng
      private function generateNewStaffId() {
            $sql = "SELECT MANV FROM NHANVIEN WHERE MANV LIKE 'NV%' ORDER BY MANV DESC LIMIT 1";
            $result = $this->select('MANV', 'NHANVIEN', 'MANV LIKE "NV%" ORDER BY MANV DESC LIMIT 1');
            if (!empty($result)) {
                  $lastId = $result[0]['MANV']; // Ví dụ: NV023
                  $num = intval(substr($lastId, 2)); // Lấy 023 -> 23
                  $new = $num + 1;
            } else {
                  $new = 1;
            }
            return 'NV' . str_pad($new, 3, '0', STR_PAD_LEFT);
      }

      //gửi email
      private function sendTokenEmail($hoten, $email, $token, $subject, $customBody = "") {
            $link = "http://localhost/nhomhuongnoi/source/login.php?token=$token";
            $body = $customBody ?: "Chào $hoten,<br><br>
                                    Một tài khoản đã được tạo cho bạn trên hệ thống.<br>
                                    Vui lòng sử dụng liên kết sau để đăng nhập lần đầu (hiệu lực trong 1 phút):<br>
                                    <a href='$link'>$link</a><br><br>
                                    Sau khi đăng nhập lần đầu, bạn sẽ được yêu cầu đổi mật khẩu.<br>
                                    Nếu bạn không yêu cầu tạo tài khoản này, vui lòng liên hệ quản trị viên.";
        
            $mail = new PHPMailer(true);
            try {
                  $mail->isSMTP();
                  $mail->Host = 'smtp.gmail.com';
                  $mail->SMTPAuth = true;
                  $mail->Username = 'trinhhieungantran@gmail.com';
                  $mail->Password = 'qofi ihmn rrsy pjqg'; 
                  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                  $mail->Port = 587;

                  $mail->setFrom('admin@gmail.com', 'Admin');
                  $mail->addAddress($email, $hoten);
                  $mail->isHTML(true);
                  $mail->CharSet = 'UTF8';
                  $mail->Subject = $subject;
                  $mail->Body = $body;

                  $mail->send();
                  return true;
            } catch (Exception $e) {
                  return "Lỗi gửi email: " . $mail->ErrorInfo;
            }
        }
      
      public function createNewStaffAndSendToken($hoten, $email, $loai = 'Staff') {
            $this->con->query("SET time_zone = '+07:00'");
            $username = explode("@", $email)[0];
            $emailExists = $this->select('EMAIL', 'NHANVIEN', 'EMAIL = ?', [$email]);
            if (!empty($emailExists)) {
                  return ["error" => "Email đã được sử dụng!"];
            }
            if (!empty($this->getUserByUsername($username))) {
                  return ["error" => "Tài khoản đã tồn tại!"];
            }
      
            // Chỉ cho phép tạo nhân viên loại Staff
            if ($loai !== 'Staff') {
                  return ["error" => "Chỉ được phép tạo tài khoản nhân viên bán hàng!"];
            }
      
            if (!$this->createStaff($hoten, $email, $loai)) {
                  return ["error" => "Không thể tạo nhân viên mới!"];
            }
      
            $token = bin2hex(random_bytes(16));
            $expiredAt = date("Y-m-d H:i:s", strtotime("+1 minute"));
            $this->updateToken("TENDANGNHAP", $username, $token, $expiredAt);
      
            $result = $this->sendTokenEmail($hoten, $email, $token, "Tài khoản của bạn đã được tạo");
            return $result === true 
                  ? ["success" => "Tạo nhân viên thành công, email đã được gửi!"]
                  : ["error" => $result];
      }
      
      //tạo nhân viên mới
      private function createStaff($hoten, $email, $loai = 'Staff') {
            $avatar = "frontend/images/anhdaidien/defaultAvatar.jpg";
            $manv = $this->generateNewStaffId();
            $username = explode("@", $email)[0];
            $defaultPassword = "52300266";
            $sql = "INSERT INTO NHANVIEN (ANHDAIDIEN, MANV, HOTEN, EMAIL, TENDANGNHAP, MATKHAU, LOAI, TRANGTHAI, NHANVIENMOI)
                  VALUES (?, ?, ?, ?, ?, ?, ?, 'Unlocked', 1)";
            return $this->insert(
                  'NHANVIEN',
                  [$avatar, $manv, $hoten, $email, $username, $defaultPassword, $loai, 'Unlocked', 1],
                  ["ANHDAIDIEN", "MANV", "HOTEN", "EMAIL", "TENDANGNHAP", "MATKHAU", "LOAI", "TRANGTHAI", "NHANVIENMOI"]
              );              
      }

      public function checkFirstLoginAccess($username) {
            $user = $this->getUserByUsername($username);
            if (!empty($user) && $user[0]['NHANVIENMOI'] == 1) {
                return false;
            }
            return true;
        }

      // Gửi lại link đăng nhập
      public function resendToken($id) {
            $staff = $this->select('HOTEN, EMAIL', 'NHANVIEN', 'MANV = ? AND LOAI = "Staff"', [$id]);
            if (empty($staff)) return "Không tìm thấy nhân viên.";

            $hoten = $staff[0]['HOTEN'];
            $email = $staff[0]['EMAIL'];
            $token = bin2hex(random_bytes(16));
            $expiredAt = date("Y-m-d H:i:s", strtotime("+1 minute"));
            $this->updateToken("MANV", $id, $token, $expiredAt);

            $body = "Chào $hoten,<br><br>
                  Đây là liên kết đăng nhập mới (hiệu lực 1 phút):<br>
                  <a href='http://localhost/nhomhuongnoi/source/login.php?token=$token'>http://localhost/nhomhuongnoi/source/login.php?token=$token</a><br><br>
                  Nếu bạn không yêu cầu, hãy bỏ qua email này.";

            $result = $this->sendTokenEmail($hoten, $email, $token, "Gửi lại liên kết đăng nhập", $body);/////
            return $result === true ? "Gửi lại token thành công." : $result;
      }

      // Khóa/Mở khóa nhân viên 
      public function setAccountStatus($id, $status) {
            $sql = "UPDATE NHANVIEN SET TRANGTHAI = ? WHERE MANV = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("ss", $status, $id); // Thay đổi từ "ii" sang "ss" vì status là string
            return $stmt->execute();
      }

      // Đổi mật khẩu lần đầu và đánh dấu đã đổi
      public function doimatkhau($username, $newPassword) {
            $sql = "UPDATE NHANVIEN SET MATKHAU = ?, NHANVIENMOI = 0 WHERE TENDANGNHAP = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("ss", $newPassword, $username);
            return $stmt->execute();
      }

      // Hàm xác thực đăng nhập
      public function checkLogin($username, $password) {
            $user = $this->getUserByUsername($username);
            if (!empty($user) && $password === $user[0]['MATKHAU']) {
                  return $user[0];
            }
            return false;
      }

      public function layTenvarole($username)
      {
            $sql = "SELECT HOTEN, LOAI FROM nhanvien WHERE TENDANGNHAP = ?";
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
            
            return $user;
      }

      public function TimNV($keyword){
            $keyword = $this->con->real_escape_string($keyword);
            error_log("Keyword tìm kiếm: " . $keyword); 
            
            $query = "SELECT nv.*
                  FROM nhanvien nv
                  WHERE nv.HOTEN LIKE '%$keyword%'  
                  OR nv.SDT LIKE '%$keyword%'
                  OR nv.EMAIL LIKE '%$keyword%'
                  OR nv.MANV LIKE '%$keyword%'";

            error_log("SQL Query: " . $query); 
            
            $result = $this->con->query($query);
        
            if(!$result) {
                  error_log("Lỗi SQL: " . $this->con->error);
                  return [];
            }
        
            return $result->fetch_all(MYSQLI_ASSOC);
      }
      public function deleteStaffById($id) {
            $sql = "DELETE FROM nhanvien WHERE MANV = ?";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param("s", $id);
            return $stmt->execute();
      }


      // Lấy thông tin người dùng theo email
      public function getUserByEmail($email) {
            return $this->select('*', 'NHANVIEN', 'EMAIL = ?', [$email]);
      }
      
      // Gửi email reset mật khẩu
      public function sendResetPasswordEmail($hoten, $email, $token) {
            $link = "http://localhost/nhomhuongnoi/source/backend/controllers/resetPass.php?token=$token";

            $body = "Chào $hoten,<br><br>
                  Bạn đã yêu cầu đặt lại mật khẩu.<br>
                  Vui lòng sử dụng liên kết sau để đặt lại mật khẩu (hiệu lực trong 15 phút):<br>
                  <a href='$link'>$link</a><br><br>
                  Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này.";
      
            return $this->sendTokenEmail($hoten, $email, $token, "Đặt lại mật khẩu", $body);
      }
}
