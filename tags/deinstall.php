<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();
LoadPluginLang($plugin, 'main');


$db_update = array(
	array(
		'table'		=>	'tags',
		'action'	=>	'drop',
	),
	array(
		'table'		=>	'tags_index',
		'action'	=>	'drop',
	),
//	array(
//		'table'		=>	'news',
//		'action'	=>	'modify',
//		'fields'	=>	array(
//			array('action' => 'drop', 'name' => 'tags')
//		)
//	)
);

if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
		plugin_mark_deinstalled($plugin);
	}
} else {
	generate_install_page($plugin, '', 'deinstall');
}

?>