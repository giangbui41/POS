<?php
// <!-- require model và view vào controller -->
class control {
    public function model($model) {
        require_once "./backend/models/" . $model . ".php";
        return new $model;
    }

    public function view($view, $data=[]){
		require_once "./backend/views/".$view.".php";
	}

    
    protected $folder;
    // public function render($view, $data=[]){
	// 	if (isset($_SESSION['tk'])) {
	// 		$infoModel = $this->model("infoModel");
	// 		$data['user'] = $infoModel->getUserByUsername($_SESSION['tk']);
	// 	}
		
    //     extract($data);
	// 	$file_path = "backend/views/".$this->folder."/".$view.".php";
	// 	if(file_exists($file_path)){

	// 		ob_start();
	// 		require_once($file_path);
	// 		$content = ob_get_clean();
	// 		if($_SESSION['role'] = 'admin'){
	// 			require_once('backend/views/master1.php');	
	// 		} else {
	// 			require_once('backend/views/master2.php');
	// 		}
			
	// 	} else {
	// 		echo "Khong tim thay view";
	// 		echo "<br>".$file_path;
	// 	}
    // }

	public function render($view, $data=[]){
		if (isset($_SESSION['tk'])) {
			$infoModel = $this->model("infoModel");
			$data['user'] = $infoModel->getUserByUsername($_SESSION['tk']);
		}

		extract($data);
		$file_path = "backend/views/".$this->folder."/".$view.".php";

		// check role cho nav và sidebar
		if(file_exists($file_path)){
			ob_start();
			require_once($file_path);
			$content = ob_get_clean();
			if(isset($_SESSION['role'])){
				if($_SESSION['role'] === 'admin'){
					require_once('backend/views/master1.php');
				} elseif ($_SESSION['role'] === 'staff') {
					require_once('backend/views/master2.php'); // Hoặc layout riêng cho saler
				}
			} else {
				require_once('backend/views/login_view.php'); // Layout mặc định nếu không có vai trò
			}
		} else {
			echo "Khong tim thay view";
			echo "<br>".$file_path;
		}
	}
}
?>