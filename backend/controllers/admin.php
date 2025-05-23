<?php
class admin extends control {
    public $sp;
    public $kh;
    public $nv; 
    public function __construct() {
        // Nạp model
        $this->folder = "admin";
        $this->sp = $this->model("productModel");
        $this->kh = $this->model("customerModel");
        $this->nv = $this->model("userModel");

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['tk'])) {
            header('Location: /nhomhuongnoi/source/login');
            exit();
        }

        // Saler cố tình nhập url /admin thì chuyển hướng về lại saler
        if(isset($_SESSION['role'])){ 
            if($_SESSION['role'] === 'staff'){
                header('Location: /nhomhuongnoi/source/saler');
            }
        }

    }

    public function index(){
        $username = $_SESSION['tk']; 
        $admin = $this->nv->layTenvarole($username);  

        if (!$admin) {
            // Nếu không tìm thấy admin, có thể redirect hoặc thông báo lỗi
            echo "Không tìm thấy admin.";
            return;
        }

        $data = ['admin' => $admin];  // Truyền thông tin vào view
        $this->render('dashboard', $data);
    }
    
    public function handleAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $module = $_POST['module'] ?? '';
            $action = $_POST['name'] ?? '';

            switch ($module) {
                case 'category':
                    $this->actionCategory($action);
                    break;
                case 'product':
                    $this->actionProduct($action);
                    break;
                case 'staff':
                    $this->actionStaff($action);
                    break;

                default:
                    echo 'Invalid module';
                    break;
            }
        }
    }

    //Category
    public function category(){ 
        $products = $this->sp->getSPtheoDM();
		$categories = $this->sp->getAllcategory();
        // Load view, hiện thị view
		$data = [
			'products' => $products,
			'categories' => $categories  
		];

        $this->render('category', $data);
    }

    function actionCategory($action = NULL) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'get') {
            // Nếu là GET request, trả về HTML của bảng danh mục
            $categories = $this->sp->getAllcategory();
            $products = $this->sp->getSPtheoDM();
            
            ob_start();
            include 'backend/views/admin/category_table.php';
            $html = ob_get_clean();
            echo $html;
            exit;
        }

        $md = $this->sp;

        switch ($action) {
            case 'add':
                error_log(print_r($_POST, true));
                $tendanhmuc = trim($_POST['TENDANHMUC'] ?? '');
                $mota = trim($_POST['MOTA'] ?? '');
                $nguoitao = trim($_POST['NGUOITAO'] ?? '');
                $madm = uniqid("DM");

                if (empty($tendanhmuc) || empty($mota) || empty($nguoitao)) {
                    echo "Vui lòng nhập đầy đủ thông tin!";
                    return;
                }

                $data = array($madm, $tendanhmuc, $mota, date("Y-m-d"), $nguoitao);
                $result = $md->insert("danhmuc",$data );

                if ($result) {
                    echo "success";
                } else {
                    echo "Lỗi khi thêm danh mục!";
                }
                break;

            case 'edit':
                $madm = $_POST['MADM'] ?? '';
                $tendanhmuc = trim($_POST['TENDANHMUC'] ?? '');
                $mota = trim($_POST['MOTA'] ?? '');
                $nguoitao = trim($_POST['NGUOITAO'] ?? '');

                if (empty($madm) || empty($tendanhmuc) || empty($mota) || empty($nguoitao)) {
                    echo "Thiếu thông tin để cập nhật!";
                    return;
                }

                $result = $md->update(
                    "danhmuc",
                    ["TENDANHMUC", "MOTA", "NGUOITAO"],
                    [$tendanhmuc, $mota, $nguoitao],
                    "MADM = '$madm'"
                );

                echo $result ? "success" : "Lỗi khi cập nhật danh mục!";
                break;

                case 'del':
                    $madm = $_POST['MADM'] ?? '';
                    
                    if (empty($madm)) {
                        echo "Thiếu mã danh mục cần xóa!";
                        return;
                    }
                
                    // Kiểm tra ràng buộc
                    $productCount = $md->select("COUNT(*) as count", "sanpham", "DANHMUC = ?", [$madm]);
                    if ($productCount && $productCount[0]['count'] > 0) {
                        echo "Không thể xóa danh mục vì có sản phẩm thuộc danh mục này!";
                        return;
                    }
                
                    // Gọi hàm delete
                    $result = $md->delete("danhmuc", "MADM", $madm);
                    echo $result ? "success" : "Không thể xóa danh mục!";
                    break;
            case 'search':
                $keyword = $_POST['keyword'] ?? '';
                $categories = $this->sp->searchCategories($keyword);
                $products = $this->sp->getSPtheoDM();
                
                ob_start();
                include 'backend/views/admin/category_table.php';
                $html = ob_get_clean();
                echo $html;
                exit;
                break;
            default:
                echo "Hành động không hợp lệ!";
                break;
        }
	}

    //PRODUCT
    public function product(){ 
        // Lấy dữ liệu sản phẩm từ model
        $products = $this->sp->getSP();
		$categories = $this->sp->getDM();
        // Load view, hiện thị view
		$data = [
			'products' => $products,
			'categories' => $categories 
		];

        $this->render('product', $data);
    }
    function actionProduct($action = NULL ){
		if($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'get'){
            $products = $this->sp->getSP();
            $categories = $this->sp->getDM();

            ob_start();
            include 'backend/views/admin/product_table.php';
            $html = ob_get_clean();
            echo $html;
            exit;
        }

        $pro = $this->sp;

        switch($action){
            case 'add':
                error_log(print_r($_POST, true));
                $tensp = trim($_POST['TENSP'] ?? '');
                $giagoc = trim($_POST['GIAGOC'] ?? '');
                $giabanle = trim($_POST['GIABANLE'] ?? '');
                $soluong = trim($_POST['SOLUONG'] ?? '');
                $madm = trim($_POST['DANHMUC'] ?? '');
                $barcode = trim($_POST['BARCODE'] ?? '');
                $masp = uniqid("SP");

                if (empty($tensp) || empty($giagoc) || empty($giabanle) || empty($soluong) ) {
                    echo "Vui lòng nhập đầy đủ thông tin!";
                    return;
                }

                // Validate số tiền
                if (!is_numeric($giagoc) || !is_numeric($giabanle) || $giagoc <= 0 || $giabanle <= 0) {
                    echo "Giá sản phẩm phải là số dương!";
                    return;
                }

                if (empty($barcode)) {
                    echo "Vui lòng nhập barcode!";
                    return;
                }

                $existingBarcode = $pro->select("MASP", "sanpham", "BARCODE = ?", [$barcode]);
                    if ($existingBarcode) {
                        echo "Barcode đã tồn tại!";
                        return;
                    }

                $anhsp = '';
                if(isset($_FILES['ANHSP']) && $_FILES['ANHSP']['error'] == UPLOAD_ERR_OK){ 
                    $upload = 'frontend/images/anhsp/';
                    if(!file_exists($upload)){
                        mkdir($upload, 0777, true);
                    }

                    $fileExt = pathinfo($_FILES['ANHSP']['name'], PATHINFO_EXTENSION); 
                    $filename = $masp. '.' .$fileExt;
                    $targetPath = $upload . $filename;

                    // Kiểm tra loại file
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array(strtolower($fileExt), $allowedTypes)) {
                        echo "Chỉ chấp nhận file ảnh JPG, JPEG, PNG hoặc GIF!";
                        return;
                    }

                    // Di chuyển file upload vào thư mục đích
                    if (move_uploaded_file($_FILES['ANHSP']['tmp_name'], $targetPath)) {
                        $anhsp = $targetPath;
                    } else {
                        echo "Lỗi khi upload ảnh sản phẩm!";
                        return;
                    }
                } else {
                    echo "Vui lòng chọn ảnh sản phẩm!";
                    return;
                }

                $data = array($masp, $barcode, $tensp, $anhsp, $giagoc, $giabanle, $soluong, $madm, date("Y-m-d"));
                $result = $pro->insert("sanpham",$data );

                if ($result) {
                    echo "success";
                } else {
                    echo "Lỗi khi thêm danh mục!";
                }
                break;
            case 'edit':
                // Debug dữ liệu nhận được
                error_log("POST data: " . print_r($_POST, true));
                error_log("FILES data: " . print_r($_FILES, true));
                
                $masp = $_POST['MASP'] ?? '';
                $tensp = trim($_POST['TENSP'] ?? '');
                $giagoc = trim($_POST['GIAGOC'] ?? '');
                $giabanle = trim($_POST['GIABANLE'] ?? '');
                $soluong = trim($_POST['SOLUONG'] ?? '');
                $danhmuc = trim($_POST['DANHMUC'] ?? '');
                $currentImage = trim($_POST['CURRENT_IMAGE'] ?? '');
                $barcode = trim($_POST['BARCODE'] ?? '');
            
                // Kiểm tra dữ liệu bắt buộc
                $requiredFields = [
                    'Mã sản phẩm' => $masp,
                    'Tên sản phẩm' => $tensp,
                    'Giá gốc' => $giagoc,
                    'Giá bán lẻ' => $giabanle,
                    'Số lượng' => $soluong,
                    'Danh mục' => $danhmuc
                ];
            
                foreach ($requiredFields as $field => $value) {
                    if (empty($value)) {
                        echo "Thiếu thông tin: $field";
                        return;
                    }
                }
            
                // Validate số liệu
                if (!is_numeric($giagoc) || !is_numeric($giabanle) || !is_numeric($soluong) || 
                    $giagoc <= 0 || $giabanle <= 0 || $soluong < 0) {
                    echo "Giá và số lượng phải là số dương!";
                    return;
                }

                if (empty($barcode)) {
                    echo "Vui lòng nhập barcode!";
                    return;
                }
            
                // Xử lý ảnh
                $anhsp = $currentImage;
                if (!empty($_FILES['ANHSP']['name'])) {
                    $uploadDir = 'frontend/images/anhsp/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
            
                    $fileExt = pathinfo($_FILES['ANHSP']['name'], PATHINFO_EXTENSION);
                    $fileName = $masp . '.' . $fileExt;
                    $targetPath = $uploadDir . $fileName;
            
                    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array(strtolower($fileExt), $allowedTypes)) {
                        echo "Chỉ chấp nhận file ảnh JPG, JPEG, PNG hoặc GIF!";
                        return;
                    }
            
                    if (move_uploaded_file($_FILES['ANHSP']['tmp_name'], $targetPath)) {
                        if (!empty($currentImage) && file_exists($currentImage)) {
                            unlink($currentImage);
                        }
                        $anhsp = $targetPath;
                    } else {
                        echo "Lỗi khi upload ảnh sản phẩm!";
                        return;
                    }
                }

                // Kiểm tra barcode đã tồn tại cho sản phẩm khác
                $existingBarcode = $pro->select("MASP", "sanpham", "BARCODE = ? AND MASP != ?", [$barcode, $masp]);
                if ($existingBarcode) {
                    echo "Barcode đã tồn tại cho sản phẩm khác!";
                    return;
                }
            
                $result = $pro->update(
                    "sanpham",
                    ["BARCODE", "TENSP", "ANHSP", "GIAGOC", "GIABANLE", "SOLUONG", "DANHMUC"],
                    [$barcode, $tensp, $anhsp, $giagoc, $giabanle, $soluong, $danhmuc],
                    "MASP = '$masp'"
                );
            
                echo $result ? "success" : "Lỗi khi cập nhật sản phẩm!";
                break;
            case 'del':
                $masp = $_POST['MASP'] ?? '';
                $currentImage = trim($_POST['CURRENT_IMAGE'] ?? '');

                if (empty($masp)) {
                    echo "Thiếu mã sản phẩm cần xóa!";
                    return;
                }

                //Kiểm tra sản phẩm trong đơn hàng
                $checkOrder = $pro->select("COUNT(*) as count", "cthd", "MASP = '$masp'");
                if ($checkOrder[0]['count'] > 0) {
                    echo "Không thể xóa sản phẩm vì đã tồn tại trong đơn hàng!";
                    return;
                }

                $result = $pro->delete("sanpham", "MASP", $masp);

                if ($result) {
                    if (!empty($currentImage) && file_exists($currentImage)) {
                        unlink($currentImage);
                    }
                    echo "success";
                } else {
                    echo "Không thể xóa sản phẩm!";
                }
                break;

            case 'search':
                $keyword = $_POST['keyword'] ?? '';
                $products = $this->sp->searchProducts($keyword);
                $categories = $this->sp->getDM();
                
                ob_start();
                include 'backend/views/admin/product_table.php';
                $html = ob_get_clean();
                echo $html;
                exit;
                break;
            default:
                echo "Hành động không hợp lệ!";
                break;
        }
	}

    public function customer(){
        // Model
        $khachhang = $this->kh->getKH();
        $data = ['khachhang' => $khachhang];
        $this->render('customer', $data);
    }


    public function staff(){ //action quản lý nhân viên của admin
        // Model
        $staff = $this->nv->getStaffList();
        $data = ['staff' => $staff];
        // render
        $this->render("staff", $data);
    }
     // Xử lý các action liên quan đến nhân viên
     public function actionStaff($action = null) {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'get'){
            $staff = $this->nv->getStaffList();

            ob_start();
            include 'backend/views/admin/staff_table.php';
            $html = ob_get_clean();
            echo $html;
            exit;
        }

        $model = $this->nv;

        switch ($action) {
            case 'add':
                // Tạo nhân viên mới
                error_log(print_r($_POST, true));
                $name = trim($_POST['HOTEN'] ?? '');
                $email = trim($_POST['EMAIL'] ?? '');

                if (empty($name) || empty($email)) {
                    echo "Vui lòng nhập đầy đủ thông tin!";
                    return;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "Email không hợp lệ!";
                    return;
                }

                // Tạo nhân viên và gửi email kích hoạt
                $result = $model->createNewStaffAndSendToken($name, $email);
                
                if ($result) {
                    echo "success";
                } else {
                    echo "Lỗi khi thêm!";
                }
                break;

            case 'lock':
                // Khóa tài khoản nhân viên
                $id = $_POST['MANV'] ?? '';
                if (empty($id)) {
                    echo "Thiếu mã nhân viên!";
                    return;
                }
            
                $result = $model->setAccountStatus($id, 'Locked');
                echo $result ? "success" : "Lỗi khi khóa tài khoản!";
                break;
            
            case 'unlock':
                // Mở khóa tài khoản nhân viên
                $id = $_POST['MANV'] ?? '';
                if (empty($id)) {
                    echo "Thiếu mã nhân viên!";
                    return;
                }
            
                $result = $model->setAccountStatus($id, 'Unlocked');
                echo $result ? "success" : "Lỗi khi mở khóa tài khoản!";
                break;

            case 'resend':
                // Gửi lại liên kết đăng nhập
                $id = $_POST['MANV'] ?? '';
                if (empty($id)) {
                    echo "Thiếu mã nhân viên!";
                    return;
                }

                $result = $model->resendToken($id);
                echo $result === "Gửi lại token thành công." ? "success" : $result;
                break;

            case 'search':
                $keyword = $_POST['keyword'] ?? '';
                $staff = $model->TimNV($keyword);
                
                ob_start();
                include 'backend/views/admin/staff_table.php';
                $html = ob_get_clean();
                echo $html;
                break;

            case 'delete':
                // Xóa nhân viên
                $id = $_POST['MANV'] ?? '';
                if (empty($id)) {
                    echo "Thiếu mã nhân viên!";
                    return;
                }
        
                $result = $model->deleteStaffById($id);
                echo $result ? "success" : "Lỗi khi xóa nhân viên!";
                break;
                
            default:
                echo "Hành động không hợp lệ!";
                break;
        }
    }

}
?>