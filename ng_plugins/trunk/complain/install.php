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
  'table'  => 'complain',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    array('action' => 'cmodify', 'name' => 'complete', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'status', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'publisher_id', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'publisher_ip', 'type' => 'char(15)'),
    array('action' => 'cmodify', 'name' => 'publisher_mail', 'type' => 'char(80)'),
    array('action' => 'cmodify', 'name' => 'owner_id', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'author_id', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'date', 'type' => 'datetime'),
    array('action' => 'cmodify', 'name' => 'rdate', 'type' => 'datetime'),
    array('action' => 'cmodify', 'name' => 'ds_id', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'entry_id', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'error_code', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'flags', 'type' => 'char(20)'),
   )
 ),
);


if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('complain', $db_update)) {
		plugin_mark_installed('complain');
	}
} else {
	$text = "Плагин <b>complain</b> в первую очередь нужен для сайтов, занимающимся размещением какой-либо информации или ссылок.<br/>Он позволяет решать проблему \"мёртвых ссылок\" - теперь любой посетитель обнаружив проблему сможет одним нажатием сообщить автору новости о наличии той или иной проблемы с его новостью.";
	generate_install_page('complain', $text);
}

?>