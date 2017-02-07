<?php
# protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');
/*
 * configuration file for plugin
 */
# preload config file
pluginsLoadConfig();
# load lang files
LoadPluginLang($plugin, 'config', '', '', ':');
# fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => $lang['bookmarks:descr']));
$cfgX = array();
array_push($cfgX, array(
	'name'   => 'sidebar',
	'title'  => $lang['bookmarks:sidebar'],
	'type'   => 'select',
	'values' => array('1' => $lang['bookmarks:label_yes'], '0' => $lang['bookmarks:label_no']),
	'value'  => intval(pluginGetVariable($plugin, 'sidebar'))
));
array_push($cfgX, array(
	'name'  => 'max_sidebar',
	'title' => $lang['bookmarks:max_sidebar'],
	'type'  => 'input',
	'value' => intval(pluginGetVariable($plugin, 'max_sidebar')) ? pluginGetVariable($plugin, 'max_sidebar') : '10'
));
array_push($cfgX, array(
	'name'   => 'hide_empty',
	'title'  => $lang['bookmarks:hide_empty'],
	'type'   => 'select',
	'values' => array('1' => $lang['bookmarks:label_yes'], '0' => $lang['bookmarks:label_no']),
	'value'  => intval(pluginGetVariable($plugin, 'hide_empty'))
));
array_push($cfgX, array(
	'name'  => 'maxlength',
	'title' => $lang['bookmarks:maxlength'],
	'type'  => 'input',
	'value' => intval(pluginGetVariable($plugin, 'maxlength')) ? pluginGetVariable($plugin, 'maxlength') : '100'
));
array_push($cfgX, array(
	'name'   => 'counter',
	'title'  => $lang['bookmarks:counter'],
	'type'   => 'select',
	'values' => array('1' => $lang['bookmarks:label_yes'], '0' => $lang['bookmarks:label_no']),
	'value'  => intval(pluginGetVariable($plugin, 'counter'))
));
array_push($cfgX, array(
	'name'   => 'news_short',
	'title'  => $lang['bookmarks:news.short'],
	'type'   => 'select',
	'values' => array('1' => $lang['bookmarks:label_yes'], '0' => $lang['bookmarks:label_no']),
	'value'  => intval(pluginGetVariable($plugin, 'news_short'))
));
array_push($cfgX, array(
	'name'  => 'bookmarks_limit',
	'title' => $lang['bookmarks:bookmarks_limit'],
	'type'  => 'input',
	'value' => intval(pluginGetVariable($plugin, 'bookmarks_limit')) ? pluginGetVariable($plugin, 'bookmarks_limit') : '100'
));
array_push($cfg, array('mode' => 'group', 'title' => $lang['bookmarks:title_plugin_settings'], 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array(
	'name'   => 'localsource',
	'title'  => $lang['bookmarks:templates_source'],
	'type'   => 'select',
	'values' => array('0' => $lang['bookmarks:select_main_tpl'], '1' => $lang['bookmarks:select_plugin_tpl']),
	'value'  => intval(pluginGetVariable($plugin, 'localsource'))
));
array_push($cfg, array('mode' => 'group', 'title' => $lang['bookmarks:title_view'], 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array(
	'name'   => 'cache',
	'title'  => $lang['bookmarks:use_cache'],
	'type'   => 'select',
	'values' => array('1' => $lang['bookmarks:label_yes'], '0' => $lang['bookmarks:label_no']),
	'value'  => intval(pluginGetVariable($plugin, 'cache'))
));
array_push($cfgX, array(
	'name'  => 'cacheExpire',
	'title' => $lang['bookmarks:cache_expire'],
	'type'  => 'input',
	'value' => intval(pluginGetVariable($plugin, 'cacheExpire')) ? pluginGetVariable($plugin, 'cacheExpire') : '60'
));
array_push($cfg, array('mode' => 'group', 'title' => $lang['bookmarks:title_cache'], 'entries' => $cfgX));
# RUN
if ($_REQUEST['action'] == 'commit') {
	# if submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}