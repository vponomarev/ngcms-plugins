<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Voting plugin deinstaller
//
plugins_load_config();
LoadPluginLang('voting', 'install');
$db_update = array(
	// array(
	//  'table'  => 'vote',
	//  'action' => 'drop',
	// ),
	// array(
	//  'table'  => 'voteline',
	//  'action' => 'drop',
	// ),
);
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('voting', $db_update, 'deinstall')) {
		plugin_mark_deinstalled('voting');
	}
} else {
	$text = 'Внимание! Удаление плагина приведёт к удалению всех созданных на сайте опросов!<br><br>';
	generate_install_page('voting', $text, 'deinstall');
}
?>