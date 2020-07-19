<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
pluginsLoadConfig();
$personalCount = intval(pluginGetVariable($plugin, 'personalCount'));
if (($personalCount < 2) || ($personalCount > 100))
	$personalCount = 10;
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => 'Плагин показывает новости конкретного пользователя'));
$cfgX = array();
array_push($cfgX, array('name' => 'replaceCount', 'title' => "Заменять значение переменной {news} на активную ссылку на блог пользователя?<br /><small><b>Да</b> - Будет заменяться значение переменной<br /><b>Нет</b> - значение переменной заменяться не будет</small>", 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => intval(pluginGetVariable($plugin, 'replaceCount'))));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Страница просмотра профиля пользователя</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'personalPages', 'title' => "Включить персональную ленту для новостей пользователей<br /><small><b>Да</b> - По адресу /plugin/ublog/?id=<b>ID_пользователя</b> будет доступен список его новостей<br /><b>Нет</b> - список новостей пользователя выводиться не будет</small>", 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => intval(pluginGetVariable($plugin, 'personalPages'))));
array_push($cfgX, array('name' => 'personalCount', 'title' => "Кол-во новостей, отображаемых на странице<br/><small>Значение по умолчанию: <b>10</b></small>", 'type' => 'input', 'value' => $personalCount));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Собственная страница с лентой новостей пользователя</b>', 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

