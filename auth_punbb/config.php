<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
pluginsLoadConfig();
// Load lang files
LoadPluginLang('auth_punbb', 'config', '', 'auth');
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => $lang['auth_description']));
array_push($cfgX, array('descr' => $lang['auth_extdb_fulldesc']));
array_push($cfgX, array('name' => 'extdb', 'title' => $lang['auth_extdb_extdb'], 'descr' => $lang['auth_extdb_extdb_desc'], 'type' => 'select', 'values' => array('1' => $lang['yesa'], '0' => $lang['noa']), 'value' => extra_get_param('auth_punbb', 'extdb')));
array_push($cfgX, array('name' => 'dbhost', 'title' => $lang['auth_extdb_dbhost'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'dbhost')));
array_push($cfgX, array('name' => 'dbname', 'title' => $lang['auth_extdb_dbname'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'dbname')));
array_push($cfgX, array('name' => 'dblogin', 'title' => $lang['auth_extdb_dblogin'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'dblogin')));
array_push($cfgX, array('name' => 'dbpass', 'title' => $lang['auth_extdb_dbpass'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'dbpass')));
array_push($cfg, array('mode' => 'group', 'title' => $lang['auth_extdb'], 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'dbprefix', 'title' => $lang['auth_params_prefix'], 'descr' => $lang['auth_params_prefix_desc'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'dbprefix')));
array_push($cfgX, array('name' => 'cookie_seed', 'title' => $lang['auth_params_seed'], 'descr' => $lang['auth_params_seed_desc'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'cookie_seed')));
array_push($cfgX, array('name' => 'cookie_domain', 'title' => $lang['auth_params_domain'], 'descr' => $lang['auth_params_domain_desc'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'cookie_domain')));
array_push($cfg, array('mode' => 'group', 'title' => $lang['auth_params'], 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'initial_group_id', 'title' => $lang['auth_reg_group'], 'descr' => $lang['auth_reg_group_desc'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'initial_group_id')));
array_push($cfgX, array('name' => 'reg_lang', 'title' => $lang['auth_reg_lang'], 'descr' => $lang['auth_reg_lang_desc'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'reg_lang')));
array_push($cfgX, array('name' => 'reg_style', 'title' => $lang['auth_reg_style'], 'descr' => $lang['auth_reg_style_desc'], 'type' => 'input', 'value' => extra_get_param('auth_punbb', 'reg_style')));
array_push($cfg, array('mode' => 'group', 'title' => $lang['auth_reg'], 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'userjoin', 'title' => $lang['auth_auto_join'], 'descr' => $lang['auth_auto_join_desc'], 'type' => 'select', 'values' => array('1' => $lang['yesa'], '0' => $lang['noa']), 'value' => extra_get_param('auth_punbb', 'userjoin')));
array_push($cfgX, array('name' => 'autocreate_ng', 'title' => $lang['auth_auto_ng'], 'descr' => $lang['auth_auto_ng_desc'], 'type' => 'select', 'values' => array('1' => $lang['yesa'], '0' => $lang['noa']), 'value' => extra_get_param('auth_punbb', 'autocreate_ng')));
array_push($cfgX, array('name' => 'autocreate_punbb', 'title' => $lang['auth_auto_punbb'], 'descr' => $lang['auth_auto_punbb_desc'], 'type' => 'select', 'values' => array('1' => $lang['yesa'], '0' => $lang['noa']), 'value' => extra_get_param('auth_punbb', 'autocreate_punbb')));
array_push($cfg, array('mode' => 'group', 'title' => $lang['auth_auto'], 'entries' => $cfgX));
// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('auth_punbb', $cfg);
	print_commit_complete('auth_punbb');
} else {
	generate_config_page('auth_punbb', $cfg);
}

