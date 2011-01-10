<?php

require_once 'inc/sql.php';
require_once 'inc/html.php';
require_once 'inc/questionnaire.php';

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
