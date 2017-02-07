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
function plugin_fin_smscoin_install($action) {

	global $lang;
	if ($action != 'autoapply')
		loadPluginLang('fin_smscoin', 'config', '', '', ':');
	// Fill DB_UPDATE configuration scheme
	$db_update = array(
		array(
			'table'  => 'fin_smscoin_history',
			'key'    => 'primary key(id)',
			'action' => 'cmodify',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'id', 'type' => 'int not null auto_increment'),
				array('action' => 'cmodify', 'name' => 'success', 'type' => 'int', 'params' => 'default "0"'),
				array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
				array('action' => 'cmodify', 'name' => 'purse', 'type' => 'int'),
				array('action' => 'cmodify', 'name' => 'order_id', 'type' => 'int'),
				array('action' => 'cmodify', 'name' => 'amount', 'type' => 'float'),
				array('action' => 'cmodify', 'name' => 'clear_amount', 'type' => 'int'),
				array('action' => 'cmodify', 'name' => 'inv', 'type' => 'bigint'),
				array('action' => 'cmodify', 'name' => 'phone', 'type' => 'char(32)'),
				array('action' => 'cmodify', 'name' => 'sign_v2', 'type' => 'char(32)'),
				array('action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)'),
				array('action' => 'cmodify', 'name' => 'userid', 'type' => 'int'),
				array('action' => 'cmodify', 'name' => 'sum', 'type' => 'float'),
				array('action' => 'cmodify', 'name' => 'trid', 'type' => 'int'),
			)
		),
		array(
			'table'  => 'fin_smscoin_transactions',
			'key'    => 'primary key(id)',
			'action' => 'cmodify',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'id', 'type' => 'int not null auto_increment'),
				array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
				array('action' => 'cmodify', 'name' => 'userid', 'type' => 'int'),
				array('action' => 'cmodify', 'name' => 'username', 'type' => 'char(100)'),
				array('action' => 'cmodify', 'name' => 'amount', 'type' => 'float'),
				array('action' => 'cmodify', 'name' => 'profit', 'type' => 'float'),
			)
		),
	);
	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('fin_smscoin', $lang['fin_smscoin:desc_install']);
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('fin_smscoin', $db_update, 'install', ($action == 'autoapply') ? true : false)) {
				plugin_mark_installed('fin_smscoin');
			} else {
				return false;
			}
			break;
	}

	return true;
}
