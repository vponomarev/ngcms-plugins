<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
pluginsLoadConfig();
LoadPluginLang($plugin, 'config', '', '', ':');
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => $lang['other_user_news:description']));
$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => $lang['other_user_news:localsource'], 'descr' => $lang['other_user_news:localsource#desc'], 'type' => 'select', 'values' => array('0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(pluginGetVariable($plugin, 'localsource'))));
array_push($cfg, array('mode' => 'group', 'title' => $lang['other_user_news:group.source'], 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
?>
