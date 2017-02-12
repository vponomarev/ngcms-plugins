<?php
# protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');
/*
 * configuration file for plugin
 */
# preload config file
pluginsLoadConfig();
$cfg = array();
$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => '¬ыберите каталог из которого плагин будет брать шаблоны дл€ отображени€<br /><small><b>Ўаблон сайта</b> - плагин будет пытатьс€ вз€ть шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут вз€ты из собственного каталога плагина<br /><b>ѕлагин</b> - шаблоны будут братьс€ из собственного каталога плагина</small>', 'type' => 'select', 'values' => array('0' => 'Ўаблон сайта', '1' => 'ѕлагин'), 'value' => intval(pluginGetVariable($plugin, 'localsource'))));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Ќастройки отображени€</b>', 'entries' => $cfgX));
# RUN 
if ($_REQUEST['action'] == 'commit') {
	# if submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
