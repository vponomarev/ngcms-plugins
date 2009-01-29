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

$regstatus = intval(extra_get_param('auth_basic','regstatus'));
if (($regstatus < 1)||($regstatus > 4))
 $regstatus = 4;

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => $lang['auth:description']));
array_push($cfg, array('name' => 'regstatus', 'title' => $lang['auth:regstatus'], 'descr' => $lang['auth:regstatus_descr'],'type' => 'select',  'values' => array ( '1' => '1 - '.$lang['st_1'], '2' => '2 - '.$lang['st_2'], '3' => '3 - '.$lang['st_3'], '4' => '4 - '.$lang['st_4']), value => $regstatus));
array_push($cfg, array('name' => 'restorepw', 'title' => $lang['auth:restorepw'], 'descr' => $lang['auth:restorepw_descr'],'type' => 'select',  'values' => array ( '0' => $lang['auth:restore_disabled'], 'login' => $lang['auth:restore_login'], 'email' => $lang['auth:restore_email'], 'both' => $lang['auth:restore_both']), value => extra_get_param('auth_basic','restorepw')));
array_push($cfg, array('name' => 'en_dbprefix', 'title' => $lang['auth:en_dbprefix'], 'descr' => $lang['auth:en_dbprefix_descr'],'type' => 'checkbox', value => extra_get_param('auth_basic','en_dbprefix')));
array_push($cfg, array('name' => 'dbprefix', 'title' => $lang['auth:dbprefix'], 'descr' => $lang['auth:dbprefix_descr'],'type' => 'text', value => extra_get_param('auth_basic','dbprefix')));

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