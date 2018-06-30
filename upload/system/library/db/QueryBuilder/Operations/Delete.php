<?php
namespace db\QueryBuilder\Operations;

trait Delete {
	
	public function delete($limit = null) {
		if(!is_null($limit)) {
			$this->limit($limit);
		}
		
		$sql = "DELETE FROM ".$this->_table().$this->_where().$this->_order().$this->_limit();

		$this->execute($sql);
		
		return $this->driver->countAffected();
	}
	
	public function clear() {
		$this->execute("TRUNCATE TABLE ".$this->_table());
		
		return $this->driver->countAffected();
	}
	
}
