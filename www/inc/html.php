<?php

$_indent = 0;
$_tagstack = array();

$_blocktags = array(1 => 'address', 'blockquote', 'body', 'colgroup', 'div', 
	'dl', 'fieldset', 'head', 'html', 'form', 'ol', 'optgroup', 'pre', 
	'select', 'style', 'table', 'tbody', 'tfoot', 'thead', 'tr', 'ul' );

$_inlinetags = array(1 => 'a', 'abbr', 'acronym', 'b', 'big', 'caption', 
	'cite', 'code', 'del', 'dd', 'dfn', 'dl', 'dt', 'em', 'h1', 'h2', 'h3', 
	'h4', 'h5', 'h6', 'i', 'ins', 'kbd', 'label', 'legend', 'li', 'option', 
	'p', 'q', 'samp', 'script', 'small', 'span', 'strong', 'sub', 'sup', 
	'td', 'th', 'title', 'tt', 'u' );

$_emptytags = array(1 => 'base', 'br', 'hr', 'img', 'link', 'meta' );

$_inlineemptytags = array(1 => 'input');

function _nlpr($text = '') {
	print("\n");
	$i = $GLOBALS['_indent'];
	while ($i--) print("\t");
	print($text);
}

function _block_push($tag, &$attributes /*= array()*/) {
	_nlpr("<" . $tag);
	array_push($GLOBALS['_tagstack'], $tag);
	foreach ($attributes as $attrib => $value) {
		print(" $attrib=\"" . $value . "\"");
	}
	print(">");
	$GLOBALS['_indent'] += 1;
}

function _inline_push($tag, &$attributes /*= array()*/) {
	_nlpr("<" . $tag);
	array_push($GLOBALS['_tagstack'], $tag);
	foreach ($attributes as $attrib => $value) {
		print(" $attrib=\"" . $value . "\"");
	}
	print(">");
}

function _empty_push($tag, &$attributes /*= array()*/) {
	_nlpr("<" . $tag);
	foreach ($attributes as $attrib => $value) {
		print(" $attrib=\"" . $value . "\"");
	}
	print(" />");
}

function _inline_empty_push($tag, &$attributes /*= array()*/) {
	print("<" . $tag);
	foreach ($attributes as $attrib => $value) {
		print(" $attrib=\"" . $value . "\"");
	}
	print(" />");
}

function tag_push($tag, $attributes = array()) {
	if     (array_search($tag, $GLOBALS['_blocktags'])       != FALSE) _block_push($tag, $attributes);
	elseif (array_search($tag, $GLOBALS['_inlinetags'])      != FALSE) _inline_push($tag, $attributes);
	elseif (array_search($tag, $GLOBALS['_emptytags'])       != FALSE) _empty_push($tag, $attributes);
	elseif (array_search($tag, $GLOBALS['_inlineemptytags']) != FALSE) _inline_empty_push($tag, $attributes);
	else {
		die("TAG $tag not found!!!");
	}
}

function tag_pop($check = '') {
	$tag = array_pop($GLOBALS['_tagstack']);
	if (!empty($check) && $check != $tag) {
		die("Tag stack error, popped $check was $tag");
	}
	if       (array_search($tag, $GLOBALS['_blocktags'])) {
		$GLOBALS['_indent'] -= 1;
		_nlpr("</" . $tag . ">");
	} elseif (array_search($tag, $GLOBALS['_inlinetags'])) {
		print("</" . $tag . ">");
	} elseif (array_search($tag, $GLOBALS['_emptytags'])) {
		die("Shouldn't reach here");
	} elseif (array_search($tag, $GLOBALS['_inlineemptytags'])) {
		die("Shouldn't reach here");
	} else {
		die("TAG $tag not found on stack!!!");
	}
}

