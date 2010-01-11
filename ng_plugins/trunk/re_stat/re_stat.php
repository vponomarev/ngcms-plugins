<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

$re_stat_values = pluginGetVariable('re_stat', 'values');
foreach ($re_stat_values as $key => $row) register_plugin_page('re_stat', $row['code'], 'plugin_re_stat');

function plugin_re_stat($params)
{
	global $CurrentHandler;
	$idstat = 0;
	$values = pluginGetVariable('re_stat', 'values');
	foreach ($values as $key => $row) if ($row['code'] == $CurrentHandler['handlerName']) {$idstat = $row['id']; break;}
	include_once root.'includes/static.php';
	showStaticPage(array('id' => $idstat));
}