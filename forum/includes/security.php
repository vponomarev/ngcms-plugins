<?php
/*
=====================================================
 NG FORUM v.alfa
-----------------------------------------------------
 Author: Nail' R. Davydov (ROZARD)
-----------------------------------------------------
 Jabber: ROZARD@ya.ru
 E-mail: ROZARD@list.ru
-----------------------------------------------------
 © Настоящий программист никогда не ставит 
 комментариев. То, что писалось с трудом, должно 
 пониматься с трудом. :))
-----------------------------------------------------
 Данный код защищен авторскими правами
=====================================================
*/
if (!defined('NGCMS')) die ('HAL');
##############################
## ПРОВЕРКА ВВОДИМЫХ ДАННЫХ ##
##############################
function secure_search_forum($text) {

	$text = convert(trim($text));
	$text = preg_replace("/[^\w\x7F-\xFF\s]/", "", $text);

	return secure_html($text);
}

function safe_text_forum($text) {

	$text = convert(trim($text));
	if (preg_match("/[^\w\x7F-\xFF]/", $text)) {
		return true;
	} else {
		return false;
	}
}

function descript($text, $striptags = true) {

	$search = array(
		"40", "41", "58", "65", "66", "67", "68", "69", "70",
		"71", "72", "73", "74", "75", "76", "77", "78", "79", "80", "81",
		"82", "83", "84", "85", "86", "87", "88", "89", "90", "97", "98",
		"99", "100", "101", "102", "103", "104", "105", "106", "107",
		"108", "109", "110", "111", "112", "113", "114", "115", "116",
		"117", "118", "119", "120", "121", "122"
	);
	$replace = array(
		"(", ")", ":", "a", "b", "c", "d", "e", "f", "g", "h",
		"i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
		"v", "w", "x", "y", "z", "a", "b", "c", "d", "e", "f", "g", "h",
		"i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u",
		"v", "w", "x", "y", "z"
	);
	$entities = count($search);
	$i = 0;
	for (; $i < $entities; $i++)
		$text = preg_replace("#(&\#)(0*" . $search[$i] . "+);*#si", $replace[$i], $text);
	$text = preg_replace('#(&\#x)([0-9A-F]+);*#si', "", $text);
	$text = preg_replace('#(<[^>]+[/\"\'\s])(onmouseover|onmousedown|onmouseup|onmouseout|onmousemove|onclick|ondblclick|onfocus|onload|xmlns)[^>]*>#iU', ">", $text);
	$text = preg_replace('#([a-z]*)=([\`\'\"]*)script:#iU', '$1=$2nojscript...', $text);
	$text = preg_replace('#([a-z]*)=([\`\'\"]*)javascript:#iU', '$1=$2nojavascript...', $text);
	$text = preg_replace('#([a-z]*)=([\'\"]*)vbscript:#iU', '$1=$2novbscript...', $text);
	$text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU', "$1>", $text);
	$text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU', "$1>", $text);
	if ($striptags) {
		do {
			$thistext = $text;
			$text = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $text);
		} while ($thistext != $text);
	}

	return $text;
}

function secureinput($text) {

	if (!is_array($text)) {
		$text = trim($text);
		$search = array("&", "\"", "'", "\\", '\"', "\'", "<", ">");
		$replace = array("&amp;", "&quot;", "&#39;", "&#92;", "&quot;", "&#39;", "&lt;", "&gt;");
		$text = preg_replace("/(&amp;)+(?=\#([0-9]{2,3});)/i", "&", str_replace($search, $replace, $text));
	} else {
		foreach ($text as $key => $value) $text[$key] = secureinput($value);
	}

	return $text;
}

function securemysql($sql) {

	$sql = db_squote($sql);

	return $sql;
}

function securenum($value) {

	$value = intval($value);

	return $value;
}