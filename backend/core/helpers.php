<?php
// Hàm lấy đường dẫn đến thư mục /source/
function base_url() {
    $script_path = $_SERVER['SCRIPT_NAME']; // ví dụ: /bb/source/index.php
    $base_path = strstr($script_path, '/source/', true); // lấy phần trước "/source/"
    return $base_path . '/source/';
}

// Hàm sinh route controller/action
function route($controller, $action) {
    return base_url() . "{$controller}/{$action}";
}
?>
