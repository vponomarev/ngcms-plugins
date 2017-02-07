<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
loadPluginLang('wpinger', 'config', '', '', ':');
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => $lang['wpinger:desc']));
array_push($cfg, array('name' => 'proxy', 'title' => $lang['wpinger:proxy'], 'descr' => $lang['wpinger:proxy#desc'], 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => pluginGetVariable('wpinger', 'proxy')));
array_push($cfg, array('name' => 'urls', 'title' => $lang['wpinger:urls'], 'descr' => $lang['wpinger:urls#desc'], 'type' => 'text', 'html_flags' => 'rows=4 cols=60', 'value' => pluginGetVariable('wpinger', 'urls')));
#array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['jchat:main'].'</b>', 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('wpinger', $cfg);
	print_commit_complete('wpinger');
} else {
	generate_config_page('wpinger', $cfg);
}