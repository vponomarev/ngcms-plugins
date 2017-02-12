<?php
# protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');
global $lang;
$db_update = array(
	array(
		'table'  => 'users',
		'action' => 'modify',
		'fields' => array(
			array('action' => 'drop', 'name' => 'provider', 'type' => 'varchar(255)', 'params' => 'DEFAULT \'\''),
			array('action' => 'drop', 'name' => 'social_id', 'type' => 'text', 'params' => 'DEFAULT \'\''),
			array('action' => 'drop', 'name' => 'social_page', 'type' => 'text', 'params' => 'DEFAULT \'\''),
			array('action' => 'drop', 'name' => 'sex', 'type' => 'varchar(255)', 'params' => 'DEFAULT \'\''),
			array('action' => 'drop', 'name' => 'birthday', 'type' => 'varchar(255)', 'params' => 'DEFAULT \'\'')
		)
	),
);
if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install('auth_social', $db_update, 'deinstall')) {
		plugin_mark_deinstalled('auth_social');
	}
} else {
	generate_install_page('auth_social', 'You are shure?', 'deinstall');
}