<?php
class reportModel extends DB {
    /**
     * Lấy báo cáo tổng hợp bán hàng
     */
    public function getSalesReport($startDate, $endDate, $staffId = null) {
        $report = [
            'tongdoanhthu' => 0,
            'tonghoadon' => 0,
            'tongsp' => 0,
            'loinhuan' => 0,
            'byCategory' => []
        ];

        // Thêm điều kiện lọc theo nhân viên nếu có
        $staffCondition = $staffId ? "AND hd.MANV = ?" : "";
        $params = [$startDate, $endDate];
        if ($staffId) {
            $params[] = $staffId;
        }

        $sql = "SELECT 
                    COUNT(DISTINCT hd.MAHD) as tonghoadon,
                    SUM(ct.SOLUONG) as tongsp,
                    SUM(ct.TONGTIEN) as tongdoanhthu
                FROM hoadon hd
                JOIN cthd ct ON hd.MAHD = ct.MAHD
                WHERE hd.NGAYTAO BETWEEN ? AND ?
                $staffCondition
                -- AND hd.PHUONGTHUC = 'completed'
                ";
        
        $result = $this->querySingle($sql, $params);
        if ($result) {
            $report = array_merge($report, $result);
        }

        // Tính lợi nhuận thực tế
        $sql = "SELECT SUM((ct.DONGIA-sp.GIAGOC)*ct.SOLUONG) as loinhuan
                FROM cthd ct
                JOIN sanpham sp ON ct.MASP = sp.MASP
                JOIN hoadon hd ON ct.MAHD = hd.MAHD
                WHERE hd.NGAYTAO BETWEEN ? AND ?
                $staffCondition
                -- AND hd.PHUONGTHUC = 'completed'
                ";
        
        $profit = $this->queryValue($sql, $params);
        $report['loinhuan'] = $profit ?? 0;

        // Thống kê theo danh mục
        $sql = "SELECT 
                    dm.TENDANHMUC as category,
                    SUM(ct.SOLUONG) as products,
                    SUM(ct.TONGTIEN) as revenue,
                    SUM((ct.DONGIA-sp.GIAGOC)*ct.SOLUONG) as profit
                FROM cthd ct
                JOIN sanpham sp ON ct.MASP = sp.MASP
                JOIN danhmuc dm ON sp.DANHMUC = dm.MADM
                JOIN hoadon hd ON ct.MAHD = hd.MAHD
                WHERE hd.NGAYTAO BETWEEN ? AND ?
                $staffCondition
                -- AND hd.PHUONGTHUC = 'completed'
                GROUP BY dm.TENDANHMUC";
        
        $report['byCategory'] = $this->query($sql, $params);

        return $report;
    }

    public function getDailySalesData($startDate, $endDate, $staffId = null) {
        $staffCondition = $staffId ? "AND hd.MANV = ?" : "";
        $params = [$startDate, $endDate];
        if ($staffId) {
            $params[] = $staffId;
        }

        $sql = "SELECT 
                    DATE(hd.NGAYTAO) as date,
                    COUNT(DISTINCT hd.MAHD) as orders,
                    SUM(ct.SOLUONG) as products,
                    SUM(ct.TONGTIEN) as revenue,
                    SUM((ct.DONGIA-sp.GIAGOC)*ct.SOLUONG) as profit
                FROM hoadon hd
                JOIN cthd ct ON hd.MAHD = ct.MAHD
                JOIN sanpham sp ON ct.MASP = sp.MASP
                WHERE hd.NGAYTAO BETWEEN ? AND ?
                $staffCondition
                -- AND hd.PHUONGTHUC = 'completed'
                GROUP BY DATE(hd.NGAYTAO)
                ORDER BY date";
        
        return $this->query($sql, $params);
    }

    public function DSdonhang($startDate, $endDate, $staffId = null) {
        $staffCondition = $staffId ? "AND hd.MANV = ?" : "";
        $params = [$startDate, $endDate];
        if ($staffId) {
            $params[] = $staffId;
        }

        $sql = "SELECT 
            hd.MAHD as orderId,
            hd.NGAYTAO as orderDate,
            kh.HOTEN as customerName,
            nv.HOTEN as staffName,
            SUM(ct.TONGTIEN) as totalAmount,
            COUNT(ct.MASP) as productCount
        FROM hoadon hd
        LEFT JOIN khachhang kh ON hd.MAKH = kh.MAKH
        LEFT JOIN nhanvien nv ON hd.MANV = nv.MANV
        LEFT JOIN cthd ct ON hd.MAHD = ct.MAHD
        WHERE hd.NGAYTAO BETWEEN ? AND ?
        $staffCondition
        GROUP BY hd.MAHD
        ORDER BY hd.NGAYTAO DESC";
        
        return $this->query($sql, $params);
    }

    private function query($sql, $params = []) {
        $stmt = $this->con->prepare($sql);
        if (!$stmt) return [];
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }
    
    private function querySingle($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result[0] ?? null;
    }
    
    private function queryValue($sql, $params = []) {
        $result = $this->querySingle($sql, $params);
        return $result ? reset($result) : null;
    }

    public function getOrderInfo($orderId) {
        $sql = "SELECT hd.NGAYTAO, kh.HOTEN as TENKH 
                FROM hoadon hd
                LEFT JOIN khachhang kh ON hd.MAKH = kh.MAKH
                WHERE hd.MAHD = ?";
        
        return $this->querySingle($sql, [$orderId]);
    }
    
    public function getOrderDetails($orderId) {
        $sql = "SELECT 
                    sp.TENSP as productName,
                    ct.SOLUONG as quantity,
                    ct.DONGIA as unitPrice,
                    (ct.SOLUONG * ct.DONGIA) as totalPrice
                FROM cthd ct
                JOIN sanpham sp ON ct.MASP = sp.MASP
                WHERE ct.MAHD = ?";
        
        return $this->query($sql, [$orderId]);
    }
    
}