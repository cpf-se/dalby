<?php

require_once 'inc/sql.inc';

class questionnaire {
	private $_id;
	private $_name;
	private $_title;
	private $_welcome;

	public function __construct($name) {
		$what   = array('id', 'name', 'title', 'welcome');
		$from   = 'questionnaires';
		$where  = _EQ('name', _Q($name));
		$result = array();
		if (!_QUERY(_SELECT($what, $from, $where), $result)) {
			throw new Exception('Error constructing questionnaire: ' . pg_last_error());
		} else {
			$this->_id      = $result[0]['id'];
			$this->_name    = $result[0]['name'];
			$this->_title   = $result[0]['title'];
			$this->_welcome = $result[0]['welcome'];
		}
	}

	public function &id()
	{
		return $this->_id;
	}

	public function &name()
	{
		return $this->_name;
	}

	public function &title()
	{
		return $this->_title;
	}

	public function &welcome()
	{
		return $this->_welcome;
	}
}
?>
