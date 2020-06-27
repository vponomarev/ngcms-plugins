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
        'action' => 'cmodify',
        'key'    => 'primary key(id), KEY `last_time` (`last_time`), KEY `ip` (`ip`), KEY `sess_id` (`sess_id`), KEY `users_id` (`users_id`)',
        'fields' => array(
            array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
            array('action' => 'cmodify', 'name' => 'sess_id', 'type' => 'char(255)', 'params' => 'default \'\''),
            array('action' => 'cmodify', 'name' => 'last_time', 'type' => 'char(255)', 'params' => 'default \'\''),
            array('action' => 'cmodify', 'name' => 'ip', 'type' => 'varchar(15)', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'users', 'type' => 'varchar(100)', 'params' => 'default \'\''),
            array('action' => 'cmodify', 'name' => 'users_id', 'type' => 'int(11)', 'params' => 'default 0'),
            array('action' => 'cmodify', 'name' => 'users_status', 'type' => 'tinyint(1)', 'params' => 'default 0'),
            )),
    );

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install($plugin, $db_update)) {
        $cron->unregisterTask('site_stats');
        $cron->registerTask('site_stats', 'run', '0', '0,23', '*', '*', '*');
		plugin_mark_installed($plugin);
	}
} else {
	generate_install_page($plugin, $lang[$plugin.':install']);
}
