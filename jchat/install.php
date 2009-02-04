<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();

$db_update = array(
 array(
  'table'  => 'jchat',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    array('action' => 'cmodify', 'name' => 'chatid', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'postdate', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'author', 'type' => 'char(50)'),
    array('action' => 'cmodify', 'name' => 'author_id', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'status', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)'),
    array('action' => 'cmodify', 'name' => 'text', 'type' => 'text'),
  )
 ),
);


if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('jchat', $db_update)) {
		plugin_mark_installed('jchat');
	}
} else {
	$text = "Плагин <b>jchat</b> позволяет вам установить AJAX based chat на вашем сайте<br /><br />Внимание! При установке плагин производит изменения в БД системы!";
	generate_install_page('jchat', $text);
}

?>