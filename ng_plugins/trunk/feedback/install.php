<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

//
// Install script for plugin.
// $action: possible action modes
// 	confirm		- screen for installation confirmation
//	apply		- apply installation, with handy confirmation
//	autoapply       - apply installation in automatic mode [INSTALL script]
//
function plugin_feedback_install($action) {
	global $lang;

	if ($action != 'autoapply')
		loadPluginLang('feedback', 'config', '', '', ':');

	// Fill DB_UPDATE configuration scheme
	$db_update = array(
	 array(
	  'table'  => 'feedback',
	  'action' => 'cmodify',
	  'key'    => 'primary key(id)',
	  'fields' => array(
	    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
	    array('action' => 'cmodify', 'name' => 'active', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'regonly', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'flags', 'type' => 'char(20)'),
	    array('action' => 'cmodify', 'name' => 'name', 'type' => 'char(40)'),
	    array('action' => 'cmodify', 'name' => 'title', 'type' => 'char(80)'),
	    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
	    array('action' => 'cmodify', 'name' => 'struct', 'type' => 'text'),
	    array('action' => 'cmodify', 'name' => 'template', 'type' => 'char(50)'),
	    array('action' => 'cmodify', 'name' => 'emails', 'type' => 'text'),
	   )
	 ),
	);

	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('feedback', $lang['feedback:text.install']);
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('feedback', $db_update, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('feedback');
			} else {
				return false;
			}
			break;
	}
	return true;
}
