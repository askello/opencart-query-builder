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
			if($this->isRawSql($field)) {
				$this->sortFields[] = $field;
			} else {
				$this->sortFields[$field] = $order;
			}
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

	private function sortViceVersa() {
		foreach(array_keys($this->sortFields) as $sortField) {
			if(!is_int($sortField)) {
				$sortOrder = $this->sortOrder($this->sortFields[$sortField]) == "ASC" ? "DESC" : "ASC";
				$this->sortBy($sortField, $sortOrder);
			}
		}
	}

	private function _order() {
		if($this->sortFields) {
			$fields = array();

			foreach($this->sortFields as $field => $order) {
				if(is_int($field)) { // if raw sql
					$fields[] = $order;
				} else {
					$fields[] = $this->_field($field)." ".$this->sortOrder($order);
				}
			}

			return PHP_EOL."ORDER BY ".implode(", ", $fields);
		}

		return "";
	}

}
