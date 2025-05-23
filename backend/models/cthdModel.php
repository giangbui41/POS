<?php

class cthdModel extends model {
    protected $table = 'cthd';
    protected $primaryKey = 'ID';

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    // Các phương thức khác nếu cần
}