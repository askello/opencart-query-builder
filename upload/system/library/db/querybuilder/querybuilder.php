<?php
namespace db\QueryBuilder;

class QueryBuilder {
	
	private $db;
	
	private $log = array();
	private $logEnabled = false;
	
	public function __construct($db) {
		$this->db = $db;
	}
	
	public function table($table) {
    return new Query($this, $table);
}

    public function enableLog() {
        $this->logEnabled = true;
    }

    public function getExecutedQueries() {
        return $this->log;
    }

    public function printExecutedQueries() {
        foreach($this->log as $sql) {
            echo '<pre>'.$sql.'</pre>';
        }
    }
	
	
	/* Overwriten functions */
	
	public function query($sql) {
	    if($this->logEnabled) {
	        $this->log[] = $sql;
        }

		return $this->db->query($sql);
	}
	
	public function escape($value) {
		return $this->db->escape($value);
	}
	
	public function countAffected() {
		return $this->db->countAffected();
	}

	public function insertId() {
	    return $this->db->getLastId();
    }
	
	public function getLastId() {
		return $this->db->getLastId();
	}
	
	public function connected() {
		return $this->db->connected();
	}
	
}