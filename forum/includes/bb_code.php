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
function bb_codes($text) {

	$tpath = locatePluginTemplates(array(':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
	$text = split_text($text, 200);
	$bb_open[] = '/\[b\](.*?)\[\/b\]/is';
	$bb_close[] = '<b>\\1</b>';
	$bb_open[] = '/\[i\](.*?)\[\/i\]/is';
	$bb_close[] = '<i>\\1</i>';
	$bb_open[] = '/\[u\](.*?)\[\/u\]/is';
	$bb_close[] = '<u>\\1</u>';
	$bb_open[] = '/\[s\](.*?)\[\/s\]/is';
	$bb_close[] = '<s>\\1</s>';
	$bb_open_f[] = '#\[php\](.+?)\[/php\]#is';
	$bb_close_f[] = array('forum_code_tag', 'php', '1');
	$bb_open_f[] = '#\[html\](.+?)\[/html\]#is';
	$bb_close_f[] = array('forum_code_tag', 'html', '1');;
	$bb_open_f[] = '#\[code=([^\]]+?)\](.+?)\[/code=php\]#is';
	$bb_close_f[] = array('forum_code_tag', '1', '2');
	$bb_open_f[] = '#\[code=([^\]]+?)\](.+?)\[/code\]#is';
	$bb_close_f[] = array('forum_code_tag', '1', '2');
	$bb_open_f[] = '#\[quote\]#i';
	$bb_close_f[] = array('forum_open_quote_tag');
	$bb_open_f[] = '#\[quote=([^\]]+?)\]#is';
	$bb_close_f[] = array('forum_quote_tag', '1');
	$bb_open_f[] = '#\[/quote\]#i';
	$bb_close_f[] = array('forum_close_quote_tag');
	$bb_open_f[] = '#\[img\](.+?)\[/img\]#i';
	$bb_close_f[] = array('forum_img_tag', '1');
	$bb_open_f[] = '#\[img title=([^\]]+?)\\](.+?)\[/img\]#i';
	$bb_close_f[] = array('forum_img_tag', '2', '1');
	$bb_open_f[] = '#\[img\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/img\]#i';
	$bb_close_f[] = array('forum_img_tag', '2', '1');
	$bb_open_f[] = '#\[img\s*=\s*(\S+?)\s*\](.*?)\[\/img\]#i';
	$bb_close_f[] = array('forum_img_tag', '2', '1');
	$bb_open_f[] = '#\[url\](\S+?)\[/url\]#i';
	$bb_close_f[] = array('forum_url_tag', '1', '1');
	$bb_open_f[] = '#\[url\s*=\s*\&quot\;\s*(\S+?)\s*\&quot\;\s*\](.*?)\[\/url\]#i';
	$bb_close_f[] = array('forum_url_tag', '1', '2');
	$bb_open_f[] = '#\[url\s*=\s*(\S+?)\s*\](.*?)\[\/url\]#i';
	$bb_close_f[] = array('forum_url_tag', '1', '2');
	$bb_open_f[] = '#\[size=([0-9]+)\](.+?)\[/size\]#is';
	$bb_close_f[] = array('forum_size_tag', '1', '2');
	$bb_open_f[] = '#\[color=([\#0-9a-zA-Z]+)\](.+?)\[/color\]#is';
	$bb_close_f[] = array('forum_color_tag', '1', '2');
	$bb_open_f[] = '#\[font=([a-zA-Z\s]+)\](.*?)\[/font\]#is';
	$bb_close_f[] = array('forum_font_tag', '1', '2');
	while (preg_match('#\n?\[list\](.+?)\[/list\]\n?#ies', $text)) {
		$text = preg_replace('#\n?\[list\](.+?)\[/list\]\n?#ies', 'forum_list_tag(\'\\1\', \'0\')', $text);
	}
	while (preg_match('#\n?\[list=(a|A|i|I|1)\](.+?)\[/list\]\n?#ies', $text)) {
		$text = preg_replace('#\n?\[list=(a|A|i|I|1)\](.+?)\[/list\]\n?#ies', 'forum_list_tag(\'\\2\', \'\\1\')', $text);
	}
	$text = preg_replace($bb_open, $bb_close, $text);
	$text = _preg_replace($bb_open_f, $bb_close_f, $text);
	unset($bb_open, $bb_close, $bb_open_f, $bb_close_f);
	$tag = array(
		':/ '        => '<img src="' . $tpath['url::'] . '/smiles/12.gif" />',
		':)'         => '<img src="' . $tpath['url::'] . '/smiles/1.gif" />',
		':('         => '<img src="' . $tpath['url::'] . '/smiles/2.gif" />',
		':D'         => '<img src="' . $tpath['url::'] . '/smiles/3.gif" />',
		':cool:'     => '<img src="' . $tpath['url::'] . '/smiles/4.gif" />',
		':up:'       => '<img src="' . $tpath['url::'] . '/smiles/4.gif" />',
		':rolleyes:' => '<img src="' . $tpath['url::'] . '/smiles/5.gif" />',
		':o'         => '<img src="' . $tpath['url::'] . '/smiles/6.gif" />',
		':shock:'    => '<img src="' . $tpath['url::'] . '/smiles/6.gif" />',
		':|'         => '<img src="' . $tpath['url::'] . '/smiles/9.gif" />',
		':down:'     => '<img src="' . $tpath['url::'] . '/smiles/9.gif" />',
		':angry:'    => '<img src="' . $tpath['url::'] . '/smiles/9.gif" />',
		':lol:'      => '<img src="' . $tpath['url::'] . '/smiles/10.gif" />',
		';)'         => '<img src="' . $tpath['url::'] . '/smiles/11.gif" />',
		':P'         => '<img src="' . $tpath['url::'] . '/smiles/13.gif" />',
		':mad:'      => '<img src="' . $tpath['url::'] . '/smiles/14.gif" />',
		':sick:'     => '<img src="' . $tpath['url::'] . '/smiles/14.gif" />',
		'[c]'    => '&copy;',
		'[tm]'   => '&#153;',
		'[r]'    => '&reg;',
		'[hr]'   => '<hr>',
		'[hr /]' => '<hr>',
		"\t"     => '&nbsp;&nbsp;&nbsp;&nbsp;',
		"\r\n"   => "\n",
		"\r"     => "\n",
		'<br>'   => '',
		'\s'     => '',
		'<br />' => '',
	);
	$text = str_ireplace(array_keys($tag), array_values($tag), $text);
	unset($tag);
	$text = nl2br($text);

	return trim($text);
}

function _preg_replace($pattern, $replacement, $subject) {

	if (is_array($pattern)) {
		foreach ($pattern as $key => $value) {
			$subject = preg_replace_callback(
				$value,
				function ($match) use (&$replacement, &$key) {

					return call_user_func($replacement[$key][0], $match[$replacement[$key][1]], $match[$replacement[$key][2]]);
				},
				$subject
			);
		}
	}

	return $subject;
}

function split_text($text, $width = 90, $break = "\n") {

	return preg_replace('#([^\s]{' . $width . '})#s', '$1' . $break, $text);
}

function forum_list_tag($list, $num) {

	if (empty($num)) {
		return '<ul>' . forum_list_item_tag($list) . '</ul>';
	} else {
		return '<ol type=' . $num . '>' . forum_list_item_tag($list) . '</ol>';
	}
}

function forum_list_item_tag($list) {

	$bb_open[] = '#\n?\[\*\](.+?)\[/\*\]\n?#is';
	$bb_close[] = "'<li>\\1</li>'";
	$list = preg_replace($bb_open, $bb_close, $list);

	return $list;
}

function forum_open_quote_tag() {

	return '<blockquote><div class="incqbox"><p>';
}

function forum_close_quote_tag() {

	return '</p></div></blockquote>';
}

function forum_quote_tag($quote) {

	return '<blockquote><div class="incqbox"><h4>' . $quote . ' пишет:</h4><p>';
}

function forum_color_tag($color, $show) {

	$color = preg_replace('/[^\d\w\#\s]/s', '', $color);

	return '<span style="color:' . $color . '">' . $show . '</span>';
}

function forum_font_tag($font, $show) {

	$font = preg_replace('/[^\d\w\#\-\_\s]/s', '', $font);

	return '<span style="font-family:' . $font . '">' . $show . '</span>';
}

function forum_size_tag($size, $show) {

	$size = $size + 7;
	if ($size > 30)
		$size = 30;

	return '<span style="font-size:' . $size . 'px">' . $show . '</span>';
}

function forum_url_tag($url, $show) {

	if (empty($show)) return;
	//$show = secureinput($show);
	$url = secureinput($url);
	if (substr($url, 0, 7) != 'http://')
		$url = 'http://' . $url;

	return '<a href=\'' . $url . '\' target=\'_blank\'>' . $show . '</a>';
}

function forum_img_tag($url, $text) {

	if (empty($url)) return;
	if (substr($url, 0, 7) != 'http://')
		$url = 'http://' . $url;
	$url = secureinput($url);

	return '<img src=\'' . $url . '\' border=\'0\' alt=\'' . $text . '\' />';
}

function forum_html_tag($text) {

	if (empty($text)) return;
	$text = "\n" . trim($text);
	$arr = range(1, substr_count(trim($text), "\n") + 1);
	$num = implode("\n", $arr);
	$text = preg_replace("#&lt;([^&<>]+)&gt;#", "&lt;<span style='color:blue'>\\1</span>&gt;", $text);
	$text = preg_replace("#&lt;([^&<>]+)=#", "&lt;<span style='color:blue'>\\1</span>=", $text);
	$text = preg_replace("#&lt;/([^&]+)&gt;#", "&lt;/<span style='color:blue'>\\1</span>&gt;", $text);
	$text = preg_replace("!=(&quot;|&#39;)(.+?)?(&quot;|&#39;)(\s|&gt;)!", "=\\1<span style='color:orange'>\\2</span>\\3\\4", $text);
	$text = preg_replace("!&#60;&#33;--(.+?)--&#62;!", "&lt;&#33;<span style='color:red'>--\\1--</span>&gt;", $text);
	$line = '<div style="float:left;'
		. ' border-right:1px solid;'
		. ' background-color:#2A52BE;'
		. ' padding-left:3px;'
		. ' padding-right:3px;'
		. ' margin-right:2px;font-size:13px'
		. ' margin-top:-5px;'
		. ' text-align:right;">'
		. "<code style=\"color:#FFFFFF\">\n" . $num . "\n</code></div>";

	return '<div class="codewrap"><div class="codetop">Код: HTML</div><div class="codemain">' . $line . $text . '</div></div>';
}

function forum_php_tag($text) {

	if (empty($text)) return;
	$text = htmlspecialchars_decode($text, ENT_QUOTES);
	$text = "<?php\n" . trim($text) . "\n?>";
	$arr = range(1, substr_count($text, "\n") + 0);
	$text = highlight_string($text, true);
	$num = implode("\n", $arr);
	$line = '<div style="float:left;'
		. ' border-right:1px solid;'
		. ' background-color:#2A52BE;'
		. ' padding-left:3px;'
		. ' padding-right:3px;'
		. ' margin-right:2px;font-size:13px'
		. ' margin-top:-5px;'
		. ' text-align:right;">'
		. "<code style=\"color:#FFFFFF\">\n" . $num . "\n</code></div>";

	return '<div class="codewrap"><div class="codetop">Код: PHP</div><div class="codemain">' . $line . $text . '</div></div>';
}

function forum_code_tag($code, $text) {

	switch (strtolower($code)) {
		case 'php':
			return forum_php_tag($text);
			break;
		case 'html':
			return forum_html_tag($text);
			break;
		default:
			return forum_html_tag($text);
	}
}

/* function print_bbtags(){global $twig;
	$tpath = locatePluginTemplates(array('bb_tags', ':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['bb_tags'].'bb_tags.tpl');
	
	$tVars = array(
		'tpl' => $tpath['url::'],
		'js' => '<script type="text/javascript" src="'.$tpath['url::'].'/bb.js"></script>',
	);
	
	return $xt->render($tVars);;
}
 */
function strip_bb_tags($text) {

	$text = preg_replace("/\[[^]]+\]/i", "", $text);

	return $text;
}