function _prolog() {
	print("<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n");
	print("<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" ");
	print("\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">");
	tag_push('html', array( 'xmlns' => 'http://www.w3.org/1999/xhtml', 'xml:lang' => 'sv', 'lang' => 'sv', 'dir' => 'ltr' ));
}

function _head($cssfile) {
	tag_push('head');
	tag_push('meta', array('http-equiv' => 'Content-Type', 'content' => 'text/html; charset=utf-8'));
	tag_push('meta', array('name' => 'generator', 'content' => 'vi'));
	tag_push('meta', array('name' => 'author', 'content' => 'Christian Andersson &lt;christian@cpf.se&gt;'));
	tag_push('base', array('href' => '/'));
	tag_push('title'); print('Dalby'); tag_pop('title');
	tag_push('link', array('type' => 'text/css', 'rel' => 'stylesheet', 'media' => 'all', 'href' => $cssfile));
	//tag_push('script', array('type' => 'text/javascript', 'src' => 'jquery-1.4.2.min.js')); tag_pop();
	tag_push('script', array('type' => 'text/javascript', 'src' => 'dalby.js')); tag_pop();
	tag_pop('head');
}

function _foot() {
	while (!empty($GLOBALS['_tagstack'])) {
		tag_pop();
	}
	print("\n");
}

function html_test() {
	_prolog();
	_head('dalby.css');
	tag_push('body');
	tag_push('h1'); print("Dalbytitel"); tag_pop('h1');
	tag_push('pre');
	print('$_SESSION[] = '); var_dump($_SESSION);
	tag_pop('pre');
	_foot();
}

function _span($class, $content) {
	tag_push('span', array('class' => $class));
	print($content);
	tag_pop('span');
}

function _html_select($label, $name, $id, $values, $extra) {
	tag_push('label', array('for' => $id)); print($label); tag_pop('label');
	$selattrs = array('id' => $id, 'name' => $name);
	if (isset($extra['onchange'])) $selattrs['onchange'] = $extra['onchange'];
	tag_push('select', $selattrs); {
		tag_push('option', array('value' => '')); print(''); tag_pop('option');
		foreach ($values as $key => $value) {
			$optattrs = array('name' => $key, 'value' => $key);
			if (isset($extra['selected']) && $extra['selected'] == $key) {
				$optattrs['selected'] = 'selected';
			}
			tag_push('option', $optattrs);
			print($value);
			tag_pop('option');
		}
		tag_pop('select');
	}
}

function _birthdate() {
	function _year() {
		$years = array(); for ($i = 1992; $i >= 1910; --$i) $years[$i] = $i;
		$extra = array('onchange' => 'javascript:yearChanged()');
		if (isset($_REQUEST['year']) && !empty($_REQUEST['year'])) {
			$extra['selected'] = $_REQUEST['year'];
		}
		_html_select('År', 'year', 'year', $years, $extra);
	}
	function _month() {
		$months = array(1 => 'januari', 'februari', 'mars', 'april', 'maj', 'juni',
			'juli', 'augusti', 'september', 'oktober', 'november', 'december');
		$extra = array('onchange' => 'javascript:monthChanged()');
		if (isset($_REQUEST['year']) && !empty($_REQUEST['month'])) {
			$extra['selected'] = $_REQUEST['month'];
		}
		_html_select('Månad', 'month', 'month', $months, $extra);
	}
	function _day() {
		$extra = array('onchange' => 'javascript:dayChanged()');
		_html_select('Dag', 'day', 'day', array(), $extra);
	}
	tag_push('fieldset', array('id' => 'birthdate')); {
		tag_push('legend'); print('Födelsedata'); tag_pop('legend');
		_year();
		_month();
		_day();
		tag_pop('fieldset');
	}
}

function _input($label, $name, $id, $type, $extra, $value = '') {
	tag_push('label', array('for' => $id)); print($label); tag_pop('label');
	$inpattrs = array('id' => $id, 'name' => $name, 'type' => $type);
	if (isset($extra['disabled'])) $inpattrs['disabled'] = $extra['disabled'];
	if (!empty($value)) $inpattrs['value'] = $value;
	tag_push('input', $inpattrs);
}

function _sequence() {
	function _useq() {
		$extra = array('disabled' => 'true');
		_input('Kod', 'useq', 'useq', 'password', $extra);
	}
	tag_push('fieldset', array('id' => 'identity')); {
		tag_push('legend'); print('Identifiering'); tag_pop('legend');
		_useq();
		tag_pop('fieldset');
	}
}

function login_form($title, $welcome) {
	_prolog();
	_head('dalby.css');
	tag_push('body', array('id' => 'login', 'onLoad' => 'javascript:initLogin()')); {
		tag_push('div', array('id' => 'welcome')); {
			tag_push('h1'); print($title); tag_pop('h1');
			tag_push('div', array('id' => 'welcome_text')); {
				print($welcome); // FIXME
				tag_pop('div');
			}
			tag_pop('div');
		}
		tag_push('div', array('id' => 'loginform')); {
			tag_push('form', array('action' => '/', 'method' => 'post')); {
				_birthdate();
				_sequence();
				tag_push('p'); {
					tag_push('input', array('type' => 'submit', 'value' => 'Bekräfta'));
					tag_pop('p');
				}
				tag_pop('form');
			}
			tag_pop('div');
		}
		tag_pop('body');
	}
	_foot();
}

require_once 'inc/question.php';
require_once 'inc/answer.php';

function _pr_qt0(&$q) {
	tag_push('p'); {
		print($q->text());
		tag_pop('p');
	}
	if ($q->subtype() == 1) {
		tag_push('ol'); {
			foreach ($q->dependants() as $d) {
				tag_push('li'); {
					_pr_qt1($d);
					tag_pop('li');
				}
			}
			tag_pop('ol');
		}
	} elseif ($q->subtype() == 2) {
		tag_push('ol'); {
			foreach ($q->dependants() as $d) {
				tag_push('li'); {
					_pr_qt2($d);
					tag_pop('li');
				}
			}
			tag_pop('ol');
		}
	} elseif ($q->subtype() == 3) {
		$lo = $hi = 1;
		tag_push('div', array('class' => 'mnemonics')); {
			tag_push('ol'); {
				foreach ($q->answers() as $a) {
					tag_push('li'); {
						print($a->text());
						$hi += 1;
						tag_pop('li');
					}
				}
				tag_pop('ol');
			}
			tag_pop('div');
		}
		tag_push('ul'); {
			foreach ($q->dependants() as $d) {
				tag_push('li'); {
					_pr_qt3($d, $lo, $hi);
					tag_pop('li');
				}
			}
			tag_pop('ul');
		}
	} elseif ($q->subtype() == 4) {
		tag_push('ol'); {
			foreach ($q->dependants() as $d) {
				tag_push('li'); {
					_pr_qt4($d);
					tag_pop('li');
				}
			}
			tag_pop('ol');
		}
	} else {
		print("\n<!-- q-subtype is " . $q->subtype() . " -->");
	}
}

function _pr_qt1(&$q) { // Multi-choice, ett svar
	tag_push('fieldset', array('class' => 'qt' . $q->type())); {
		tag_push('legend'); {
			print($q->text());
			tag_pop('legend');
		}
		$resps = $q->responses();
		//print("\n<!--\n"); var_dump($resps); print("-->");
		foreach ($q->answers() as $a) {
			tag_push('label'); {
				$attrs = array('type' => 'radio', 'name' => 'q' . $q->id(), 'value' => 'a' . $a->id());
				if ($resps != null && $a->id() == $resps[0]) {
					$attrs['checked'] = 'checked';
				}
				tag_push('input', $attrs);
				print($a->text());
				tag_pop('label');
			}
		}
		tag_pop('fieldset');
	}
}

function _pr_qt2(&$q) { // Multi-choice, ett svar, horisontellt
	_pr_qt1($q);
	//tag_push('fieldset', array('class' => 'qt' . $q->type())); {
	//	tag_push('legend'); {
	//		print($q->text());
	//		tag_pop('legend');
	//	}
	//	foreach ($q->answers() as $a) {
	//		tag_push('label'); {
	//			tag_push('input', array('type' => 'radio', 'name' => 'q' . $q->id(), 'value' => 'a' . $a->id()));
	//			print($a->text());
	//			tag_pop('label');
	//		}
	//	}
	//	tag_pop('fieldset');
	//}
}

function _pr_qt3(&$q, &$lo, &$hi) { // Multi-choice, ett svar, horisontellt, långa svar
	$i = $lo;
	tag_push('fieldset', array('class' => 'qt' . $q->type())); {
		tag_push('legend'); {
			print($q->text());
			tag_pop('legend');
		}
		$resps = $q->responses();
		foreach ($q->answers() as $a) {
			tag_push('label'); {
				$attrs = array('type' => 'radio', 'name' => 'q' . $q->id(), 'value' => 'a' . $a->id());
				if ($resps != null && $a->id() == $resps[0]) {
					$attrs['checked'] = 'checked';
				}
				tag_push('input', $attrs);
				print($i++);
				tag_pop('label');
			}
		}
		tag_pop('fieldset');
	}
}

function _pr_qt4(&$q) { // Multi-choice, flera svar
	tag_push('p'); {
		print($q->text());
		tag_pop('p');
	}
	tag_push('fieldset'); {
		$answers = array();
		foreach ($q->answers() as $a) {
			$answers[] = $a->id();
		}
		tag_push('input', array('type' => 'hidden', 'name' => 'answers', 'value' => implode($answers, ' ')));
		$resps = $q->responses();
		foreach ($q->answers() as $a) {
			tag_push('label'); {
				$attrs = array('type' => 'checkbox', 'name' => 'q' . $q->id() . '_' . $a->id(), 'value' => 'a' . $a->id());
				$akey = array_search($a->id(), $answers);
				if ($resps != null && ((1 << $akey) & $resps[0]) != 0) {
					$attrs['checked'] = 'checked';
				}
				tag_push('input', $attrs);
				print($a->text());
				tag_pop('label');
			}
		}
		tag_pop('fieldset');
	}
}

function print_question($q) {
	_prolog();
	_head('dalby.css');
	tag_push('body', array('id' => 'question')); {
		tag_push('div', array('class' => 'qt' . $q->type())); {
			tag_push('form', array('action' => '/', 'method' => 'get')); {
				tag_push('span', array('id' => 'prev')); {
					tag_push('input', array('type' => 'image', 'src' => 'im/prev.png', 'name' => 'prev'));
					tag_pop('span');
				}
				tag_push('div', array('id' => 'main')); {
					tag_push('input', array('type' => 'hidden', 'name' => 'question', 'value' => $q->id()));
					switch ($q->type()) {
					case 0: _pr_qt0($q); break;
					case 1: _pr_qt1($q); break;
					case 2: _pr_qt2($q); break;
					case 3: _pr_qt3($q); break;
					case 4: _pr_qt4($q); break;
					}
					tag_pop('div');
				}
				tag_push('span', array('id' => 'next')); {
					tag_push('input', array('type' => 'image', 'src' => 'im/next.png', 'name' => 'next'));
					tag_pop('span');
				}
				tag_pop('form');
			}
			tag_pop('div');
		}
		tag_pop('body');
	}
	_foot();
}

function print_error($e) {
	_prolog();
	_head('dalby.css');
	tag_push('body', array('id' => 'error')); {
		tag_push('pre'); {
			print($e->getMessage());
			var_dump($_REQUEST);
			tag_pop('pre');
		}
		tag_pop('body');
	}
	_foot();
}
?>
