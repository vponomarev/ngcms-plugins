<?php

// #====================================================================================#
// # Наименование плагина: auth_punBB [ punBB auth DB module ]                          #
// # Разрешено к использованию с: Next Generation CMS                                   #
// # Автор: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#

// #====================================================================================#
// # Инсталл скрипт плагина                                                             #
// #====================================================================================#

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

plugins_load_config();

$db_update = array(
 array(
  'table'  => 'users',
  'action' => 'modify',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'punbb_userid', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'punbb_newpw', 'type' => 'char(40)'),
  )
 ),
);

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('auth_punbb', $db_update)) {
		plugin_mark_installed('auth_punbb');
	}	
} else {
	$text = 'Плагин <b>auth_punbb</b> позволяет использовать БД форума punBB в качестве основоной БД для авторизации пользователей.<br />Благодаря этому плагину у вас появится возможность для авторизации пользователей<br />';
	generate_install_page('auth_punbb', $text);
}

