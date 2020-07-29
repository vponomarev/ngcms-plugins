<?php
if (!defined('NGCMS')) die ('HAL');
function plugin_cat_description_install($action) {

	$db_create = array(
		array(
			'table'  => 'cat_description',
			'action' => 'cmodify',
			'key'    => 'primary key (`id`)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => '`id`', 'type' => 'int(11)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
				array('action' => 'cmodify', 'name' => '`catid`', 'type' => 'int(11)', 'params' => 'UNSIGNED not null'),
				array('action' => 'cmodify', 'name' => '`is_on`', 'type' => 'int(1)', 'params' => 'UNSIGNED NOT NULL DEFAULT 1'),
				array('action' => 'cmodify', 'name' => '`description`', 'type' => 'text', 'params' => 'NOT NULL DEFAULT \'\'')
			)
		)
	);
	switch ($action) {
		case 'confirm':
			generate_install_page('cat_description', 'Cейчас плагин будет установлен');
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('cat_description', $db_create, 'install', ($action == 'autoapply') ? true : false)) {
				plugin_mark_installed('cat_description');
			} else {
				return false;
			}
			break;
	}

	return true;
}
