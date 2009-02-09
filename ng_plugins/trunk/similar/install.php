<?php

//
// Configuration file for plugin
//

plugins_load_config();
LoadPluginLang($plugin, 'main');


$db_update = array(
	array(
		'table'  => 'news',
		'action' => 'cmodify',
		'fields' => array(
		// Status of similar news:
		// 0 - BROKEN (data should be rebuilded)
		// 1 - No SIMILAR data
		// 2 - Have SIMILAR data
		array('action' => 'cmodify', 'name' => 'similar_status', 'type' => 'int', 'params' => "default 0"),
		)
	),
	array(
		'table'		=>	'similar_index',
		'action'	=>	'cmodify',
		'key'		=>	'primary key(`id`), key `newsID` (`newsID`) ',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'id',  'type' => 'int', 'params' => 'not null auto_increment'),
			array('action' => 'cmodify', 'name' => 'dimension',  'type' => 'int', 'params' => 'default 0'),
			array('action' => 'cmodify', 'name' => 'newsID',  'type' => 'int'),
			array('action' => 'cmodify', 'name' => 'refNewsID',  'type' => 'int'),
			array('action' => 'cmodify', 'name' => 'refNewsQuantaty',  'type' => 'int'),
			array('action' => 'cmodify', 'name' => 'refNewsTitle',  'type' => 'varchar(120)', 'params' => ''),
			array('action' => 'cmodify', 'name' => 'refNewsDate',  'type' => 'int'),
		)
	),
);


if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install($plugin, $db_update)) {
		plugin_mark_installed($plugin);
	}
} else {
	$text = 'ѕлагин <b>similar</b> позвол€ет создавать блоки похожих (по различным пол€м) новостей.<br />Ќа текущий момент поддерживаетс€ только анализ тегов.';
	generate_install_page($plugin, $text);
}

?>