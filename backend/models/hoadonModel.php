<?php

class hoadonModel extends model {
    protected $table = 'hoadon';
    protected $primaryKey = 'MAHD';

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function delete($mahd) {
        return $this->db->delete($this->table, $this->primaryKey . " = ?", [$mahd]);
    }

    // Các phương thức khác nếu cần
}