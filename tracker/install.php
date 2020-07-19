<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Install script for plugin.
// $action: possible action modes
// 	confirm		- screen for installation confirmation
//	apply		- apply installation, with handy confirmation
//	autoapply       - apply installation in automatic mode [INSTALL script]
//
pluginsLoadConfig();
function plugin_tracker_install($action) {

	global $lang;
	if ($action != 'autoapply')
		loadPluginLang('tracker', 'config', '', '', ':');
	$db_update = array(
		array(
			'table'  => 'news',
			'action' => 'cmodify',
			'key'    => 'primary key(id)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'tracker_fileid', 'type' => 'int', 'params' => 'default 0'),
				array('action' => 'cmodify', 'name' => 'tracker_magnetid', 'type' => 'int', 'params' => 'default 0'),
				array('action' => 'cmodify', 'name' => 'tracker_infohash', 'type' => 'char(40)'),
				array('action' => 'cmodify', 'name' => 'tracker_lastupdate', 'type' => 'int', 'params' => 'default 0'),
				array('action' => 'cmodify', 'name' => 'tracker_seed', 'type' => 'int', 'params' => 'default 0'),
				array('action' => 'cmodify', 'name' => 'tracker_leech', 'type' => 'int', 'params' => 'default 0'),
			)
		),
		array(
			'table'  => 'tracker_magnets',
			'action' => 'cmodify',
			'key'    => 'primary key(id)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
				array('action' => 'cmodify', 'name' => 'magnet', 'type' => 'text'),
				array('action' => 'cmodify', 'name' => 'infohash', 'type' => 'char(40)'),
			)
		),
	);
	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('tracker', $lang['tracker:desc_install']);
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('tracker', $db_update, 'install', ($action == 'autoapply') ? true : false)) {
				plugin_mark_installed('tracker');
			}
			// Now we need to set some default params
			$params = array();
			foreach ($params as $k => $v) {
				pluginSetVariable('tracker', $k, $v);
			}
			extra_commit_changes();
			break;
	}

	return true;
}
