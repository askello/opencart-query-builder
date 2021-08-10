<?php

namespace db\QueryBuilder\Operations;

trait Select {

    use Join;
    use Aggregates;

    public function get($fields = null) {
        $fields_sql = $this->prepareFieldsToSelect($fields);

        $sql = "SELECT" . PHP_EOL . "   " . $fields_sql . PHP_EOL . "FROM " . $this->_tableAsAlias() . $this->_joins() . $this->_where() . $this->_order() . $this->_limit();

        $rows = $this->execute($sql)->rows;

        // return value
        if (is_string($fields)) {
            return $this->getFieldValue($this->getFieldName($fields), $rows);
        }

        // return rows
        if ($this->single()) {
            return isset($rows[0]) ? $rows[0] : [];
        } else {
            return $rows;
        }
    }

    public function has($id) {
        return (boolean)$this->find($id)->count();
    }

    private function getFieldValue($field, $rows) {
        if ($this->single()) {
            return isset($rows[0][$field]) ? $rows[0][$field] : null;
        } else {
            $values = array();

            foreach ($rows as $row) {
                $values[] = isset($row[$field]) ? $row[$field] : null;
            }

            return $values;
        }
    }

    private function getFieldName($field_sql) {
        if (strpos($field_sql, '.') !== false) {
            $field_sql = explode('.', $field_sql)[1];
        }

        $parts = explode(' ', $field_sql);

        return end($parts);
    }

    private function single() {
        return $this->limitCount == 1;
    }

    private function prepareFieldsToSelect($fields) {
        $fields_sql = "*";

        if (is_array($fields)) {
            $tmp = array();

            foreach ($fields as $field => $alias) {
                if (is_int($field)) {
                    $tmp[] = $this->_field($alias);
                } else {
                    $tmp[] = $this->_field($field) . " AS `" . $alias . "`";
                }
            }

            $fields_sql = implode("," . PHP_EOL . "   ", $tmp);
        } else if (is_string($fields)) {
            $fields_sql = $this->_field($fields);
        }

        return $fields_sql;
    }

}
