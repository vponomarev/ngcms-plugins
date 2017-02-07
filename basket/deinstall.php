<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
plugins_load_config();
loadPluginLang('basket', 'config', '', '', ':');
$db_update = array(
	array(
		'table'  => 'basket',
		'action' => 'drop',
	),
);
if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
		plugin_mark_deinstalled($plugin);
	}
} else {
	generate_install_page('basket', $lang['basket:desc_deinstall'], 'deinstall');
}


