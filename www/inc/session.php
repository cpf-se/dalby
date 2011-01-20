<?php

require_once 'inc/sql.php';
require_once 'inc/html.php';
require_once 'inc/question.php';
require_once 'inc/patient.php';
require_once 'inc/response.php';

function _collect() {
	$response = $_SESSION['response'];

	$values = array();

	$keys = array_keys($_REQUEST);
	foreach ($keys as $key) {
		$qid = array();
		if (!preg_match('/^q(\d+)$/', $key, $qid)) continue;

		$aid = array();
		if (!preg_match('/^a(\d+)$/', $_REQUEST[$key], $aid)) continue;

		$values[] = array($response, $qid[1], $aid[1]);
	}

	if (!empty($values)) {
		$into = 'responses_questions';
		$variables = array('response', 'question', 'answer');
		$insert = _INSERT($into, $variables, $values);
		if (!_QUERY($insert, $result)) {
			throw new Exception('Couldn\'t insert response components: ' . pg_last_error());
		}
	}
}

function _collect_spec4() {
	$response = $_SESSION['response'];

	$answers = explode(' ', $_REQUEST['answers']);

	$value = 0;
	$values = array();

	$keys = array_keys($_REQUEST);
	foreach ($keys as $key) {
		$qid = array();
		if (!preg_match('/^q(\d+)_(\d+)$/', $key, $qid)) continue;

		// extra check
		$aid = array();
		if (!preg_match('/^a(\d+)$/', $_REQUEST[$key], $aid)) continue;
		if ($qid[2] != $aid[1]) {
			throw new Exception('Qid = ' . $qid[2] . ', Aid = ' . $aid[1]);
		}

		$akey = array_search($qid[2], $answers);
		$value |= (1 << $akey);
		$values[0] = array($response, $qid[1], $value);
	}

	if ($value != 0) {
		$into = 'responses_questions';
		$variables = array('response', 'question', 'answer');
		$insert = _INSERT($into, $variables, $values);
		if (!_QUERY($insert, $result)) {
			throw new Exception("Couldn't insert response component, type 4,\n" . $insert . "\n" . pg_last_error());
		}
	}
}

function go() {
	if (isset($_REQUEST['question'])) {
		$q = new question($_REQUEST['question']);
		$order = $q->order();

		if ($q->type() == 4) {
			_collect_spec4();
		} else {
			_collect();
		}

		if (isset($_REQUEST['next_x'], $_REQUEST['next_y'])) { // going forward
			$what = array('A.question');
			$from = _FROM(array(
				'questionnaires_questions AS A',
				'questionnaires AS B' => _EQ('A.questionnaire', 'B.id')));
			$where =_AND(array(
				_EQ('B.name', _Q('Dalby')),
				_GT('A.order', $order)));
			$order = array('A.order');
			$select = _SELECT($what, $from, $where, $order) . ' LIMIT 1';
			$result = array();
			if (!_QUERY($select, $result)) {
				throw new Exception('Cannot retrieve next question id: ' . pg_last_error());
			} else {
				$q = new question($result[0]['question']);
			}
		} elseif (isset($_REQUEST['prev_x'], $_REQUEST['prev_y'])) { // going backward
			$what = array('A.question');
			$from = _FROM(array(
				'questionnaires_questions AS A',
				'questionnaires AS B' => _EQ('A.questionnaire', 'B.id')));
			$where =_AND(array(
				_EQ('B.name', _Q('Dalby')),
				_LT('A.order', $order)));
			$order = array('A.order');
			$select = _SELECT($what, $from, $where, $order) . ' DESC LIMIT 1';
			$result = array();
			if (!_QUERY($select, $result)) {
				throw new Exception('Cannot retrieve previous question id: ' . pg_last_error());
			} else {
				$q = new question($result[0]['question']);
			}
		} else {
			throw new Exception('LOGIC ERROR: Direction unknown');
		}
		print_question($q);
	} else {
		// Ok, vi ska presentera första frågan!
		$what = array('A.question');
		$from = _FROM(array(
			'questionnaires_questions AS A',
			'questionnaires AS B' => _EQ('A.questionnaire', 'B.id')));
		$where = _EQ('B.name', _Q('Dalby'));
		$order = array('A.order'/*, 'A.label'*/);		// XXX: WTF
		$select = _SELECT($what, $from, $where, $order) . ' LIMIT 1';
		$result = array();
		if (!_QUERY($select, $result)) {
			throw new Exception('Cannot retrive first question id: ' . pg_last_error());
		} else {
			$q = new question($result[0]['question']);
			print_question($q);
		}
	}
}
?>
