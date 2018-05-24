<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
plugins_load_config();
LoadPluginLang('finance', 'config');
$db_update = array(
	array(
		'table'  => 'news',
		'action' => 'cmodify',
		'fields' => array(
			array('action' => 'cmodify', 'name' => 'ur_1count', 'type' => 'int', 'params' => "default 0"),
			array('action' => 'cmodify', 'name' => 'ur_1value', 'type' => 'int', 'params' => "default 0"),
		)
	),
	array(
		'table'  => 'urating',
		'action' => 'cmodify',
		'fields' => array(
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'int'),
			array('action' => 'cmodify', 'name' => 'dtype', 'type' => 'int', 'params' => 'default 0'),
			array('action' => 'cmodify', 'name' => 'dstorageid', 'type' => 'int', 'params' => 'default 0'),
			array('action' => 'cmodify', 'name' => 'did', 'type' => 'int', 'params' => 'default 0'),
			array('action' => 'cmodify', 'name' => 'userid', 'type' => 'int', 'params' => 'default 0'),
			array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
			array('action' => 'cmodify', 'name' => 'value', 'type' => 'int', 'params' => 'default 0'),
			array('action' => 'cmodify', 'name' => 'text', 'type' => 'char(30)'),
		)
	),
);
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('unirating', $db_update)) {
		plugin_mark_installed('unirating');
	}
} else {
	$text = "Плагин <b>unirating</b> позволяет проставлять рейтинг для любых информационных элементов - новостей, статических страниц, самих пользователей,...<br /><br />Внимание! При установке плагин производит изменения в БД системы!";
	generate_install_page('unirating', $text);
}

