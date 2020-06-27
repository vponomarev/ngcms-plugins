<?php

//
// Configuration file for plugin
//

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Preload config file
pluginsLoadConfig();

// Load lang files
LoadPluginLang($plugin, 'config', '', '', ':');

// Fill configuration parameters
$db_update = array(
	array(
		'table'  => 'site_stats',
		'action' => 'drop',
		),
	);

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
        $cron->unregisterTask('site_stats');
		plugin_mark_deinstalled($plugin);
	}
} else {
	generate_install_page($plugin, $lang[$plugin.':deinstall'], 'deinstall');
}
