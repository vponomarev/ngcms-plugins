<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

function plugin_trace7_generate(){
	global $template, $timer;

	$template['vars']['plugin_ads_trade7_tizer'] = '';

	// Banners
	$banner_begin	= "<!-- BB banner code start. Version 20090316. -->";
	$banner_end		= "<!-- BB banner code end -->";

	// Timeout
	$timeout_sec	= intval(pluginGetVariable('plugin_ads7','timeout_sec'));
	$timeout_usec	= intval(pluginGetVariable('plugin_ads7','timeout_usec'));

	// Set default timeout to '2 sec'
	if (!$timeout_sec && !$timeout_usec)
		$timeout_sec = 2;

	if ($timeout_sec < 1) {
		$timeout_sec = 1; $timeout_usec = 0;
	}

	// Prepare HTTP query
	$q = "GET /bb?v=php20090316".
			'&id='.pluginGetVariable('ads_trade7', 'id').
			'&cs='.pluginGetVariable('ads_trade7', 'cs').
			'&categories_2='.pluginGetVariable('ads_trade7', 'categories_2').
			'&size='.pluginGetVariable('ads_trade7', 'size').' '.
			"HTTP/1.0\r\n".
			"Host: trade7.ru\r\n";

	if ($sp=$_SERVER["REMOTE_ADDR"])
		$q .= "X-IP: " . $sp . "\r\n";

	if (($sp=$_SERVER["REQUEST_URI"]))
		$q .= "X-URL: " . "http://" . $_SERVER["HTTP_HOST"] . (($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80)?':'.$_SERVER["SERVER_PORT"]:'') . $sp . "\r\n";

	foreach (array('HTTP_HOST', 'HTTP_USER_AGENT', 'HTTP_ACCEPT', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_CHARSET') as $k) {
		if (isset($_SERVER[$k]))
			$q .= $k.': '.$_SERVER[$k]."\r\n";
	}
	$q .= "\r\n";

	// Open socket
	$tBegin = $timer->stop(4);

	$sock = fsockopen ("trade7.ru", 80, $errno, $errstr, $timeout_sec + ($timeout_usec/1000));
	stream_set_timeout($sock, $timeout_sec, $timeout_usec);
	if ($sock && fputs ($sock, $q)) {
		$hdr = fgets ($sock, 1024);
		if (preg_match ("/^HTTP\S+\s200\D/i", $hdr)) {
			while ($hdr = fgets ($sock, 1024))
				if (chop($hdr) == "") break;

			$s = trim (stream_get_contents ($sock));
			if (strlen ($s) > 0) {
				$template['vars']['plugin_ads_trade7_tizer'] = '<!-- Tizer load time: '. ($timer->stop(4) - $tBegin)." -->\n" . $banner_begin . $s . $banner_end;
				return;
			}
		}
	}

	// If data were not successfully fetched
	if (pluginGetVariable('ads_trade7', 'default'))
		$template['vars']['plugin_ads_trade7_tizer'] = pluginGetVariable('ads_trade7', 'default');

	return;
}

add_act('index_post', 'plugin_trace7_generate');
