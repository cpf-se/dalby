<?php

function is_leap_year($year) {
	return date("L", strtotime("$year-01-01"));
}

function array_bsearch($needle, &$haystack) {
	$count = $high = count($haystack);
	$low  = 0;

	while ($high - $low > 1) {
		$probe = ($high + $low) / 2;
		if ($haystack[$probe] < $needle) {
			$low = $probe;
		} else {
			$high = $probe;
		}
	}

	return $high == $count || $haystack[$high] != $needle ? -1 : $high;
}
//
// ==== TEST ====
//
//	$h = array(1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 13, 15, 20);
//
//	for ($n = 1; $n < 21; $n += 1) {
//		printf("array_bsearch($n, H) == %d\n", array_bsearch($n, $h));
//	}
//
//	$h = array('a', 'abbr', 'acronym', 'b', 'big', 'caption', 'cite', 
//		'code', 'del', 'dd', 'dfn', 'dl', 'dt', 'em', 'h1', 'h2', 'h3', 
//		'h4', 'h5', 'h6', 'i', 'ins', 'kbd', 'label', 'legend', 'li', 
//		'option', 'p', 'q', 'samp', 'script', 'small', 'span', 
//		'strong', 'sub', 'sup', 'td', 'th', 'title', 'tt', 'u');
//
//	foreach (array('a', 'abbr', 'span', 'body', 'select') as $n) {
//		printf("array_bsearch($n, H) == %d\n", array_bsearch($n, $h));
//	}
?>
