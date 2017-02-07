<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
plugins_load_config();
$db_update = array(
	array(
		'table'  => 'forum_complaints',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_attach',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_thank',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_news',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_forums',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_topics',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_posts',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_online',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_group',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_moderators',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_permission',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_subscriptions',
		'action' => 'drop',
	),
	array(
		'table'  => 'forum_reputation',
		'action' => 'drop',
	),
	array(
		'table'  => 'news',
		'action' => 'modify',
		'fields' => array(
			array('action' => 'drop', 'name' => 'tid'),
		)
	),
	array(
		'table'  => 'users',
		'action' => 'modify',
		'fields' => array(
			array('action' => 'drop', 'name' => 'timezone'),
			array('action' => 'drop', 'name' => 'signature'),
			array('action' => 'drop', 'name' => 'int_post'),
			array('action' => 'drop', 'name' => 'l_post'),
			array('action' => 'drop', 'name' => 'reputation'),
			array('action' => 'drop', 'name' => 'int_thank'),
		)
	),
);
if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
		plugin_mark_deinstalled($plugin);
	}
} else {
	generate_install_page($plugin, 'Тестовое удаление', 'deinstall');
}
?>