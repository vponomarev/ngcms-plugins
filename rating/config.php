<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
// Fill configuration parameters
$skList = array();
if ($skDir = opendir(extras_dir . '/rating/tpl/skins')) {
	while ($skFile = readdir($skDir)) {
		if (!preg_match('/^\./', $skFile)) {
			$skList[$skFile] = $skFile;
		}
	}
	closedir($skDir);
}
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => 'Плагин позволяет посетителям проставлять рейтинг для новостей на сайте.'));
array_push($cfgX, array('name' => 'regonly', 'title' => 'Рейтинг только для зарегистрированных', 'descr' => '<b>Да</b> - проставлять оценки могут только зарегистрированные пользователи<br><b>Нет</b> - проставлять оценки могут все', 'type' => 'select', 'values' => array('0' => 'Нет', '1' => 'Да'), 'value' => extra_get_param($plugin, 'regonly')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки плагина</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>", 'type' => 'select', 'values' => array('0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(extra_get_param($plugin, 'localsource'))));
array_push($cfgX, array('name' => 'localskin', 'title' => "Выберите активный шаблон<br /><small>Выбранный скин будет использоваться при установке <b>Плагин</b> в предыдущем поле</small>", 'type' => 'select', 'values' => $skList, 'value' => extra_get_param($plugin, 'localskin') ? extra_get_param($plugin, 'localskin') : 'basic'));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки отображения</b>', 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
?>