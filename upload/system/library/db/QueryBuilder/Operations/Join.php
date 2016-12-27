<?php
namespace db\QueryBuilder\Operations;

trait Join {
	
	private $joins = array();
	
	public function join($table, $key1 = null, $key2 = null) {
		$table = $this->addTable($table);
		
		$this->joins[] = $this->createJoinSql("INNER JOIN", $table, $key1, $key2);
		
		return $this;
	}
	
	public function leftJoin($table, $key1, $key2 = null) {
		$table = $this->addTable($table);
		
		$this->joins[] = $this->createJoinSql("LEFT OUTER JOIN", $table, $key1, $key2);
		
		return $this;
	}
	
	public function rightJoin($table, $key1, $key2 = null) {
		$table = $this->addTable($table);
		
		$this->joins[] = $this->createJoinSql("RIGHT OUTER JOIN", $table, $key1, $key2);
		
		return $this;
	}
	
	public function crossJoin($table) {
		$table = $this->addTable($table);
		
		$this->joins[] = $this->createJoinSql("CROSS JOIN", $table);
		
		return $this;
	}
	
	private function createJoinSql($type, $table, $key1 = null, $key2 = null) {
		return $type." ".$this->_tableAsAlias($table).$this->parseJoinConditions($key1, $key2);
	}
	
	private function parseJoinConditions($key1, $key2) {
		if(is_string($key1) and is_null($key2)) {
			if(strpos($key1, '.') !== false) {
				return " ON ".$key1;
			} else {
				return " USING (`".$this->escape($key1)."`)";
			}
		}
		
		if(is_string($key1) and is_string($key2)) {
			return " ON ".$this->_field($key1)." = ".$this->_field($key2);
		}
		
		if(is_array($key1)) {
			return " ON ".$this->parseConditions($key1);
		}
		
		return "";
	}
	
	private function _joins() {
		if($this->joins) {
			return PHP_EOL.implode(PHP_EOL, $this->joins);
		} else {
			return "";
		}
	}
	
}
