<?php
namespace db\QueryBuilder\Operations;

trait Insert {
	
	public function insert($data) {
		if(!is_array_assoc($data)) {
			$keys = array();
			
			foreach($data as $data1) {
				$keys[] = $this->_insert($data1);
			}
			
			return $keys;
		}
		
		return $this->_insert($data);
	}
	
	private function _insert($data) {
		$fields = array();
		
		foreach($data as $field => $value) {
			$fields[] = $this->fieldToValue($field, $value);
		}
		
		$fields = implode(',', $fields);
		
		$sql = "INSERT INTO ".$this->_table()." SET ".$fields;
		$this->execute($sql);
		
		return $this->driver->getLastId();
	}
	
}
