<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
pluginsLoadConfig();
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => 'Плагин отображает "календарь" - отображает данные о новостях по выбранному месяцу подсвечивая дни когда были размещены новостиПри клике на день будут отображаться новости за этот день'));
$cfgX = array();
array_push($cfgX, array('name' => 'mode', 'title' => "В каком режиме генерируется вывод плагина<br /><small><b>Автоматически</b> - при включении плагина автоматически генерируется блок {plugin_comments}<br /><b>TWIG</b> - вывод плагина генерируется только через TWIG функцию <b>callPlugin()</b></small>", 'type' => 'select', 'values' => array('0' => 'Автоматически', '1' => 'TWIG'), 'value' => intval(pluginGetVariable($plugin, 'mode'))));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Режим запуска</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>", 'type' => 'select', 'values' => array('0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(pluginGetVariable($plugin, 'localsource'))));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки отображения</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Использовать кеширование данных<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>", 'type' => 'select', 'values' => array('1' => 'Да', '0' => 'Нет'), 'value' => intval(pluginGetVariable($plugin, 'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "Период обновления кеша<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(pluginGetVariable($plugin, 'cacheExpire')) ? pluginGetVariable($plugin, 'cacheExpire') : '60'));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки кеширования</b>', 'entries' => $cfgX));
// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
?>