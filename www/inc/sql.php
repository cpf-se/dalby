<?php

//$pg_connect_string  = 'host=localhost port=5432 dbname=dalby user=www_dalby password=www_dalby' ;
$pg_connect_string  = 'dbname=dalby user=www_dalby password=www_dalby' ;
//$pg_response_expiry = _MUL(array(60, 60, 24, 7)); // en vecka

$pg_response_expiry = _MUL(array(60, 60));		// DEBUG: Bara en timma under test!!!

function _QUERY($query, &$result) {
	//
	//
	//====================================================
	//
	// FIXME: Uppdatera felrapportering med undantag!
	//
	//====================================================
	//
	//
	$c = pg_connect($GLOBALS['pg_connect_string']) or die(pg_last_error());
	$r = pg_query($c, $query);
	$status = 0;
	while ($row = pg_fetch_array($r)) {
		$status += 1;
		array_push($result, $row);
	}
	//pg_close($c);
	return $status;
}

function _FROM($tables) {
	$t = array();
	array_push($t, array_shift($tables)); // Wow! Works even for scalar strings.
	foreach ($tables as $table => $condition) array_push($t, $table . ' ON ' . $condition);
	return implode("\nINNER JOIN ", $t);
}

function _AND($terms) {
	return implode("\nAND ", $terms);
}

function _OR($terms) {
	return implode("\nOR ", $terms);
}

function _MUL($terms) {
	return implode(" * ", $terms);
}

function _SECS($interval) {
	return sprintf("extract(epoch from %s)", $interval);
}

function _GT($a, $b) {
	return sprintf("%s > %s", $a, $b);
}

function _LT($a, $b) {
	return sprintf("%s < %s", $a, $b);
}

function _EQ($a, $b) {
	return sprintf("%s = %s", $a, $b);
}

function _Q($str) {
	return sprintf("'%s'", $str);
}

function _PAREN($str) {
	return sprintf("(%s)", $str);
}

function _SELECT($what, $from, $where, $order_by = null) {
	return ($order_by == null)
		? sprintf("SELECT %s\nFROM %s\nWHERE %s", implode(', ', $what), $from, $where)
		: sprintf("SELECT %s\nFROM %s\nWHERE %s\nORDER BY %s", implode(', ', $what), $from, $where, implode(', ', $order_by));
}

function _INSERT($into, $variables, $values) {
	$vals = array();
	foreach ($values as $value) array_push($vals, _TUPLE($value));
	return sprintf("INSERT INTO %s\n%s\nVALUES\n%s\nRETURNING *", $into, _TUPLE($variables), _VALUES($vals));
}

function _TUPLE($values) {
	return _PAREN(implode(', ', $values));
}

function _VALUES($tuples) {
	return implode(",\n", $tuples);
}

function check_logged_in() {
	session_start();

	if (isset($_SESSION['useq'], $_SESSION['year'], $_SESSION['month'], $_SESSION['day'], $_SESSION['response'])) {
		return true;
	}

	elseif (isset($_REQUEST['useq'], $_REQUEST['year'], $_REQUEST['month'], $_REQUEST['day'])) {
		$year   = $_REQUEST['year'];
		$month  = $_REQUEST['month'];
		$day    = $_REQUEST['day'];
		$useq   = $_REQUEST['useq'];
		$bdate  = sprintf('%04d-%02d-%02d', $year, $month, $day);
		$result = array();
		$select = _SELECT(array('*'), 'patients', _AND(array(_EQ('code', $useq), _EQ('birthdate', _Q($bdate)))));
		if (_QUERY($select, $result)) {
			$_SESSION['year']  = $year;
			$_SESSION['month'] = $month;
			$_SESSION['day']   = $day;
			$_SESSION['useq']  = $useq;

			while (!isset($_SESSION['response'])) {
				$what = array('responses.id', 'questionnaire', 'patient');
				$from = _FROM(array('responses', 'questionnaires' => 'questionnaire = questionnaires.id'));
				$where = _AND(array(
					_EQ('questionnaires.name', _Q('Dalby')),
					_EQ('patient', $_SESSION['useq']),
					_LT(_SECS('age(now(), stamp)'), $GLOBALS['pg_response_expiry'])));
				$select = _SELECT($what, $from, $where);
				$result = array();
				if (!_QUERY($select, $result)) {
					$into = 'responses';
					$variables = array('questionnaire', 'patient');
					$what = array('id');
					$from = 'questionnaires';
					$where = _EQ('name', _Q('Dalby'));
					$questionnaire = _SELECT($what, $from, $where);
					$values = array(array(_PAREN($questionnaire), $_SESSION['useq']));
					$insert = _INSERT($into, $variables, $values);
					_QUERY($insert, $result) or die("Couldn't create new response row!");
				} elseif (count($result) > 1) {
					// XXX: This is strange, die for now, or else live-lock
					die('FOO');
				} elseif (count($result) < 1) {
					// XXX: This is strange too
					die('BAR');
				} else {
					$_SESSION['response'] = $result[0]['id'];
				}
			}

			return true;
		} else return false;
	}
}

?>
