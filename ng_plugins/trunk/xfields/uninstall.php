<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();
LoadPluginLang('xfields', 'config');

$db_update = array(
 array(
  'table'  => 'news',
  'action' => 'modify',
  'fields' => array(
    array('action' => 'drop', 'name' => 'xfields',  'type' => 'text'),
  )
 ),
 array(
  'table'  => 'categories',
  'action' => 'modify',
  'fields' => array(
    array('action' => 'drop', 'name' => 'xf_group',  'type' => 'text'),
  )
 ),
 array(
  'table'  => 'users',
  'action' => 'modify',
  'fields' => array(
    array('action' => 'drop', 'name' => 'xfields',  'type' => 'text'),
  )
 )
);

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('xfields', $db_update, 'deinstall')) {
		plugin_mark_deinstalled('xfields');
	}	
} else {
	$text = $lang['xfields_desc_uninstall'];
	generate_install_page('xfields', $text, 'deinstall');
}

?>