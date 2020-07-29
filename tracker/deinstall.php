<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
pluginsLoadConfig();
LoadPluginLang($plugin, 'main');
$db_update = array(
	array(
		'table'  => 'tracker_magnets',
		'action' => 'drop',
	),
	array(
		'table'  => 'news',
		'action' => 'modify',
		'fields' => array(
			array('action' => 'drop', 'name' => 'tracker_fileid'),
			array('action' => 'drop', 'name' => 'tracker_magnetid'),
			array('action' => 'drop', 'name' => 'tracker_infohash'),
			array('action' => 'drop', 'name' => 'tracker_lastupdate'),
			array('action' => 'drop', 'name' => 'tracker_seed'),
			array('action' => 'drop', 'name' => 'tracker_leech'),
		)
	)
);
if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
		plugin_mark_deinstalled($plugin);
	}
} else {
	generate_install_page($plugin, '', 'deinstall');
}
?>