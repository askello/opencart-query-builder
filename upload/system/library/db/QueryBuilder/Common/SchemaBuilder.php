<?php
namespace db\QueryBuilder\Common;

trait SchemaBuilder {
	
	public function truncate() {
		$this->execute("TRUNCATE TABLE ".$this->_table());
		
		return $this;
	}
	
	public function drop() {
		$this->execute("DROP TABLE ".$this->_table()." IF EXISTS");
	}
	
}
