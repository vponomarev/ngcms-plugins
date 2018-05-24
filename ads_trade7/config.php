<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => 'Плагин предназначен для отображения тизера (рекламного блока) биржи <b>trade7.ru</b>'));
array_push($cfgX, array('name' => 'id', 'title' => "Ваш идентификатор (id)", 'descr' => 'Значение параметра <b>id</b> из строки запуска скрипта', 'type' => 'input', 'value' => pluginGetVariable('ads_trade7', 'id')));
array_push($cfgX, array('name' => 'cs', 'title' => "Кодировка сайта (cs)", 'descr' => 'Значение параметра <b>cs</b> из строки запуска скрипта', 'type' => 'input', 'value' => pluginGetVariable('ads_trade7', 'cs')));
array_push($cfgX, array('name' => 'categories_2', 'title' => "Список категорий контента (categories_2) для отображения в тизере", 'descr' => 'Значение параметра <b>categories_2</b> из строки запуска скрипта', 'html_flags' => 'size=40;', 'type' => 'input', 'value' => pluginGetVariable('ads_trade7', 'categories_2')));
array_push($cfgX, array('name' => 'size', 'title' => "Размер тизера (size)", 'descr' => 'Значение параметра <b>size</b> из строки запуска скрипта', 'type' => 'input', 'value' => pluginGetVariable('ads_trade7', 'size')));
array_push($cfgX, array('name' => 'default', 'title' => "Значение по умолчанию", 'descr' => 'Что отображать вместо рекламного блока в случае, если сервер рекламной биржи недоступен', 'type' => 'text', 'html_flags' => 'cols=70 rows=3', 'value' => pluginGetVariable('ads_trade7', 'default')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки идентификации/отображения</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'timeout_sec', 'title' => "Таймаут (целая часть, в секундах)", 'descr' => 'Таймаут на время обращения к серверу', 'type' => 'input', 'value' => intval(pluginGetVariable('ads_trade7', 'timeout_sec'))));
array_push($cfgX, array('name' => 'timeout_usec', 'title' => "Таймаут (дробная часть, в милисекундах)", 'descr' => 'Таймаут на время обращения к серверу', 'type' => 'input', 'value' => intval(pluginGetVariable('ads_trade7', 'timeout_usec'))));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки таймаута</b>', 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

