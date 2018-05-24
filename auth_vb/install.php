<?php
// #====================================================================================#
// # Наименование плагина: auth_vb [ vBulletin auth DB module ]                         #
// # Разрешено к использованию с: NGCMS                                                 #
// # Автор: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#
// #====================================================================================#
// # Инсталл скрипт плагина                                                             #
// #====================================================================================#
plugins_load_config();
$db_update = array(
	array(
		'table'  => 'users',
		'action' => 'modify',
		'fields' => array(
			array('action' => 'cmodify', 'name' => 'vb_userid', 'type' => 'int'),
		)
	),
);
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('auth_vb', $db_update)) {
		plugin_mark_installed('auth_vb');
	}
} else {
	$text = 'Плагин <b>auth_vb</b> позволяет использовать БД форума vBulletin в качестве основоной БД для авторизации пользователей.<br />Благодаря этому плагину у вас появится возможность для авторизации пользователей<br />';
	generate_install_page('auth_vb', $text);
}
?>