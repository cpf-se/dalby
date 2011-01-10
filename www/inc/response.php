<?php

require_once 'inc/sql.php';
require_once 'inc/patient.php';

class response {
	private $_id;
	private $_patient;

	public function __construct($id) {
		$what   = array('id', 'patient');
		$from   = 'responses';
		$where  = _EQ('id', $id);
		$select = _SELECT($what, $from, $where);
		$result = array();
		if (!_QUERY($select, $result)) {
			throw new Exception('Error constructing response: ' . pg_last_error());
		} else {
			$this->_id      = $result[0]['id'];
			$this->_patient = new patient($result[0]['patient']);
		}
	}

	public function &id() {
		return $this->_id;
	}

	public function &patient() {
		return $this->_patient;
	}
}
?>
