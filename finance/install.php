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
function plugin_finance_install($action) {
	global $lang;

	if ($action != 'autoapply')
			loadPluginLang('finance', 'config', '', '', ':');

	// Fill DB_UPDATE configuration scheme
	$db_update = array(
	 array(
	  'table'  => 'users',
	  'action' => 'modify',
	  'fields' => array(
	    array('action' => 'cmodify', 'name' => 'balance',  'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'balance1', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'balance2', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'balance3', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'balance4', 'type' => 'int', 'params' => 'default 0'),
	  )
	 ),
	 array(
	  'table'  => 'news',
	  'action' => 'cmodify',
	  'fields' => array(
	    array('action' => 'cmodify', 'name' => 'fin_price', 'type' => 'char(40)', 'params' => "default ''"),
	  )
	 ),
	 array(
	  'table'  => 'balance_manager',
	  'action' => 'cmodify',
	  'fields' => array(
	    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'monetary', 'type' => 'int', 'params' => 'default "1"'),
	    array('action' => 'cmodify', 'name' => 'type', 'type' => 'char(30)'),
	    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
	  )
	 ),
	 array(
	  'table'  => 'subscribe_manager',
	  'action' => 'cmodify',
	  'key'    => 'primary key(id)',
	  'fields' => array(
	    // ID строки
	    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
	    // user_id = код пользователя для кого создана подписка
	    array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int'),
	    // special_access_type = признак, что доступ идёт не по БД а по специальным типам (скажем, SMS или какие-то нотификации)
	    array('action' => 'cmodify', 'name' => 'special_access_type', 'type' => 'int'),
	    // ID группы по которой идёт доступ. если 0 - не используется
	    array('action' => 'cmodify', 'name' => 'access_group_id', 'type' => 'int'),
	    // ID элемента по которому идёт доступ. если 0 - не используется.
	    array('action' => 'cmodify', 'name' => 'access_element_id', 'type' => 'int'),
	    // дата активации подписки
	    array('action' => 'cmodify', 'name' => 'subscription_date', 'type' => 'datetime'),
	    // срок действия подписки
	    array('action' => 'cmodify', 'name' => 'expiration_date', 'type' => 'datetime'),
	   )
	 ),
	 array(
	  'table'  => 'finance_history',
	  'action' => 'cmodify',
	  'key'    => 'primary key(id)',
	  'fields' => array(
	    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
	    array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
	    // Тип операции: 1 - приход, 2 - расход
	    array('action' => 'cmodify', 'name' => 'operation_type', 'type' => 'int'),
	    // Описываем изменения балансов
	    array('action' => 'cmodify', 'name' => 'balance',  'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'balance1', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'balance2', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'balance3', 'type' => 'int', 'params' => 'default 0'),
	    array('action' => 'cmodify', 'name' => 'balance4', 'type' => 'int', 'params' => 'default 0'),
	    // В случае списания - параметры "куда"
	    // subscribe_ref - ссылка на строчку таблицы subscribe_manager
	    array('action' => 'cmodify', 'name' => 'subscribe_ref', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'special_access_type', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'access_group_id', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'access_element_id', 'type' => 'int'),
	    array('action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)'),
	    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
	   )
	 ),
	// array(
	//  'table'  => 'pricelist',
	//  'action' => 'cmodify',
	//  'key'    => 'primary key(id)',
	//  'fields' => array(
	//    // ID строки
	//    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
	//    // ID группы по которой идёт доступ. если 0 - не используется
	//    array('action' => 'cmodify', 'name' => 'access_group_id', 'type' => 'int'),
	//    // ID элемента по которому идёт доступ. если 0 - не используется.
	//    array('action' => 'cmodify', 'name' => 'access_element_id', 'type' => 'int'),
	//    // Тип специального элемента. Сперва снимаются поинты с доп. балансов этого типа а только потом - деньги
	//    array('action' => 'cmodify', 'name' => 'type', 'type' => 'char(30)'),
	//    // Цена в поинтах данного типа (при снятии с баланса)
	//    array('action' => 'cmodify', 'name' => 'tprice', 'type' => 'int'),
	//    // Цена в валюте для данного элемента
	//    array('action' => 'cmodify', 'name' => 'price', 'type' => 'int'),
	//    // Описание
	//    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
	//   )
	//  )
	);



	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('finance', $lang['finance:desc_install']);
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('finance', $db_update, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('finance');
			} else {
				return false;
			}

			// Now we need to set some default params
			$params = array(
				'syscurrency'	=> 'RUR',
			);

			foreach ($params as $k => $v) {
				extra_set_param('finance', $k, $v);
			}
			extra_commit_changes();

			break;
	}
	return true;
}

