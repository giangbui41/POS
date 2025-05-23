<?php
class customerModel extends DB {
    public function getKH()
    {
        $data = $this->select("*", "khachhang");
        return $data;
    }

    public function TimKH($keyword){
        $keyword = $this->con->real_escape_string($keyword);
        error_log("Keyword tìm kiếm: " . $keyword); 
        
        $query = "SELECT kh.*
              FROM khachhang kh
              WHERE kh.TENKH LIKE '%$keyword%'  
                 OR kh.SDT LIKE '%$keyword%'";

        error_log("SQL Query: " . $query); 
        
        $result = $this->con->query($query);
        
        if(!$result) {
            error_log("Lỗi SQL: " . $this->con->error);
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>