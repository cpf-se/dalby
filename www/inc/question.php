<?php

require_once 'inc/answer.php';
require_once 'inc/sql.php';

class question {
	private $_id;
	private $_type;
	private $_min_age;
	private $_text;
	private $_answers = null;
	private $_dependants = null;
	private $_responses = null;

	public function __construct($id) {
		$what = array('A.id', 'type', 'min_age', 'text', 'has_dependants');
		$from = _FROM(array('questions AS A',
			'questions_qtexts AS B' => _EQ('A.id', 'B.question'),
			'qtexts AS C' => _EQ('B.qtext', 'C.id')));
		$where = _EQ('A.id', $id);
		$select = _SELECT($what, $from, $where);
		$result = array();
		if (!_QUERY($select, $result)) {
			throw new Exception('Error initializing question ' . $id . ': ' . pg_last_error());
		} else {
			$this->_id      = $result[0]['id'];
			$this->_type    = $result[0]['type'];
			$this->_min_age = $result[0]['min_age'];
			$this->_text    = $result[0]['text'];

			if ($result[0]['has_dependants'] === 't') {
				$this->_dependants = array();
				$what = array('id');
				$from = 'questions';
				$where = _EQ('qref', $id);
				$order = array('questions.order');
				$select = _SELECT($what, $from, $where, $order);
				$result = array();
				if (!_QUERY($select, $result)) {
					throw new Exception('Error querying dependants to ' . $id . ': ' . pg_last_error());
				} else foreach ($result as $r) {
					$this->_dependants[] = new question($r['id']);
				}
			}

			$what = array('A.id', 'A.text');
			$from = _FROM(array('answers AS A',
				'answers_questions AS B' => _EQ('B.answer', 'A.id'),
				'questions AS C' => _EQ('C.id', 'B.question')));
			$where = _EQ('C.id', $id);
			$order = array('B.order');
			$select = _SELECT($what, $from, $where, $order);
			$result = array();
			if (!_QUERY($select, $result)) {
				//
				// Inget svar! Normalt, t ex qt0!
				//
				//=====================================================
				//
				// FIXME: Uppdatera flöde med undantag!
				//
				//=====================================================
				//
			} else {
				$this->_answers = array();
				foreach ($result as $r) {
					$this->_answers[] = new answer($r['id'], $r['text']);
				}

				$what = array('answer');
				$from = _FROM(array('responses_questions'));
				$where = _AND(array(_EQ('question', $this->id()), _EQ('response', $_SESSION['response'])));
				$order = array('stamp');
				$select = _SELECT($what, $from, $where, $order) . ' DESC';
				$result = array();
				if (!_QUERY($select, $result)) {
					//
					// Ingen tidigare respons på denna fråga, bör vara normalt.
					//
					//======================================================
					//
					// FIXME: Uppdatera flöde med undantag!
					//
					//======================================================
					//
				} else {
					$this->_responses = array();
					foreach ($result as $r) {
						$this->_responses[] = $r['answer'];
					}
				}
			}
		}
	}

	public function &id() {
		return $this->_id;
	}

	public function &type() {
		return $this->_type;
	}

	public function &subtype() {
		if ($this->_dependants == null || empty($this->_dependants)) die("INTERNAL ERROR");
		return $this->_dependants[0]->type();
	}

	public function &min_age() {
		return $this->_min_age;
	}

	public function &text() {
		return $this->_text;
	}

	public function &answers() {
		return $this->_answers;
	}

	public function &dependants() {
		return $this->_dependants;
	}

	public function &responses() {
		return $this->_responses;
	}

	public function order() {
		$what = array('A.order');
		$from = _FROM(array(
			'questionnaires_questions AS A',
			'questionnaires AS B' => _EQ('A.questionnaire', 'B.id')));
		$where = _EQ('A.question', $this->id());
		$select = _SELECT($what, $from, $where) . ' LIMIT 1';
		$result = array();
		if (!_QUERY($select, $result)) {
			throw new Exception('Cannot retrieve order attribute: ' . pg_last_error());
		}
		return $result[0]['order'];
	}

	public /* DEBUG */ function dump() {
		print $this->_id;
		print '[' . ($this->_answers != null ? count($this->_answers) : 0) . ']';
		print '( ';
		if ($this->_dependants != null) foreach ($this->_dependants as $d) {
			$d->dump();
			print ' ';
		}
		print ')';
	}
}
?>
