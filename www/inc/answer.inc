<?php

require_once 'inc/sql.inc';

class answer {
	function __construct($id, $text) {
		$this->_id   = $id;
		$this->_text = $text;
	}

	function &id() { return $this->_id; }

	function &text() { return $this->_text; }
}
?>
