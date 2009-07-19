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
function plugin_tags_install($action) {
	global $lang;

	if ($action != 'autoapply')
			loadPluginLang('tags', 'config', '', '', ':');

	// Fill DB_UPDATE configuration scheme
	$db_update = array(
		array(
			'table'		=>	'news',
			'action'	=>	'modify',
			'fields'	=>	array(
				array('action' => 'cmodify', 'name' => 'tags', 'type' => 'varchar(255)', 'params' => ''),
			)
		),
		array(
			'table'		=>	'tags',
			'action'	=>	'cmodify',
			'key'		=>	'primary key(`id`), unique key `tag` (`tag`)',
			'fields'	=>	array(
				array('action' => 'cmodify', 'name' => 'id',  'type' => 'int', 'params' => 'not null auto_increment'),
				array('action' => 'cmodify', 'name' => 'tag',  'type' => 'varchar(60)', 'params' => ''),
				array('action' => 'cmodify', 'name' => 'posts',  'type' => 'int', 'params' => 'default 1'),
			)
		),
		array(
			'table'		=>	'tags_index',
			'action'	=>	'cmodify',
			'key'		=>	'primary key(`id`), key `tagID` (`tagID`), key `newsID` (`newsID`) ',
			'fields'	=>	array(
				array('action' => 'cmodify', 'name' => 'id',  'type' => 'int', 'params' => 'not null auto_increment'),
				array('action' => 'cmodify', 'name' => 'newsID',  'type' => 'int'),
				array('action' => 'cmodify', 'name' => 'tagID',  'type' => 'varchar(60)', 'params' => ''),
			)
		),
	);

	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('tags', $lang['tags:desc_install']);
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('tags', $db_update, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('tags');
			} else {
				return false;
			}

			// Now we need to set some default params
			$params = array(
				'limit'			=> 20,
				'orderby'		=> 4,
				'ppage_limit'	=> 0,
				'ppage_orderby'	=> 1,
				'localsource'	=> 0,
				'cache'			=> 1,
				'cacheExpire'	=> 120,
			);

			foreach ($params as $k => $v) {
				extra_set_param('tags', $k, $v);
			}
			extra_commit_changes();

			break;
	}
	return true;
}

