<?php

if (!defined('NGCMS'))die ('Galaxy in danger');

function plugin_auth_loginza_install($action) {
	$db_create = array(
		array(
			'table' => 'users',
			'action' => 'cmodify',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'loginza_id', 'type' => 'varchar(255)', 'params' => 'DEFAULT \'\''),
			)
		)
	);
	
	switch ($action) {
		case 'confirm':generate_install_page('auth_loginza', 'GoGoGo!!');break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('auth_loginza', $db_create, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('auth_loginza');
			} else {
				return false;
			}
			break;
	}
	return true;
}