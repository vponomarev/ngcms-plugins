<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
pluginsLoadConfig();
$db_update = array(
	array(
		'table'  => 'faq',
		'action' => 'drop',
	),
);
if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
		plugin_mark_deinstalled($plugin);
	}
} else {
	generate_install_page($plugin, 'Удаление плагина', 'deinstall');
}
?>