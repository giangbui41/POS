 
<?php
//<!-- gọi con, model, view từ app -->
require_once "config.php";
require_once './backend/core/helpers.php';

class App {
    // Khai báo controller, action và params
    protected $controller = DEFAULT_CONTROLLER;
    protected $action = DEFAULT_ACTION;
    protected $params = [];

    public function __construct() {
        $arr = $this->UrlProcess();

        // Xử lý controller
        if (isset($arr[0]) && file_exists("./backend/controllers/" . $arr[0] . ".php")) {
            $this->controller = $arr[0];
            unset($arr[0]);
        }
        require_once "./backend/controllers/" . $this->controller . ".php";
        $this->controller = new $this->controller;

        // Xử lý action
        if (isset($arr[1])) {
            if (method_exists($this->controller, $arr[1])) {
                $this->action = $arr[1];
            }
            unset($arr[1]);
        }

        // Xử lý params
        $this->params = $arr ? array_values($arr) : [];

        // Gọi phương thức action với các tham số
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    // Xử lý URL
    private function UrlProcess() {
        if (isset($_GET["url"])) {
            // Cắt chuỗi URL thành các phần tử theo dấu "/"
            $arr = explode("/", filter_var(trim($_GET["url"], "/")));

            // Kiểm tra nếu có chỉ số đầu tiên là "index.php" thì bỏ qua
            if (!empty($arr) && $arr[0] === "index.php") {
                unset($arr[0]);
                // Đảm bảo các key của mảng được đánh lại từ 0
                $arr = array_values($arr);
            }

            return $arr;
        }
        return [];
    }
}
?>
