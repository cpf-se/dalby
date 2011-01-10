<?php

require_once 'inc/sql.inc';

class question
{
	private $type;
	private $label;
	private $text;
	private $answers;

	private function __construct($name, $id)
	{
		$what  = array('has_dependants', 'type', 'label', 'text');
		$from  = _FROM(array(
			'questionnaires qr',
			'questionnaires_questions qq' => 'qr.id = qq.questionnaire',
			'questions q' => 'qq.question = q.id',
			'questions_qtexts qqt' => 'q.id = qqt.question',
			'qtexts qt' => 'qqt.qtext = qt.id'));
		$where = _AND(array(_EQ('name', _Q($name)), _EQ('q.id', $id)));
		$sel   = _SELECT($what, $from, $where);
		print($sel . "\n\n");
		$res   = array();
		QUERY($sel, $res) or die(pg_last_error());
		$this->type  = $res[0]['type'];
		$this->label = $res[0]['label'];
		$this->text  = $res[0]['text'];
	}

	public static function first($name)
	{
		$what  = array('min(question)');
		$from  = _FROM(array(
			'questionnaires qr',
			'questionnaires_questions qq' => 'qr.id = qq.questionnaire'));
		$where = _EQ('name', _Q($name));
		$sel   = _SELECT($what, $from, $where);
		print($sel . "\n\n");
		$res   = array();
		QUERY($sel, $res) or die(pg_last_error());
		$quest = $res[0]['min'];
		return new question($name, $quest);
	}

	public static function next($id)
	{
	}

	public static function prev($id)
	{
	}

	public function show() {
		printf("%02d. %s\n", $this->label, $this->text);
	}
}
?>
