<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//
function plugin_cron_commit() {
	global $CRONDATA;
	if (!isset($_POST['data']) || !is_array($_POST['data'])) {
		return false;
	}

	$cronLines = array();
	foreach ($_POST['data'] as $k => $v) {
		if (!is_array($v))
			return false;

		// Check if values are set
		foreach (array('plugin', 'handler', 'min', 'hour', 'day', 'month', 'dow') as $xk) {
			if (!isset($v[$xk]))
				return array(false, $k, $xk);
			$v[$xk] = trim($v[$xk]);
		}
		// Check content
		if ($v['plugin'] == '') {
			// EMPTY LINE, skip
			continue;
		}

		if (($v['min'] != '*') && ((!preg_match('#^\d+$#', $v['min'], $null)) || ($v['min'] < 0) || ($v['min'] > 59)))
			return array(false, $k, 'min', $v['min']);

		if (($v['hour'] != '*') && ((!preg_match('#^\d+$#', $v['hour'], $null)) || ($v['hour'] < 0) || ($v['hour'] > 23)))
			return array(false, $k, 'hour');

		if (($v['day'] != '*') && ((!preg_match('#^\d+$#', $v['day'], $null)) || ($v['day'] < 1) || ($v['day'] > 31)))
			return array(false, $k, 'day');

		if (($v['month'] != '*') && ((!preg_match('#^\d+$#', $v['month'], $null)) || ($v['month'] < 1) || ($v['month'] > 31)))
			return array(false, $k, 'month');

		if (($v['dow'] != '*') && ((!preg_match('#^\d+$#', $v['dow'], $null)) || ($v['dow'] < 1) || ($v['dow'] > 6)))
			return array(false, $k, 'dow');

		$cronLines []= array($v['min']."\t".$v['hour']."\t".$v['day']."\t".$v['month']."\t".$v['dow']."\t".$v['plugin']."\t".$v['handler']);
	}

	// Populate cron info field
	$CRONDATA = $cronLines;
	cron_save();

	return true;
}


function plugin_cron_fillEntries($cronData) {
	$rowNum = 1;
	$entries = array();
	foreach ($cronData as $k => $v) {
		$tEntry = array(
			'id'		=> $rowNum,
			'plugin'	=> $v[6],
			'handler'	=> $v[7],
			'min'		=> $v[1],
			'hour'		=> $v[2],
			'day'		=> $v[3],
			'month'		=> $v[4],
			'dow'		=> $v[5],
		);

		$entries[]= $tEntry;
		$rowNum++;
	}
	$entries[] = array('id' => $rowNum);
	return $entries;
}



// Preload config file
plugins_load_config();

// Load language
LoadPluginLang('cron', 'config', '', '', '#');


// Preload plugin engine
include_once 'cron.php';

$cronData	= cron_load();

$tVars = array(
	'token'		=> genUToken('admin.extra-config'),
	'entries'	=> array(),
);


// Check for desired behaviour
if ($_REQUEST['action'] == 'commit') {
	$tVars = array();
	// ** Update request
	$res = plugin_cron_commit();
	if ($res !== true) {
		// ERROR
		$tVars['msg'] = $lang['cron']['result_err'];
	} else {
		$tVars['msg'] = $lang['cron']['result_ok'];
	}

	$xt = $twig->loadTemplate('plugins/cron/tpl/done.tpl');
	echo $xt->render($tVars);
} else {
	$tVars['entries'] = plugin_cron_fillEntries($cronData);
	$xt = $twig->loadTemplate('plugins/cron/tpl/config.tpl');
	echo $xt->render($tVars);
}
