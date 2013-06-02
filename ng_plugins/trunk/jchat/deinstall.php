<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();
loadPluginLang('jchat', 'config', '', '', ':');


$db_update = array(
	array(
		'table'		=>	'jchat',
		'action'	=>	'drop',
	),
	array(
		'table'		=>	'jchat_events',
		'action'	=>	'drop',
	),
);

if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
		plugin_mark_deinstalled($plugin);
	}
} else {
	generate_install_page('jchat', $lang['jchat:desc_deinstall'], 'deinstall');
}


