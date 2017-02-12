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
array_push($cfg, array('descr' => 'ѕлагин отображает "календарь" - отображает данные о новост€х по выбранному мес€цу подсвечива€ дни когда были размещены новостиѕри клике на день будут отображатьс€ новости за этот день'));
$cfgX = array();
array_push($cfgX, array('name' => 'mode', 'title' => "¬ каком режиме генерируетс€ вывод плагина<br /><small><b>јвтоматически</b> - при включении плагина автоматически генерируетс€ блок {plugin_comments}<br /><b>TWIG</b> - вывод плагина генерируетс€ только через TWIG функцию <b>callPlugin()</b></small>", 'type' => 'select', 'values' => array('0' => 'јвтоматически', '1' => 'TWIG'), 'value' => intval(pluginGetVariable($plugin, 'mode'))));
array_push($cfg, array('mode' => 'group', 'title' => '<b>–ежим запуска</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "¬ыберите каталог из которого плагин будет брать шаблоны дл€ отображени€<br /><small><b>Ўаблон сайта</b> - плагин будет пытатьс€ вз€ть шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут вз€ты из собственного каталога плагина<br /><b>ѕлагин</b> - шаблоны будут братьс€ из собственного каталога плагина</small>", 'type' => 'select', 'values' => array('0' => 'Ўаблон сайта', '1' => 'ѕлагин'), 'value' => intval(pluginGetVariable($plugin, 'localsource'))));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Ќастройки отображени€</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "»спользовать кеширование данных<br /><small><b>ƒа</b> - кеширование используетс€<br /><b>Ќет</b> - кеширование не используетс€</small>", 'type' => 'select', 'values' => array('1' => 'ƒа', '0' => 'Ќет'), 'value' => intval(pluginGetVariable($plugin, 'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "ѕериод обновлени€ кеша<br /><small>(через сколько секунд происходит обновление кеша. «начение по умолчанию: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(pluginGetVariable($plugin, 'cacheExpire')) ? pluginGetVariable($plugin, 'cacheExpire') : '60'));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Ќастройки кешировани€</b>', 'entries' => $cfgX));
// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
?>