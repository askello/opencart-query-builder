<?php

namespace db\QueryBuilder;

class Query {

    use Common\SchemaAnalyzer;
    use Common\Conditions;
    use Common\Order;
    use Common\Limit;

    use Operations\Select;
    use Operations\Insert;
    use Operations\Update;
    use Operations\Delete;

    private $db;
    private $table;

    private $tableAliases = array();

    public function __construct($db, $table) {
        $this->db = $db;

        $table = $this->addTable($table);

        $this->setTable($table);
    }

    public function setTable($table) {
        $this->table = $this->db->escape($table);
    }

    public function addTable($table) {
        $table = DB_PREFIX . $table;

        if (strpos($table, ' ') !== false) {
            $tmp = explode(' ', $table);

            $table = $tmp[0];
            $alias = $tmp[1];
        } else {
            $alias = $table;
        }

        $this->tableAliases[$table] = $alias;

        return $table;
    }

    private function _table($table = null) {
        if (is_null($table)) {
            $table = $this->table;
        }

        return "`" . $table . "`";
    }

    private function _tableAlias($table = null) {
        if (is_null($table)) {
            $table = $this->table;
        }

        if (isset($this->tableAliases[$table])) {
            $alias = $this->tableAliases[$table];
        } else {
            $alias = $table;
        }

        return "`" . $alias . "`";
    }

    private function _tableAsAlias($table = null) {
        $alias = $this->_tableAlias($table);
        $table = $this->_table($table);

        if ($table != $alias) {
            return $table . " AS " . $alias;
        }

        return $table;
    }

    private function _field($field) {
        if ($this->isRawSql($field)) {
            return $field;
        }

        if (strpos($field, '.') !== false) {
            $tmp = explode('.', $field);
            $field = $tmp[1] == '*' ? '*' : "`" . $tmp[1] . "`";
            return $this->_tableAlias($tmp[0]) . "." . $field;
        }

        if (count($this->tableAliases) > 1) {
            return $this->_tableAlias() . ".`" . $this->db->escape($field) . "`";
        } else {
            return "`" . $this->db->escape($field) . "`";
        }
    }

    private function fieldToValue($field, $operator, $value = null) {
        if (is_null($value)) {
            $value = $operator;
            $operator = "=";
        }

        return $this->_field($field) . $operator . $this->_value($value);
    }

    private function _value($value) {
        if (is_null($value)) return 'NULL';

        return "'" . $this->db->escape($value) . "'";
    }

    private function isRawSql($str) {
        return preg_match('/[()<>=`\'\ +*\-\/"]/', $str);
    }

    private function isArrayAssoc($arr) {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function execute($sql) {
        return $this->db->query($sql);
    }

}
