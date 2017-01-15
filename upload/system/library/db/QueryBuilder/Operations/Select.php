<?php
namespace db\QueryBuilder\Operations;

trait Select {
	
	use Join;
	use Aggregates;
	
	public function get($fields = null) {
		$fields_sql = $this->prepareFieldsToSelect($fields);
		
		$sql = "SELECT".PHP_EOL."   ".$fields_sql.PHP_EOL."FROM ".$this->_tableAsAlias().$this->_joins().$this->_where().$this->_order().$this->_limit();
		
		$result = $this->execute($sql);
		
		// return value
		if(is_string($fields)) {
			return $this->getFieldValue($fields, $result);
		}
		
		// return rows
		if($this->single()) {
			return $result->row;
		} else {
			return $result->rows;
		}
	}
	
	public function has($id) {
		return (boolean)$this->find($id)->count();
	}
	
	private function getFieldValue($field, $result) {
		if($this->single()) {
			if(isset($result->row[$field])) {
				return $result->row[$field];
			} else {
				return null;
			}
		} else {
			$values = array();
			
			foreach($result->rows as $row) {
				$values[] = $row[$field];
			}
			
			return $values;
		}
	}
	
	private function single() {
		return $this->limitCount == 1;
	}
	
	private function prepareFieldsToSelect($fields) {
		$fields_sql = "*";
		
		if(is_array($fields)) {
			$tmp = array();
			
			foreach($fields as $field => $alias) {
				if(is_int($field)) {
					$tmp[] = $this->_field($alias);
				} else {
					$tmp[] = $this->_field($field)." AS `".$alias."`";
				}
			}
			
			$fields_sql = implode(",".PHP_EOL."   ", $tmp);
		} else if(is_string($fields)) {
			$fields_sql = $this->_field($fields);
		}
		
		return $fields_sql;
	}
	
}
