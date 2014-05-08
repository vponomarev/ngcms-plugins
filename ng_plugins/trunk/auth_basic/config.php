<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

$lang = LoadLang('users', 'admin');

// Load lang files
LoadPluginLang('auth_basic', 'config', '', 'auth', ':');

// Default user group for new users
$regGroup = intval(pluginGetVariable('auth_basic','regstatus'));
if (!isset($UGROUP[$regGroup])) {
	// If GROUP is not defined - set "4" as default
	$regGroup = 4; // Commenter
}
$groupOptions = array();
foreach ($UGROUP as $k => $v) {
	$groupOptions[$k] = $k . ' - '. $v['name'];
}


$lastupdate = intval(pluginGetVariable('auth_basic', 'lastupdate'));
if ($lastupdate<1) { $lastupdate = ''; }

// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => $lang['auth:description']));
array_push($cfgX, array('name' => 'lastupdate', 'title' => $lang['auth:lastupdate'], 'descr' => $lang['auth:lastupdate_descr'],'type' => 'input', value => $lastupdate));
array_push($cfgX, array('name' => 'regstatus', 'title' => $lang['auth:regstatus'], 'descr' => $lang['auth:regstatus_descr'],'type' => 'select',  'values' => $groupOptions, value => $regGroup));
array_push($cfgX, array('name' => 'restorepw', 'title' => $lang['auth:restorepw'], 'descr' => $lang['auth:restorepw_descr'],'type' => 'select',  'values' => array ( '0' => $lang['auth:restore_disabled'], 'login' => $lang['auth:restore_login'], 'email' => $lang['auth:restore_email'], 'both' => $lang['auth:restore_both']), value => pluginGetVariable('auth_basic','restorepw')));
array_push($cfgX, array('name' => 'regcharset', 'title' => $lang['auth:regcharset'], 'descr' => $lang['auth:regcharset_descr'],'type' => 'select',  'values' => array ( '0' => 'Eng', '1' => 'Rus', '2' => 'Eng+Rus', '3' => 'All'), value => pluginGetVariable('auth_basic','regcharset')));
array_push($cfgX, array('name' => 'iplock', 'title' => $lang['auth:iplock'], 'descr' => $lang['auth:iplock_descr'],'type' => 'select',  'values' => array ( '0' => $lang['noa'], '1' => $lang['yesa']), value => pluginGetVariable('auth_basic','iplock')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['auth:block.main'].'</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'en_dbprefix', 'title' => $lang['auth:en_dbprefix'], 'descr' => $lang['auth:en_dbprefix_descr'],'type' => 'checkbox', value => pluginGetVariable('auth_basic','en_dbprefix')));
array_push($cfgX, array('name' => 'dbprefix', 'title' => $lang['auth:dbprefix'], 'descr' => $lang['auth:dbprefix_descr'],'type' => 'input', value => pluginGetVariable('auth_basic','dbprefix')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['auth:block.uprefix'].'</b>', 'entries' => $cfgX));


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('auth_basic', $cfg);
	print_commit_complete('auth_basic');
} else {
	generate_config_page('auth_basic', $cfg);
}

