<?php
namespace db\QueryBuilder\Common;

trait Conditions {

	private $conditions_sql = '';

	public function where($conditions) {
		$args = func_get_args();

		$sql = $this->createWhereSql($args);

		$this->appendCondition($sql);

		return $this;
	}

	public function orWhere($conditions) {
		$args = func_get_args();

		$sql = $this->createWhereSql($args);

		$this->appendCondition($sql, "OR");

		return $this;
	}

	private function createWhereSql($args) {
		if(count($args) > 1) {
			$sql = $this->parseSingleCondition($args[0], $args[1]);
		} else if(is_array($args[0])) {
			$sql = $this->parseConditions($args[0]);
		} else {
			$sql = $args[0];
		}

		return $sql;
	}

	public function find($keys) {
		if(!is_array($keys)) {
			$this->limit(1);
		}

		$this->where($this->getPrimaryKey(), $keys);

		return $this;
	}

	public function first($limit = 1) {
		$this->limit($limit);

		if($this->_order() == "") {
			$this->sortBy($this->getPrimaryKey());
		}

		return $this;
	}

	public function last($limit = 1) {
		$this->limit($limit);

		if($this->_order() == "") {
			$this->sortBy($this->getPrimaryKey(), "DESC");
		} else {
			$this->sortViceVersa();
		}

		return $this;
	}

	public function random($limit = 1) {
		$this->limit($limit);

		$this->sortBy("RAND()");

		return $this;
	}

	private function parseSingleCondition($field, $value) {
		$field = trim($field);

		$operator = $this->determineOperator($field, $value);
		$field = $this->getConditionField($field);
		$value = $this->parseConditionValue($value);

		return $field." ".$operator." ".$value;
	}

	private function getConditionField($field) {
		$data = explode(' ', $field, 2);
		return $this->_field($data[0]);
	}

	private function parseConditionValue($value) {
		if(is_int($value) or is_float($value)) {
			return $value;
		}

		if(is_array($value)) {
			$values = array();

			foreach($value as $val) {
				if(is_int($val) or is_float($val)) {
					$values[] = $val;
				} else {
					$values[] = "'".$this->escape($val)."'";
				}
			}

			return "(".implode(",", $values).")";
		}

		if(is_null($value)) {
			return "NULL";
		}

		return "'".$this->escape($value)."'";
	}

	private function determineOperator($field, $value) {
		$operator = "=";

		if(strpos($field, ' ') !== false) {
			$data = explode(' ', $field, 2);
			$operator = trim($data[1]);
		}

		if(is_array($value)) {
			if($operator == "!=") {
				return "NOT IN";
			} else {
				return "IN";
			}
		}

		if(is_null($value)) {
			if($operator == "!=") {
				return "IS NOT";
			} else {
				return "IS";
			}
		}

		return $operator;
	}

	private function parseConditions($conditions) {
		$sql = "";

		$delimiter = "";

		foreach($conditions as $field => $value) {
			if(is_int($field)) {
				if(trim(strtoupper($value)) == "OR") {
					$delimiter = " OR ";
					continue;
				}

				if(is_string($value)) {
					$sql .= $delimiter.$value;
				}
			} else {
				$sql .= $delimiter.$this->parseSingleCondition($field, $value);
			}

			$delimiter = " AND ";
		}

		if(count($conditions) > 1) {
			$sql = "(".$sql.")";
		}

		return $sql;
	}

	private function appendCondition($condition, $operator = "AND") {
		if(!$this->conditions_sql and $condition) {
			$this->conditions_sql = PHP_EOL."WHERE ".$condition;
		} else {
			$this->conditions_sql .= " ".$operator." ".$condition;
		}
	}

	private function _where() {
		return $this->conditions_sql;
	}

}
