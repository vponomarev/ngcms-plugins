<?php
//
// Configuration file for plugin
//
pluginsLoadConfig();
LoadPluginLang('fin_sms_rb', 'config');
$db_update = array(
	array(
		'table'  => 'smsrb_price',
		'action' => 'cmodify',
		'key'    => 'primary key(id)',
		'fields' => array(
			// ID строки
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
			array('action' => 'cmodify', 'name' => 'country', 'type' => 'char(60)'),
			array('action' => 'cmodify', 'name' => 'operator', 'type' => 'char(60)'),
			array('action' => 'cmodify', 'name' => 'num', 'type' => 'char(20)'),
			array('action' => 'cmodify', 'name' => 'cost', 'type' => 'float'),
			array('action' => 'cmodify', 'name' => 'partnercost', 'type' => 'float'),
		)
	),
	array(
		'table'  => 'smsrb_incoming',
		'action' => 'cmodify',
		'key'    => 'primary key(id)',
		'fields' => array(
			// ID строки
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
			array('action' => 'cmodify', 'name' => 'smsid', 'type' => 'char(25)'),
			array('action' => 'cmodify', 'name' => 'operator', 'type' => 'char(60)'),
			array('action' => 'cmodify', 'name' => 'num', 'type' => 'char(20)'),
			array('action' => 'cmodify', 'name' => 'cost', 'type' => 'float'),
			array('action' => 'cmodify', 'name' => 'msg', 'type' => 'char(255)'),
			array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
			array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int'),
			array('action' => 'cmodify', 'name' => 'actdt', 'type' => 'datetime'),
			// Тип сервиса:
			// 0 - запрос пароля
			array('action' => 'cmodify', 'name' => 'service_type', 'type' => 'int', 'params' => 'default 0'),
			array('action' => 'cmodify', 'name' => 'scode', 'type' => 'char(30)'),
			// Ответное сообщение
			array('action' => 'cmodify', 'name' => 'answer', 'type' => 'char(255)'),
		)
	),
	array(
		'table'  => 'smsrb_history',
		'action' => 'cmodify',
		'key'    => 'primary key(id)',
		'fields' => array(
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
			array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int'),
			array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
			array('action' => 'cmodify', 'name' => 'success', 'type' => 'int', params => 'default 0'),
			array('action' => 'cmodify', 'name' => 'code_id', 'type' => 'int', params => 'default 0'),
			array('action' => 'cmodify', 'name' => 'action', 'type' => 'char(20)'),
			array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
		)
	),
);
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('fin_sms_rb', $db_update)) {
		plugin_mark_installed('fin_sms_rb');
	}
} else {
	$text = "Плагин <b>fin_sms_rb</b> позволяет вводить на ваш сайт средства, полученные от компании-SMS партнёра <a href='http://russianbilling.com/' target='_blank'>RussianBilling.com</a><br/><br/>При установке Вам следует учесть что:<br/><b>1.</b> Перед установкой плагина Вам необходимо установить плагин <b>finance</b> - без него ничего работать не будет.<br/><b>2.</b> Плагин при установке вносит изменения в БД.";
	generate_install_page('fin_sms_rb', $text);
}
?>