<?php

namespace db\QueryBuilder\Operations;

trait Insert {

    public function add($data) {
        $isMultiple = !$this->isArrayAssoc($data);

        if (!$isMultiple) $data = [$data];

        $ids = $this->_insert($data);

        if ($isMultiple) return $ids;

        return $ids[0];
    }

    private function _insert($data) {
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

        $lastId = $this->db->insertId();

        return range($lastId, $lastId + count($data) - 1);
    }

}
