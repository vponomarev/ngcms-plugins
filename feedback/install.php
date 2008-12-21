<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();
loadPluginLang('feedback', 'config', '', '', ':');

$db_update = array(
 array(
  'table'  => 'feedback',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    array('action' => 'cmodify', 'name' => 'active', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'regonly', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'flags', 'type' => 'char(20)'),
    array('action' => 'cmodify', 'name' => 'name', 'type' => 'char(40)'),
    array('action' => 'cmodify', 'name' => 'title', 'type' => 'char(80)'),
    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
    array('action' => 'cmodify', 'name' => 'struct', 'type' => 'text'),
    array('action' => 'cmodify', 'name' => 'template', 'type' => 'char(50)'),
    array('action' => 'cmodify', 'name' => 'emails', 'type' => 'text'),
   )
 ),
);


if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('feedback', $db_update)) {
		plugin_mark_installed('feedback');
	}
} else {
	$text = $lang['feedback:text.install'];
	generate_install_page('feedback', $text);
}
