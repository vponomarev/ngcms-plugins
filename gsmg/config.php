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
array_push($cfg, array('descr' => 'Плагин генерации XML карты сайта для поисковой системы Google'));
array_push($cfgX, array('name' => 'main', 'title' => "Добавлять головную страницу в карту сайта", 'descr' => "<b>Да</b> - страница будет добавляться в карту сайта<br /><b>Нет</b> - страница не будет добавляться в карту сайта", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'main'))));
array_push($cfgX, array('name' => 'main_pr', 'title' => "Приоритет головной страницы", 'descr' => 'значение от <b>0.0</b> до <b>1.0</b>', 'type' => 'input', 'value' => (extra_get_param($plugin,'main_pr') == '')?'1.0':extra_get_param($plugin,'main_pr')));
array_push($cfgX, array('name' => 'mainp', 'title' => "Добавлять постраничку головной страницы в карту сайта", 'descr' => "<b>Да</b> - страница будет добавляться в карту сайта<br /><b>Нет</b> - страница не будет добавляться в карту сайта", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'mainp'))));
array_push($cfgX, array('name' => 'mainp_pr', 'title' => "Приоритет постранички головной страницы", 'descr' => 'значение от <b>0.0</b> до <b>1.0</b>', 'type' => 'input', 'value' => (extra_get_param($plugin,'mainp_pr') == '')?'0.5':extra_get_param($plugin,'mainp_pr')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки для головной страницы сайта</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cat', 'title' => "Добавлять страницы категорий в карту сайта", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'cat'))));
array_push($cfgX, array('name' => 'cat_pr', 'title' => "Приоритет страниц категорий", 'type' => 'input', 'value' => (extra_get_param($plugin,'cat_pr') == '')?'0.5':extra_get_param($plugin,'cat_pr')));
array_push($cfgX, array('name' => 'catp', 'title' => "Добавлять постраничку страниц категорий в карту сайта", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'catp'))));
array_push($cfgX, array('name' => 'catp_pr', 'title' => "Приоритет постранички категорий", 'type' => 'input', 'value' => (extra_get_param($plugin,'catp_pr') == '')?'0.5':extra_get_param($plugin,'catp_pr')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки для страниц категорий</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'news', 'title' => "Добавлять страницы новостей в карту сайта", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'news'))));
array_push($cfgX, array('name' => 'news_pr', 'title' => "Приоритет страниц новостей", 'type' => 'input', 'value' => (extra_get_param($plugin,'news_pr') == '')?'0.3':extra_get_param($plugin,'news_pr')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки для страниц новостей</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'static', 'title' => "Добавлять статические страницы в карту сайта", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'static'))));
array_push($cfgX, array('name' => 'static_pr', 'title' => "Приоритет статических страниц", 'type' => 'input', 'value' => (extra_get_param($plugin,'static_pr') == '')?'0.3':extra_get_param($plugin,'static_pr')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки для статических страниц</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Использовать кеширование карты сайта<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => 'Период обновления кеша (в секундах)<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>10800</b>, т.е. 3 часа)', 'type' => 'input', 'value' => intval(extra_get_param($plugin,'cacheExpire'))?extra_get_param($plugin,'cacheExpire'):'10800'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки кеширования</b>', 'entries' => $cfgX));


// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>