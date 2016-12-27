<?php
namespace db\QueryBuilder\Common;

trait Limit {
	
	private $limitOffset;
	private $limitCount;
	
	public function limit($count) {
		$this->limitCount = intval($count);
		
		return $this;
	}
	
	public function skip($count) {
		$this->limitOffset = intval($count);
		
		return $this;
	}
	
	public function page($page) {
		$page = intval($page);
		$page = $page > 0 ? $page : 1;
		
		$this->limitOffset = ($page - 1) * $this->limitCount;
		
		return $this;
	}
	
	public function _limit() {
		if($this->limitCount && $this->limitOffset) {
			return PHP_EOL."LIMIT ".$this->limitOffset.",".$this->limitCount;
		}
		
		if($this->limitCount && !$this->limitOffset) {
			return PHP_EOL."LIMIT ".$this->limitCount;
		}
		
		return "";
	}
	
}
