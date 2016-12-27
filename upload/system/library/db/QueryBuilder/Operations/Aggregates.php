<?php
namespace db\QueryBuilder\Operations;

trait Aggregates {
	
	public function count() {
		return (int)$this->selectAggregate("COUNT(*)");
	}
	
	public function max($field) {
		return $this->selectAggregate("MAX(".$this->_field($field).")");
	}
	
	public function min($field) {
		return $this->selectAggregate("MIN(".$this->_field($field).")");
	}
	
	public function avg($field) {
		return $this->selectAggregate("AVG(".$this->_field($field).")");
	}
	
	public function sum($field) {
		return $this->selectAggregate("SUM(".$this->_field($field).")");
	}
	
	private function selectAggregate($aggregate) {
		$sql = "SELECT".PHP_EOL."   ".$aggregate." AS total".PHP_EOL."FROM ".$this->_tableAsAlias().$this->_joins().$this->_where();
		$result = $this->execute($sql);
		return $result->row['total'];
	}
	
}
