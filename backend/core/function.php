<?php
require_once('DB.php');

$db = new DB(); // Tạo một đối tượng của class DB
$con = $db->con; // Truy cập vào thuộc tính $con của đối tượng


// Input field validation
function validate($inputData){
    global $con;
    $validateData = mysqli_real_escape_string($con, trim($inputData));
    return $validateData;
}

// Redirect from 1 page to another page w the message(status)
function redirect($url, $status){
    $_SESSION['status'] = $status;
    header('Location: ' . $url);
    exit(0); 
}

// Thông báo message hoặc trạng thái
function alertMessage(){
    if(isset($_SESSION['status'])){
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
            <h6>'.$_SESSION['status'].'</h6>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
        unset($_SESSION['status']);
    }
}

// Thêm 
// Thêm
function insert($tableName, $data)
{
    global $con;

    $table = validate($tableName);

    $columns = array_keys($data);
    $validatedValues = array_map('validate', array_values($data));
    $values = $validatedValues;

    $finalColumn = implode(',', $columns);
    $finalValues = "'" . implode("', '", $values) . "'";

    $query = "INSERT INTO $table ($finalColumn) VALUES ($finalValues)";
    $result = mysqli_query($con, $query);

    if (!$result) {
        // Log the error to your PHP error log (recommended for production)
        error_log("Database Insert Error: " . mysqli_error($con) . " - Query: " . $query);
        // For debugging, you can also return the error message
        return "Database Error: " . mysqli_error($con);
    }

    return $result;
}

// Update data dùng function này
function update($tableName, $id, $data){
    global $con;

    $table = validate($tableName);
    $id = validate($id); // $id bây giờ sẽ là giá trị của 

    $updateDataString = "";
    foreach($data as $column => $value){
        $updateDataString .= $column . "='" . validate($value) . "',";
    }

    $finalUpdateData = substr(trim($updateDataString), 0, -1);

    $query = "UPDATE $table SET $finalUpdateData WHERE MAKH='$id'"; // 
    $result = mysqli_query($con, $query);
    return $result;
}

function getAll($tableName, $status = NULL){
    global $con;

    $table = validate($tableName);
    $status = validate($status);

    $query = "SELECT * FROM $table";
    if($status == 'status'){
        $query .= " WHERE status='0'";
    }

    return mysqli_query($con, $query);
}

function getById($tableName, $id){
    global $con;

    $table = validate($tableName);
    $id = validate($id);

    $query = "SELECT * FROM $table WHERE MAKH='$id' LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result){
        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_assoc($result);
            $response = [
                'status' => 200,
                'data' => $row ,
                'message' => 'Dữ liệu tìm thấy'
            ];
        } else {
            $response = [
                'status' => 404,
                'message' => 'Không tìm thấy dữ liệu'
            ];
        }
        return $response;

    } else {
        $response = [
            'status' => 500,
            'message' => 'Lỗi truy vấn cơ sở dữ liệu: ' . mysqli_error($con)
        ];
        return $response;
    }
}

// Delete data from database using id
function delete($tableName, $id){ 
    global $con;

    $table = validate($tableName);
    $id = validate($id);

    $query = "DELETE FROM $table WHERE MAKH='$id' LIMIT 1";
    $result = mysqli_query($con, $query);
    return $result;
}

function checkParamId($paramName) {
    if (isset($_GET[$paramName]) && is_numeric($_GET[$paramName])) {
        return $_GET[$paramName];
    } else {
        return "ID không hợp lệ.";
    }
}

function generateNewCustomerId() {
    global $con;
    $sql = "SELECT MAKH FROM KHACHHANG ORDER BY MAKH DESC LIMIT 1";
    $result = mysqli_query($con, $sql); // Sử dụng trực tiếp mysqli_query
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['MAKH'];
        $newId = $lastId + 1;
        mysqli_free_result($result); // Giải phóng bộ nhớ
    } else {
        $newId = 1;
    }
    return $newId;
}

function jsonResponse($status, $message, $data = null) {
    header('Content-Type: application/json');
    $response = [
        'status' => $status,
        'message' => $message,
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit(); // Ensure the script stops here after sending the JSON
}

?>