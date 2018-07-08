<?php
namespace db\QueryBuilder\Operations;

trait Update {
	
	public function set($field, $value = null) {
		if(is_array($field)) {
			$data = $field;
			
			$fields = array();

			foreach($data as $field => $value) {
				$fields[] = $this->fieldToValue($field, $value);
			}
			
			$fields_sql = implode(',', $fields);
		} else {
			$fields_sql = $this->fieldToValue($field, $value);
		}

		return $this->_update($fields_sql);
	}
	
	public function increment($field, $count = 1) {
		$fields_sql = $this->_field($field)."=(".$this->field($field)." + ".(int)$count.")";

        return $this->_update($fields_sql);
	}
	
	public function decrement($field, $count = 1) {
		$fields_sql = $this->_field($field)."=(".$this->field($field)." - ".(int)$count.")";

        return $this->_update($fields_sql);
	}
	
	public function toggle($field) {
		$fields_sql = $this->_field($field)."=(NOT ".$this->field($field).")";

        return $this->_update($fields_sql);
	}
	
	private function _update($fields_sql) {
		$sql = "UPDATE ".$this->_table()." SET ".$fields_sql.$this->_where();

		$this->execute($sql);

		return $this->driver->countAffected();
	}
	
}
