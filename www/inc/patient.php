<?php

require_once 'inc/sql.php';

class patient {
	private $_code;
	private $_birthdate;
	private $_age;

	public function __construct($code) {
		$what   = array('code', 'birthdate', 'EXTRACT(year FROM age(birthdate)) AS age');
		$from   = 'patients';
		$where  = _EQ('code', $code);
		$select = _SELECT($what, $from, $where);
		$result = array();
		if (!_QUERY($select, $result)) {
			throw new Exception('Error constructing patient: ' . pg_last_error());
		} else {
			$this->_code      = $result[0]['code'];
			$this->_birthdate = $result[0]['birthdate'];
			$this->_age       = $result[0]['age'];
		}
	}

	public function &age() {
		return $this->_age;
	}
}
?>
