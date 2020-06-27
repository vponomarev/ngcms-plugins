<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();

$db_update = array(
	array(
		'table'		=>	'zboard',
		'action'	=>	'drop',
	),
	array(
		'table'		=>	'zboard_view',
		'action'	=>	'drop',
	),
	array(
		'table'		=>	'zboard_cat',
		'action'	=>	'drop',
	),
	array(
		'table'		=>	'zboard_images',
		'action'	=>	'drop',
	),
    array(
		'table'		=>	'zboard_pay_order',
		'action'	=>	'drop',
	),
    array(
		'table'		=>	'zboard_pay_price',
		'action'	=>	'drop',
	),
);

if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
		plugin_mark_deinstalled($plugin);
	}
} else {
	generate_install_page($plugin, 'Удаление плагина', 'deinstall');
}

?>