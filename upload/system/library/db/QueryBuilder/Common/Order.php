<?php
namespace db\QueryBuilder\Common;

trait Order {
	
	private $sortFields = array();
	
	public function sortBy($field, $order = "ASC") {
		if(is_array($field)) {
			foreach($field as $field_name => $order) {
				$this->sortBy($field_name, $order);
			}			
		} else {
			$this->sortFields[$field] = $order;
		}
		
		return $this;
	}
	
	private function sortOrder($order) {
		$order = strtoupper($order);
		
		if($order != "DESC") {
			$order = "ASC";
		}
		
		return $order;
	}
	
	private function _order() {
		if($this->sortFields) {
			$fields = array();
			
			foreach($this->sortFields as $field => $order) {
				$fields[] = $this->_field($field)." ".$this->sortOrder($order);
			}
			
			return PHP_EOL."ORDER BY ".implode(", ", $fields);
		}
		
		return "";
	}
	
}
