<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Thông tin cá nhân' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .avatar-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }
        .avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .avatar-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #4CAF50;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .avatar-upload input {
            display: none;
        }
        .profile-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .info-item {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
        }
        .edit-field {
            display: none;
        }
        .btn-edit {
            margin-top: 20px;
        }
        .hidden {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-container">
            <h2 class="text-center mb-4">Thông tin cá nhân</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- Avatar Section -->
            <div class="avatar-container">
                <?php
                $avatarPath = !empty($user['ANHDAIDIEN']) && file_exists($user['ANHDAIDIEN']) 
                            ? $user['ANHDAIDIEN'] 
                            : 'frontend/images/anhdaidien/defaultAvatar.jpg';
                ?>
                <img src="<?= htmlspecialchars($avatarPath) ?>" 
                     alt="Avatar" 
                     class="avatar"
                     id="avatarImage"
                     onerror="this.src='frontend/images/anhdaidien/defaultAvatar.jpg'">
                
                <label class="avatar-upload" for="avatarInput" title="Đổi ảnh đại diện">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="avatarInput" accept="image/jpeg,image/png,image/gif">
                </label>
            </div>

            <!-- Info Section -->
            <form id="profileForm">
                <input type="hidden" name="MANV" value="<?= htmlspecialchars($user['MANV'] ?? '') ?>">
                
                <div class="info-item">
                    <div class="info-label">Mã nhân viên</div>
                    <div class="info-value"><?= htmlspecialchars($user['MANV'] ?? '') ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Họ tên</div>
                    <div class="info-value view-mode" id="view-hoten"><?= htmlspecialchars($user['HOTEN'] ?? '') ?></div>
                    <input type="text" class="form-control edit-mode hidden" id="edit-hoten" name="HOTEN" 
                           value="<?= htmlspecialchars($user['HOTEN'] ?? '') ?>" required>
                </div>

                <div class="info-item">
                    <div class="info-label">Số điện thoại</div>
                    <div class="info-value view-mode" id="view-sdt"><?= htmlspecialchars($user['SDT'] ?? '') ?></div>
                    <input type="tel" class="form-control edit-mode hidden" id="edit-sdt" name="SDT" 
                           value="<?= htmlspecialchars($user['SDT'] ?? '') ?>" pattern="[0-9]{10,11}" required>
                    <small class="form-text text-muted edit-mode hidden">Số điện thoại 10-11 chữ số</small>
                </div>

                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($user['EMAIL'] ?? '') ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Vai trò</div>
                    <div class="info-value">
                        <?= isset($user['LOAI']) ? ($user['LOAI'] === 'Admin' ? 'Quản trị viên' : 'Nhân viên') : '' ?>
                    </div>
                </div>

                <div class="text-center btn-edit">
                    <button type="button" class="btn btn-primary" id="editBtn">
                        <i class="fas fa-edit"></i> Chỉnh sửa
                    </button>
                    <button type="submit" class="btn btn-success hidden" id="saveBtn">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                    <button type="button" class="btn btn-secondary hidden" id="cancelBtn">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="button" class="btn btn-link" id="forgotPasswordBtn">
                        Đổi mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal đổi mật khẩu -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm">
                        <div class="form-group">
                            <label for="currentPassword">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                            <small class="form-text text-muted">Ít nhất 5 ký tự</small>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitChangePassword">Đổi mật khẩu</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script>
    $(document).ready(function() {
        function showAlert(type, message) {
            var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show">' +
                            message +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '</div>';
            
            $('.profile-container').prepend(alertHtml);
            
            // Auto dismiss after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }
        // ========== CHANGE PASSWORD ==========
        $(document).on('click', '#forgotPasswordBtn', function(e) {
            e.preventDefault();
            $('#changePasswordModal').modal('show');
        });

        $(document).on('click', '#submitChangePassword', function(e) {
            e.preventDefault();
            
            // Lấy giá trị từ form
            var currentPassword = $('#currentPassword').val().trim();
            var newPassword = $('#newPassword').val().trim();
            var confirmPassword = $('#confirmPassword').val().trim();

            // Validate
            if (!currentPassword || !newPassword || !confirmPassword) {
                alert('Vui lòng điền đầy đủ thông tin');
                return;
            }
            
            if (newPassword.length < 5) {
                alert('Mật khẩu mới phải có ít nhất 5 ký tự');
                return;
            }
            
            if (newPassword !== confirmPassword) {
                alert('Mật khẩu xác nhận không khớp');
                return;
            }

            // Gửi AJAX
            $.ajax({
                url: 'infoControl/changePassword',
                type: 'POST',
                data: {
                    currentPassword: currentPassword,
                    newPassword: newPassword,
                    confirmPassword: confirmPassword
                },
                success: function(response) {
                    try {
                        var data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.status === 'success') {
                            alert('Đổi mật khẩu thành công!');
                            $('#changePasswordModal').modal('hide');
                            $('#changePasswordForm')[0].reset();
                        } else {
                            alert(data.message || 'Đổi mật khẩu thất bại');
                        }
                    } catch (e) {
                        alert('Lỗi xử lý dữ liệu từ server');
                        console.error(e);
                    }
                },
                error: function(xhr) {
                    alert('Lỗi kết nối: ' + xhr.statusText);
                    console.error(xhr);
                }
            });
        });
        // ========== AVATAR UPLOAD ==========
        $('#avatarInput').change(function() {
            if (this.files && this.files[0]) {
                var formData = new FormData();
                formData.append('avatar', this.files[0]);
                
                $.ajax({
                    url: 'infoControl/uploadAvatar',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#avatarImage').attr('src', response.avatarPath + '?' + new Date().getTime());
                            showAlert('success', 'Cập nhật ảnh đại diện thành công');
                        } else {
                            showAlert('danger', response.message || 'Có lỗi xảy ra khi upload ảnh');
                        }
                    },
                    error: function(xhr) {
                        showAlert('danger', 'Lỗi hệ thống: ' + xhr.statusText);
                    }
                });
            }
        });

        // ========== EDIT PROFILE ==========
        $('#editBtn').click(function() {
            $('.view-mode').addClass('hidden');
            $('.edit-mode').removeClass('hidden');
            $('#editBtn').addClass('hidden');
            $('#saveBtn, #cancelBtn').removeClass('hidden');
        });

        $('#cancelBtn').click(function() {
            $('.view-mode').removeClass('hidden');
            $('.edit-mode').addClass('hidden');
            $('#editBtn').removeClass('hidden');
            $('#saveBtn, #cancelBtn').addClass('hidden');
            
            // Reset values
            $('#edit-hoten').val($('#view-hoten').text());
            $('#edit-sdt').val($('#view-sdt').text());
        });

        // ========== SUBMIT PROFILE FORM ==========
        $('#profileForm').submit(function(e) {
            e.preventDefault();
            
            if ($('#edit-hoten').val().trim() === '' || $('#edit-sdt').val().trim() === '') {
                showAlert('danger', 'Vui lòng điền đầy đủ thông tin');
                return;
            }

            if (!/^[0-9]{10,11}$/.test($('#edit-sdt').val())) {
                showAlert('danger', 'Số điện thoại không hợp lệ');
                return;
            }

            $.ajax({
                url: 'infoControl/updateProfile',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status === 'success') {
                        // Update view mode values
                        $('#view-hoten').text($('#edit-hoten').val());
                        $('#view-sdt').text($('#edit-sdt').val());
                        
                        // Switch back to view mode
                        $('.view-mode').removeClass('hidden');
                        $('.edit-mode').addClass('hidden');
                        $('#editBtn').removeClass('hidden');
                        $('#saveBtn, #cancelBtn').addClass('hidden');
                        
                        showAlert('success', 'Cập nhật thông tin thành công');
                    } else {
                        showAlert('danger', response.message || 'Có lỗi xảy ra khi cập nhật');
                    }
                },
                error: function(xhr) {
                    showAlert('danger', 'Lỗi hệ thống: ' + xhr.statusText);
                }
            });
        });
    });
    </script>
</body>
</html>