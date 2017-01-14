<?php
namespace db\QueryBuilder\Operations;

trait Update {
	
	public function set($data) {
		$fields = array();
		
		foreach($data as $field => $value) {
			$fields[] = $this->fieldToValue($field, $value);
		}
		
		$fields_sql = implode(',', $fields);
		
		$this->_update($fields_sql);
	}
	
	public function increment($field) {
		$fields_sql = $this->_field($field)."=(".$this->field($field)." + 1)";
		
		$this->_update($fields_sql);
	}
	
	public function decrement($field) {
		$fields_sql = $this->_field($field)."=(".$this->field($field)." - 1)";
		
		$this->_update($fields_sql);
	}
	
	public function toggle($field) {
		$fields_sql = $this->_field($field)."=(NOT ".$this->field($field).")";
		
		$this->_update($fields_sql);
	}
	
	private function _update($fields_sql) {
		$sql = "UPDATE ".$this->_table()." SET ".$fields_sql.$this->_where();
		
		$this->execute($sql);
	}
	
}
