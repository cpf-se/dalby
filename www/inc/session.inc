<?php

require_once 'inc/sql.inc';
require_once 'inc/html.inc';
require_once 'inc/question.inc';
require_once 'inc/patient.inc';
require_once 'inc/response.inc';

function go() {
	if (isset($_REQUEST['question'])) {
		$q = new question($_REQUEST['question']);
		$order = $q->order();

		//----------------------------------------------
		// XXX: Ta hand om inkommande svar
		//----------------------------------------------

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
