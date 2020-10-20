<?php
namespace db\QueryBuilder;
class QueryBuilder {
	private $db;
	private $log = array();
	private $logEnabled = false;
	private $sql_replace = array();

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

	public function sql_replace($find = array(), $replace = array()) {
		if ($find) {
			return $this->sql_replace[] = array('find' => $find, 'replace' => $replace);
		}
	}

	/* Overwriten functions */

	public function query($sql) {
		foreach ($this->sql_replace as $res) {
			$sql = str_replace($res['find'], $res['replace'], $sql);
		}
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
