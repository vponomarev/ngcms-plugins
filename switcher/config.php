<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

// Load lang files
LoadPluginLang('switcher', 'config');

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => $lang['switcher_description']));

$lang_list[] = $lang['switcher_bydefault'];
$lang_list = array_merge($lang_list, ListFiles('lang', ''));

$tpl_list[] = $lang['switcher_bydefault'];
$tpl_list = array_merge($tpl_list, ListFiles('../templates', ''));

$cfgX = array();
$profile_count = intval(pluginGetVariable('switcher','count'));
if (!$profile_count) { $profile_count = 3; }

array_push($cfgX, array('name' => 'count', 'title' => $lang['switcher_count'], 'descr' => $lang['switcher_count_desc'], 'type' => 'input', 'html_flags' => ' size="5"', 'value' => $profile_count));
array_push($cfgX, array('name' => 'selfpage', 'title' => $lang['switcher_selfpage'], 'descr' => $lang['switcher_selfpage_desc'], 'type' => 'select', 'values' => array ( '1' => $lang['yesa'], '0' => $lang['noa']), 'value' => pluginGetVariable('switcher','selfpage')));
array_push($cfgX, array('name' => 'localsource', 'title' => $lang['switcher_localsource'], 'descr' => $lang['switcher_localsource#desc'], 'type' => 'select', 'values' => array ( '0' => $lang['switcher_localsource_site'], '1' => $lang['switcher_localsource_plugin']), 'value' => intval(pluginGetVariable($plugin,'localsource'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['switcher_commonconfig'].'</b>', 'entries' => $cfgX));


for ($i = 1; $i <= $profile_count; $i++) {
	$cfgX = array();
	array_push($cfgX, array('name' => 'profile'.$i.'_active', 'title' => $lang['switcher_flagactive'], 'type' => 'select', 'values' => array ( '1' => $lang['yesa'], '0' => $lang['noa']), 'value' => pluginGetVariable('switcher','profile'.$i.'_active')));
	array_push($cfgX, array('name' => 'profile'.$i.'_template', 'title' => $lang['switcher_template'], 'descr' => $lang['switcher_template_desc'], 'type' => 'select', 'values' => $tpl_list, 'value' => pluginGetVariable('switcher','profile'.$i.'_template')));
	array_push($cfgX, array('name' => 'profile'.$i.'_lang', 'title' => $lang['switcher_lang'], 'descr' => $lang['switcher_lang_desc'],'type' => 'select', 'values' => $lang_list, 'value' => pluginGetVariable('switcher','profile'.$i.'_lang')));
	array_push($cfgX, array('name' => 'profile'.$i.'_name', 'title' => $lang['switcher_name'], 'descr' => $lang['switcher_name_desc'],'type' => 'input', 'value' => pluginGetVariable('switcher','profile'.$i.'_name')));
	array_push($cfgX, array('name' => 'profile'.$i.'_id', 'title' => $lang['switcher_id'], 'descr' => $lang['switcher_id_desc'],'type' => 'input', 'value' => pluginGetVariable('switcher','profile'.$i.'_id')));
	array_push($cfgX, array('name' => 'profile'.$i.'_redirect', 'title' => $lang['switcher_redirect'], 'descr' => $lang['switcher_redirect_desc'],'type' => 'input', 'html_flags' => ' size="45"','value' => pluginGetVariable('switcher','profile'.$i.'_redirect')));
	array_push($cfgX, array('name' => 'profile'.$i.'_domains', 'title' => $lang['switcher_domains'], 'descr' => $lang['switcher_domains_desc'],'type' => 'text', 'html_flags' => 'cols=30 rows=3', 'value' => pluginGetVariable('switcher','profile'.$i.'_domains')));
	array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['switcher_profile'].' ¹'.$i.'</b>', 'entries' => $cfgX));
}


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
