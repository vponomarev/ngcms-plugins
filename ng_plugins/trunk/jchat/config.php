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
array_push($cfgX, array('name' => 'access', 'title' => $lang['jchat:access'], 'descr' => $lang['jchat:access#desc'], 'type' => 'select', 'values' => array ('0' => $lang['jchat:access.off'], '1' => $lang['jchat:access.ro'], '2' => $lang['jchat:access.rw']), 'value' => pluginGetVariable($plugin,'access')));
array_push($cfgX, array('name' => 'rate_limit', 'title' => $lang['jchat:rate_limit'], 'descr' => $lang['jchat:rate_limit#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin,'rate_limit')));
array_push($cfgX, array('name' => 'maxwlen', 'title' => $lang['jchat:maxwlen'], 'descr' => $lang['jchat:maxwlen#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin,'maxwlen')));
array_push($cfgX, array('name' => 'maxlen', 'title' => $lang['jchat:maxlen'], 'descr' => $lang['jchat:maxlen#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin,'maxlen')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['jchat:conf.main'].'</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'enable_panel', 'title' => $lang['jchat:enable.panel'], 'descr' => $lang['jchat:enable.panel#desc'], 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => pluginGetVariable($plugin,'enable_panel')));
array_push($cfgX, array('name' => 'refresh', 'title' => $lang['jchat:refresh'], 'descr' => $lang['jchat:refresh#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin,'refresh')));
array_push($cfgX, array('name' => 'history', 'title' => $lang['jchat:history'], 'descr' => $lang['jchat:history#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin,'history')));
array_push($cfgX, array('name' => 'maxidle', 'title' => $lang['jchat:maxidle'], 'descr' => $lang['jchat:maxidle#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin,'maxidle')));
array_push($cfgX, array('name' => 'order', 'title' => $lang['jchat:order'], 'descr' => $lang['jchat:order#desc'], 'type' => 'select', 'values' => array('0' => $lang['jchat:order.asc'], '1' => $lang['jchat:order.desc']), 'value' => pluginGetVariable($plugin,'order')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['jchat:conf.panel'].'</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'enable_win', 'title' => $lang['jchat:enable.win'], 'descr' => $lang['jchat:enable.win#desc'], 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => pluginGetVariable($plugin,'enable_win')));
array_push($cfgX, array('name' => 'win_mode', 'title' => $lang['jchat:win.mode'], 'descr' => $lang['jchat:win.mode#desc'], 'type' => 'select', 'values' => array('0' => $lang['jchat:win.mode.internal'], '1' => $lang['jchat:win.mode.external']), 'value' => pluginGetVariable($plugin,'win_mode')));
array_push($cfgX, array('name' => 'win_refresh', 'title' => $lang['jchat:refresh'], 'type' => 'input', 'value' => pluginGetVariable($plugin,'win_refresh')));
array_push($cfgX, array('name' => 'win_history', 'title' => $lang['jchat:history'], 'type' => 'input', 'value' => pluginGetVariable($plugin,'win_history')));
array_push($cfgX, array('name' => 'win_maxidle', 'title' => $lang['jchat:maxidle'], 'type' => 'input', 'value' => pluginGetVariable($plugin,'win_maxidle')));
array_push($cfgX, array('name' => 'win_order', 'title' => $lang['jchat:order'], 'type' => 'select', 'values' => array('0' => $lang['jchat:order.asc'], '1' => $lang['jchat:order.desc']), 'value' => pluginGetVariable($plugin,'win_order')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['jchat:conf.window'].'</b>', 'entries' => $cfgX));


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}