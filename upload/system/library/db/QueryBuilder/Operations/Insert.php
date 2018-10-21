<?php

namespace db\QueryBuilder\Operations;

trait Insert {

    public function add($data) {
        if (!$this->isArrayAssoc($data)) {
            $keys = array();

            foreach ($data as $data1) {
                $keys[] = $this->_insert($data1);
            }

            return $keys;
        }

        return $this->_insert($data);
    }

    public function addManyFast($data) {
        $this->_insertAll($data);
    }

    private function _insert($data) {
        $fields = array();

        foreach ($data as $field => $value) {
            $fields[] = $this->fieldToValue($field, $value);
        }

        $fields = implode(',', $fields);

        $sql = "INSERT INTO " . $this->_table() . " SET " . $fields;
        $this->execute($sql);

        return $this->db->insertId();
    }

    private function _insertAll($data) {
        if (!$data) return;

        // prepare fields
        $fields = array_keys($data[0]);
        foreach ($fields as &$field) {
            $field = $this->_field($field);
        }
        $fields = implode(',', $fields);

        // prepare values
        $values = [];
        foreach ($data as $row) {
            $sql = PHP_EOL . "   (";

            $v = [];
            foreach($row as $key => $value) {
                $v[] = $this->_value($value);
            }
            $sql .= implode(',', $v);

            $sql .= ")";

            $values[] = $sql;
        }

        $values = implode(',', $values);

        $this->execute("INSERT INTO " . $this->_table() . PHP_EOL . "   (" . $fields . ")" . PHP_EOL . "VALUES" . $values);
    }

}
