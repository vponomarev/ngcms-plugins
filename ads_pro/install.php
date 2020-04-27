<?php
if (!defined('NGCMS')) die ('HAL');
function plugin_ads_pro_install($action) {

	$db_create = array(
		array(
			'table'  => 'ads_pro',
			'action' => 'cmodify',
			'key'    => 'primary key (id)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
				array('action' => 'cmodify', 'name' => 'ads_blok', 'type' => 'text', 'params' => 'NOT NULL')
			)
		)
	);
	switch ($action) {
		case 'confirm':
			generate_install_page('ads_pro', 'Cейчас плагин будет установлен');
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('ads_pro', $db_create, 'install', ($action == 'autoapply') ? true : false)) {
				plugin_mark_installed('ads_pro');
			} else {
				return false;
			}
			break;
	}

	return true;
}
