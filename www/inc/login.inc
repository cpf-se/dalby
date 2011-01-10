<?php

require_once 'inc/sql.inc';
require_once 'inc/html.inc';
require_once 'inc/questionnaire.inc';

function logged_in() {
	return check_logged_in();
}

function login() {
	try {
		$q = new questionnaire('Dalby');
		login_form($q->title(), $q->welcome());
	} catch (Exception $e) {
		die ("FATAL: " . $e->getMessage());
	}
}

?>
