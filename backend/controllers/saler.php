<?php
class saler extends control {
    public $sp;
    public $nv;
    protected $infoModel;
    protected $userModel; // Thêm model user nếu bạn chưa có

    public function __construct() {
        // Nạp model
        $this->folder = "saler";
        $this->sp = $this->model("productModel");
        $this->infoModel = $this->model("infoModel");
        $this->userModel = $this->model("userModel"); // Nạp model user
        $this->nv = $this->model("userModel"); // Giữ lại biến $nv

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['tk'])) {
            header('Location: /nhomhuongnoi/source/login');
            exit();
        }

        // admin thì ko dùng trang saler
        if(isset($_SESSION['role'])){
            if($_SESSION['role'] === 'admin'){
                header('Location: /nhomhuongnoi/source/admin');
            }
        }
    }

    // Trang chủ
    public function index() {
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

    public function product(){
        $products = $this->sp->getSP();
        
        $data = [
            'products' => $products,
            
        ];
        $this->render('product', $data);
    }

    
    // Lịch sử giao dịch
    public function orders() {
        // View
        $this->render('orders', []);
    }

    public function view_orders() {
        // View
        $this->render('view_orders', []);
    }

    //========== Quản lý khách hàng ==========
    public function customer() {
        // View
        $this->render('customer', []);
    }
    public function customer_create() {
        // View
        $this->render('customer_create', []);
    }
    public function customer_edit() {
        $this->render('customer_edit',[]);
    }
    public function customer_delete() {
        $this->render('customer_delete',[]);
    }

    //========== Xem báo cáo =============
    public function report() {
        // View
        $this->render('report', []);
    }


    //========== Xử lý giao dịch ==========
    public function xulygiaodich(){
        $this->render('xulygiaodich',[]);
    }

    public function get_customer() {
        global $con; // Đảm bảo bạn có kết nối cơ sở dữ liệu $con (nếu nó không được quản lý trong model)

        $phone = $_POST['phone'] ?? '';
        $sql = "SELECT MAKH, HOTEN, DIACHI FROM khachhang WHERE SDT = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $phone);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode(['name' => $row['HOTEN'], 'address' => $row['DIACHI'], 'makh' => $row['MAKH']]); // Trả về JSON
        } else {
            echo json_encode(null); // Trả về null nếu không tìm thấy
        }

        mysqli_stmt_close($stmt);
        // Không cần gọi $this->render() ở đây vì chúng ta đang trả về JSON
    }

    public function get_product() {
        header('Content-Type: application/json');
        global $con; // Đảm bảo bạn có kết nối cơ sở dữ liệu $con

        $key = $_GET['key'];
        $sql = "SELECT MASP AS id, TENSP AS name, BARCODE AS barcode, GIABANLE AS price, ANHSP AS image FROM sanpham WHERE TENSP LIKE '%$key%' OR BARCODE = '$key' LIMIT 10";
        $result = $con->query($sql);
        $data = [];

        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data); // trả về file json
        // Không cần gọi $this->render() ở đây vì chúng ta đang trả về JSON
    }

    public function export_invoice(){
        $this->render('export_invoice',[]);
    }

}
?>