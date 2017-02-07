<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
loadPluginLang('tracker', 'config', '', '', ':');
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfgX, array('descr' => $lang['tracker:desc']));
array_push($cfgX, array('name' => 'storrent', 'title' => $lang['tracker:support.torrent'], 'descr' => $lang['tracker:support.torrent#desc'], 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => pluginGetVariable($plugin, 'storrent')));
array_push($cfgX, array('name' => 'smagnet', 'title' => $lang['tracker:support.magnet'], 'descr' => $lang['tracker:support.magnet#desc'], 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => pluginGetVariable($plugin, 'smagnet')));
array_push($cfgX, array('name' => 'tracker', 'title' => $lang['tracker:tracker'], 'descr' => str_replace('{tracker_url}', generatePluginLink('tracker', 'announce', array(), array(), false, true), $lang['tracker:tracker#desc']), 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => pluginGetVariable($plugin, 'tracker')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>' . $lang['tracker:main'] . '</b>', 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}