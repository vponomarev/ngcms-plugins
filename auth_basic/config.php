<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

// Load lang files
LoadPluginLang('auth_basic', 'config', '', 'auth');

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => $lang['auth_description']));
array_push($cfg, array('name' => 'restorepw', 'title' => $lang['auth_restorepw'], 'descr' => $lang['auth_restorepw_descr'],'type' => 'select',  'values' => array ( '0' => $lang['auth_restore_disabled'], 'login' => $lang['auth_restore_login'], 'email' => $lang['auth_restore_email'], 'both' => $lang['auth_restore_both']), value => extra_get_param('auth_basic','restorepw')));
array_push($cfg, array('name' => 'en_dbprefix', 'title' => $lang['auth_en_dbprefix'], 'descr' => $lang['auth_en_dbprefix_descr'],'type' => 'checkbox', value => extra_get_param('auth_basic','en_dbprefix')));
array_push($cfg, array('name' => 'dbprefix', 'title' => $lang['auth_dbprefix'], 'descr' => $lang['auth_dbprefix_descr'],'type' => 'text', value => extra_get_param('auth_basic','dbprefix')));

//array_push($cfg, array('name' => 'multilogin', title => 'Разрешить многократный вход', 'descr' => 'возможность работы с нескольких мест одновременно.<br />!!! снижает производительность !!!','type' => 'select', 'values' => array ( '0' => 'нет', '1' => 'да'), value => extra_get_param('auth_basic','multilogin')));


// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('auth_basic', $cfg);
	print_commit_complete('auth_basic');
} else {
	generate_config_page('auth_basic', $cfg);
}


?>