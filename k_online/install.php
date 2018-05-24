<?php
/*
=====================================================
 K_Online v.0.1
-----------------------------------------------------
 Author: Nail' R. Davydov (ROZARD)
-----------------------------------------------------
 Jabber: ROZARD@ya.ru
 E-mail: ROZARD@list.ru
-----------------------------------------------------
 © Настоящий программист никогда не ставит 
 комментариев. То, что писалось с трудом, должно 
 пониматься с трудом. :))
-----------------------------------------------------
 Данный код защищен авторскими правами
=====================================================
*/
if (!defined('NGCMS')) {
	die ('HAL');
}
function plugin_k_online_install($action) {

	$checkVer = explode('.', substr(engineVersion, 0, 5));
	if ($checkVer['0'] == 0 && $checkVer['1'] == 9 && $checkVer['2'] = 3)
		$check = true;
	else
		$check = false;
	$db_update = array(
		array(
			'table'  => 'k_online',
			'action' => 'cmodify',
			'key'    => 'primary key(id), KEY `last_time` (`last_time`), KEY `ip` (`ip`), KEY `sess_id` (`sess_id`), KEY `users_id` (`users_id`)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
				array('action' => 'cmodify', 'name' => 'sess_id', 'type' => 'char(255)', 'params' => 'default \'\''),
				array('action' => 'cmodify', 'name' => 'last_time', 'type' => 'char(255)', 'params' => 'default \'\''),
				array('action' => 'cmodify', 'name' => 'ip', 'type' => 'varchar(15)', 'params' => 'default 0'),
				array('action' => 'cmodify', 'name' => 'users', 'type' => 'varchar(100)', 'params' => 'default \'\''),
				array('action' => 'cmodify', 'name' => 'users_id', 'type' => 'int(11)', 'params' => 'default 0'),
				array('action' => 'cmodify', 'name' => 'users_status', 'type' => 'tinyint(1)', 'params' => 'default 0'),
			)
		),
	);
	// Apply requested action
	switch ($action) {
		case 'confirm':
			if ($check)
				generate_install_page('k_online', 'Тыкай установить');
			else
				msg(array("type" => "error", "info" => "Версия CMS не соответствует допустимой<br />У вас установлена " . $checkVer['0'] . "." . $checkVer['1'] . ".<b>" . $checkVer['2'] . "</b>. Требуется 0.9.3!"));
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('k_online', $db_update, 'install', ($action == 'autoapply') ? true : false)) {
				plugin_mark_installed('k_online');
			} else {
				return false;
			}
			break;
	}

	return true;
}
