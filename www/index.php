<?php
error_reporting(E_ALL);

require_once 'inc/login.inc' ;
require_once 'inc/session.inc' ;
require_once 'inc/html.inc' ;

try {
	if (!logged_in()) login();
	else go();
} catch (Exception $e) {
	print_error($e);
}
?>
