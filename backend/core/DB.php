<?php
require_once 'config.php';
class DB{

    public $con;
    function __construct(){
        $this->con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$this->con) {
            die("Kết nối thất bại: " . mysqli_connect_error());
        }
        mysqli_query($this->con, "SET NAMES 'utf8'");
        error_log("Kết nối database thành công");
    }

    

    function insert($table, $values, $columns = null) {
        if($columns){
            $columnStr = "(" . implode(",", $columns) . ")";
        } else {
            $columnStr = "";
        }
    
        $placeholders = implode(",", array_fill(0, count($values), "?"));
        $sql = "INSERT INTO $table $columnStr VALUES ($placeholders)";
    
        $stmt = $this->con->prepare($sql);
        if(!$stmt) {
            error_log("Prepare failed: " . $this->con->error);
            return false;
        }
    
        $types = str_repeat("s", count($values));
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    function update($table, $setColumns, $setVal, $cond){
        $setStr = implode("=?, ", $setColumns) . "=?";
        $sql = "UPDATE $table SET $setStr WHERE $cond";

        $stmt = $this->con->prepare($sql);
        $types = str_repeat("s", count($setVal));
        $stmt->bind_param($types, ...$setVal);
        return $stmt->execute();
    }

    public function select($columns, $table, $condition = null, $params = []) {
        $sql = "SELECT $columns FROM $table";
        if ($condition) {
            $sql .= " WHERE $condition";
        }
        
        $stmt = $this->con->prepare($sql);
        if (!$stmt) {
            error_log("Lỗi chuẩn bị câu lệnh: " . $this->con->error);
            return false;
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        try {
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return $data;
        } catch (Exception $e) {
            error_log("Lỗi truy vấn SQL: " . $e->getMessage());
            return false;
        } finally {
            $stmt->close();
        }
    }

    public function delete($table, $whereColumn, $value) {
        if (empty($table) || empty($whereColumn)) {
            throw new InvalidArgumentException("Bảng hoặc cột không hợp lệ.");
        }
    
        $sql = "DELETE FROM `$table` WHERE `$whereColumn` = ?";
    
        if ($stmt = $this->con->prepare($sql)) {
            // Gắn giá trị cho tham số
            $stmt->bind_param("s", $value);  // 's' cho kiểu dữ liệu string
    
            try {
                // Thực thi câu lệnh SQL
                $result = $stmt->execute();
                if ($result) {
                    return true;  
                }
                return false; 
            } catch (Exception $e) {
                error_log("Lỗi khi xóa dữ liệu: " . $e->getMessage());
                return false; 
            } finally {
                $stmt->close();
            }
        } else {
            error_log("Lỗi chuẩn bị câu lệnh SQL: " . $this->con->error);
            return false;
        }
    }
    
    
    function exe_query($sql){
        $result = mysqli_query($this->con, $sql);
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($this->con));
        }
        return $result;
    }

    function getListMasp($sql){
        $result = [];
        $query = mysqli_query($this->con, $sql);
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row['masp'];
        }
        return $result;
    }

    function getLastInsertID(){
        return mysqli_insert_id($this->con);
    }

    function __destruct(){
        if ($this->con) {
            mysqli_close($this->con);
        }
    }
}
?>