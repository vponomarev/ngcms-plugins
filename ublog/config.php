<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
$personalCount = intval(pluginGetVariable($plugin, 'personalCount'));
if (($personalCount < 2) || ($personalCount > 100))
	$personalCount = 10;
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => 'ѕлагин показывает новости конкретного пользовател€'));
$cfgX = array();
array_push($cfgX, array('name' => 'replaceCount', 'title' => "«амен€ть значение переменной {news} на активную ссылку на блог пользовател€?<br /><small><b>ƒа</b> - Ѕудет замен€тьс€ значение переменной<br /><b>Ќет</b> - значение переменной замен€тьс€ не будет</small>", 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => intval(pluginGetVariable($plugin, 'replaceCount'))));
array_push($cfg, array('mode' => 'group', 'title' => '<b>—траница просмотра профил€ пользовател€</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'personalPages', 'title' => "¬ключить персональную ленту дл€ новостей пользователей<br /><small><b>ƒа</b> - ѕо адресу /plugin/ublog/?id=<b>ID_пользовател€</b> будет доступен список его новостей<br /><b>Ќет</b> - список новостей пользовател€ выводитьс€ не будет</small>", 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => intval(pluginGetVariable($plugin, 'personalPages'))));
array_push($cfgX, array('name' => 'personalCount', 'title' => " ол-во новостей, отображаемых на странице<br/><small>«начение по умолчанию: <b>10</b></small>", 'type' => 'input', 'value' => $personalCount));
array_push($cfg, array('mode' => 'group', 'title' => '<b>—обственна€ страница с лентой новостей пользовател€</b>', 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

