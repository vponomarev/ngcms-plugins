<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Voting plugin installer
//

plugins_load_config();

LoadPluginLang('voting', 'install');

$db_update = array(
 array(
  'table'  => 'vote',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    array('action' => 'cmodify', 'name' => 'newsid', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'name', 'type' => 'char(50)'),
    array('action' => 'cmodify', 'name' => 'descr', 'type' => 'text'),
    array('action' => 'cmodify', 'name' => 'active', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'closed', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'regonly', 'type' => 'int', 'params' => 'default 0'),
  )
 ),
 array(
  'table'  => 'voteline',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    array('action' => 'cmodify', 'name' => 'voteid', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'position', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'name', 'type' => 'char(50)'),
    array('action' => 'cmodify', 'name' => 'cnt', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'active', 'type' => 'int', 'params' => 'default 1'),
  )
 ),
 array(
  'table'  => 'votestat',
  'action' => 'cmodify',
  'key'	   => 'primary key(id)',
  'fields' => array(
   array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
   array('action' => 'cmodify', 'name' => 'userid', 'type' => 'int', 'params' => 'default 0'),
   array('action' => 'cmodify', 'name' => 'voteid', 'type' => 'int', 'params' => 'default 0'),
   array('action' => 'cmodify', 'name' => 'voteline', 'type' => 'int', 'params' => 'default 0'),
   array('action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)'),
   array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
  ),
 ),
);

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('voting', $db_update)) {
		plugin_mark_installed('voting');
	}	
} else {
	$text = 'ѕлагин <b>voting</b> позвол€ет устанавливать на сайте опросы.<br><br>';
	generate_install_page('voting', $text);
}

?>