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
plugins_load_config();
function plugin_jchat_install($action) {
	global $lang;

	if ($action != 'autoapply')
		loadPluginLang('jchat', 'config', '', '', ':');

	$db_update = array(
	 array(
	  'table'  => 'jchat',
	  'action' => 'cmodify',
	  'key'    => 'primary key(id)',
	  'fields' => array(
	    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
	    array('action' => 'cmodify', 'name' => 'chatid', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'postdate', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'author', 'type' => 'char(50)'),
	    array('action' => 'cmodify', 'name' => 'author_id', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'status', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)'),
	    array('action' => 'cmodify', 'name' => 'text', 'type' => 'text'),
	  )
	 ),
	);

	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('jchat', $lang['jchat:desc_install']);
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('jchat', $db_update, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('jchat');
			}

			// Now we need to set some default params
			$params = array(
				'access'		=> 1,
				'refresh'		=> 30,
				'history'		=> 30,
				'rate_limit'	=> 0,
				'maxidle'		=> 0,
				'maxwlen'		=> 40,
				'maxlen'		=> 500,
			);

			foreach ($params as $k => $v) {
				extra_set_param('jchat', $k, $v);
			}
			extra_commit_changes();

			break;
	}
	return true;
}



if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('jchat', $db_update)) {
		plugin_mark_installed('jchat');
	}
} else {
	$text = "Плагин <b>jchat</b> позволяет вам установить AJAX based chat на вашем сайте<br /><br />Внимание! При установке плагин производит изменения в БД системы!";
	generate_install_page('jchat', $text);
}

?>