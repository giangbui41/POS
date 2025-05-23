<?php

class khachhangModel extends model {
    protected $table = 'khachhang';
    protected $primaryKey = 'MAKH';

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function findOneBy($field, $value) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$field} = ? LIMIT 1");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Các phương thức khác nếu cần
}