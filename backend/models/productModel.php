<?php
class productModel extends DB {
    public function getSP()
    {
        $data = $this->select("*", "sanpham",null,null);
        return $data;
    }

    public function getSPtheoDM(){
        $sql = $this->select("count(sp.MASP) as tongsp, sp.DANHMUC", "sanpham sp , danhmuc dm", "sp.DANHMUC = dm.MADM GROUP BY sp.DANHMUC");
        return $sql;
    }

    public function getDM() {
        $sql = "SELECT MADM, TENDANHMUC FROM danhmuc"; 
        $result = $this->con->query($sql);
        if ($result->num_rows > 0) {
            $categories = [];
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            return $categories;
        } else {
            return [];
        }
    }

    //category
    function getAllcategory(){
        return $this->select("*", "danhmuc");
    }

    public function searchCategories($keyword) {
        $keyword = $this->con->real_escape_string($keyword);
        error_log("Keyword tìm kiếm: " . $keyword); // Debug
        
        $query = "SELECT d.*, 
                  (SELECT COUNT(*) FROM sanpham WHERE DANHMUC = d.MADM) as TONGSP
                  FROM danhmuc d
                  WHERE d.TENDANHMUC LIKE '%$keyword%' 
                     OR d.MOTA LIKE '%$keyword%' 
                     OR d.NGUOITAO LIKE '%$keyword%'";
        
        error_log("SQL Query: " . $query); // Debug
        
        $result = $this->con->query($query);
        
        if(!$result) {
            error_log("Lỗi SQL: " . $this->con->error);
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchProducts($keyword) {
        $keyword = $this->con->real_escape_string($keyword);
        error_log("Keyword tìm kiếm: " . $keyword); // Debug
        
        $query = "SELECT sp.*, dm.TENDANHMUC 
              FROM sanpham sp
              JOIN danhmuc dm ON sp.DANHMUC = dm.MADM
              WHERE sp.TENSP LIKE '%$keyword%' 
                 OR sp.MASP LIKE '%$keyword%' 
                 OR dm.TENDANHMUC LIKE '%$keyword%'";
        
        error_log("SQL Query: " . $query); // Debug
        
        $result = $this->con->query($query);
        
        if(!$result) {
            error_log("Lỗi SQL: " . $this->con->error);
            return [];
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function checkBarcodeExists($barcode, $excludeMasp = null) {
        $sql = "SELECT COUNT(*) as count FROM sanpham WHERE BARCODE = ?";
        $params = [$barcode];
        
        if ($excludeMasp) {
            $sql .= " AND MASP != ?";
            $params[] = $excludeMasp;
        }
        
        $result = $this->select($sql, null, null, $params); // Sửa cách gọi hàm select
        return $result && $result[0]['count'] > 0;
    }

    public function getById($masp) {
        $stmt = $this->db->prepare("SELECT * FROM sanpham WHERE MASP = ?");
        $stmt->execute([$masp]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
}
?>