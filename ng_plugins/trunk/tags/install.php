<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();
LoadPluginLang($plugin, 'main');


$db_update = array(
	array(
		'table'		=>	'news',
		'action'	=>	'modify',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'tags', 'type' => 'varchar(255)', 'params' => ''),
		)
	),
	array(
		'table'		=>	'tags',
		'action'	=>	'cmodify',
		'key'		=>	'primary key(`id`), unique key `tag` (`tag`)',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'id',  'type' => 'int', 'params' => 'not null auto_increment'),
			array('action' => 'cmodify', 'name' => 'tag',  'type' => 'varchar(60)', 'params' => ''),
			array('action' => 'cmodify', 'name' => 'posts',  'type' => 'int', 'params' => 'default 1'),
		)
	),
	array(
		'table'		=>	'tags_index',
		'action'	=>	'cmodify',
		'key'		=>	'primary key(`id`), key `tagID` (`tagID`), key `newsID` (`newsID`) ',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'id',  'type' => 'int', 'params' => 'not null auto_increment'),
			array('action' => 'cmodify', 'name' => 'newsID',  'type' => 'int'),
			array('action' => 'cmodify', 'name' => 'tagID',  'type' => 'varchar(60)', 'params' => ''),
		)                                                    	
	),
);


if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update)) {
		plugin_mark_installed($plugin);
	}	
} else {
	$text = 'Плагин <b>tags</b> позволяет реализовать функционал "облако тегов"<br />';
	generate_install_page($plugin, $text);
}

?>