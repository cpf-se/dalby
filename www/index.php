<?php
error_reporting(E_ALL);

require_once 'inc/login.php' ;
require_once 'inc/session.php' ;
require_once 'inc/html.php' ;

try {
	if (!logged_in()) login();
	else go();
} catch (Exception $e) {
	print_error($e);
}
?>
