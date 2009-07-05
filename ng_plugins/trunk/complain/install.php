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
function plugin_complain_install($action) {
	global $lang;

	if ($action != 'autoapply')
		loadPluginLang('complain', 'config', '', '', ':');

	$db_update = array(
	 array(
	  'table'  => 'complain',
	  'action' => 'cmodify',
	  'key'    => 'primary key(id)',
	  'fields' => array(
	    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
	    array('action' => 'cmodify', 'name' => 'complete', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'status', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'publisher_id', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'publisher_ip', 'type' => 'char(15)'),
	    array('action' => 'cmodify', 'name' => 'publisher_mail', 'type' => 'char(80)'),
	    array('action' => 'cmodify', 'name' => 'owner_id', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'author_id', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'date', 'type' => 'datetime'),
	    array('action' => 'cmodify', 'name' => 'rdate', 'type' => 'datetime'),
	    array('action' => 'cmodify', 'name' => 'ds_id', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'entry_id', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'error_code', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'error_text', 'type' => 'text'),
	    array('action' => 'cmodify', 'name' => 'flags', 'type' => 'char(20)'),
	   )
	 ),
	);

	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('complain', $lang['complain:desc_install']);
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('complain', $db_update, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('complain');
			}

			// Now we need to set some default params
			$params = array(
				'localsource'	=> 1,
				'extform'		=> 1,
				'errlist'		=> "1|Неверная ссылка\n2|Ссылка удалена\n3|Другая ошибка",
				'inform_author'	=> 1,
				'inform_admin'	=> 2,
				'author_multi'	=> 1,
				'inform_reporter'		=> 1,
				'allow_unreg'			=> 1,
				'allow_unreg_inform'	=> 0,
				'allow_text'	=> 1,
				'inform_admins'	=> 1,
			);

			foreach ($params as $k => $v) {
				extra_set_param('complain', $k, $v);
			}
			extra_commit_changes();

			break;
	}
	return true;
}

