<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

loadPluginLang('jchat', 'config', '', '', ':');

// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfgX, array('descr' => $lang['jchat:desc']));
array_push($cfgX, array('name' => 'access', 'title' => $lang['jchat:access'], 'descr' => $lang['jchat:access#desc'], 'type' => 'select', 'values' => array ('0' => $lang['jchat:access.off'], '1' => $lang['jchat:access.ro'], '2' => $lang['jchat:access.rw']), 'value' => extra_get_param($plugin,'access')));
array_push($cfgX, array('name' => 'refresh', 'title' => $lang['jchat:refresh'], 'descr' => $lang['jchat:refresh#desc'], 'type' => 'input', 'value' => extra_get_param($plugin,'refresh')));
array_push($cfgX, array('name' => 'history', 'title' => $lang['jchat:history'], 'descr' => $lang['jchat:history#desc'], 'type' => 'input', 'value' => extra_get_param($plugin,'history')));
array_push($cfgX, array('name' => 'rate_limit', 'title' => $lang['jchat:rate_limit'], 'descr' => $lang['jchat:rate_limit#desc'], 'type' => 'input', 'value' => extra_get_param($plugin,'rate_limit')));
array_push($cfgX, array('name' => 'maxidle', 'title' => $lang['jchat:maxidle'], 'descr' => $lang['jchat:maxidle#desc'], 'type' => 'input', 'value' => extra_get_param($plugin,'maxidle')));
array_push($cfgX, array('name' => 'maxwlen', 'title' => $lang['jchat:maxwlen'], 'descr' => $lang['jchat:maxwlen#desc'], 'type' => 'input', 'value' => extra_get_param($plugin,'maxwlen')));
array_push($cfgX, array('name' => 'maxlen', 'title' => $lang['jchat:maxlen'], 'descr' => $lang['jchat:maxlen#desc'], 'type' => 'input', 'value' => extra_get_param($plugin,'maxlen')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['jchat:main'].'</b>', 'entries' => $cfgX));


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}