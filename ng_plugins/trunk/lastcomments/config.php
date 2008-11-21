<?php

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();


// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg,  array('descr' => 'Плагин отображает последние комментарии, оставленные на новости сайта.'));
array_push($cfgX, array('name' => 'sidepanel', 'title' => 'Включить генерацию боковой панели', 'descr' => '<b>Да</b> - панель будет генерироваться<br/><b>Нет</b> - панель не будет генерироваться', 'type' => 'select', 'values' => array ('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => extra_get_param('lastcomments','sidepanel')));
array_push($cfgX, array('name' => 'number', 'title' => 'Количество выводимых комментариев', 'descr' => 'Значение по умолчанию: <b>10</b>', 'type' => 'input', 'html_flags' => 'size=5', 'value' => extra_get_param('lastcomments','number')));
array_push($cfgX, array('name' => 'comm_length', 'title' => 'Усечение длины комментария', 'descr' => 'Кол-во символов из комментария для отображения<br/>Значение по умолчанию: <b>50</b>', 'type' => 'input', 'html_flags' => 'size=5', 'value' => extra_get_param('lastcomments','comm_length')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки боковой панели</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'ppage', 'title' => 'Разрешить собственную страницу плагина', 'descr' => '<b>Да</b> - собственная страница разрешена<br/><b>Нет</b> - собственная страница запрещена', 'type' => 'select', 'values' => array ('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => extra_get_param('lastcomments','ppage')));
array_push($cfgX, array('name' => 'pp_number', 'title' => 'Количество выводимых комментариев', 'descr' => 'Значение по умолчанию: <b>30</b>', 'type' => 'input', 'html_flags' => 'size=5', 'value' => extra_get_param('lastcomments','pp_number')));
array_push($cfgX, array('name' => 'pp_comm_length', 'title' => 'Усечение длины комментария', 'descr' => 'Кол-во символов из комментария для отображения<br/>Значение по умолчанию: <b>500</b>', 'type' => 'input', 'html_flags' => 'size=5', 'value' => extra_get_param('lastcomments','pp_comm_length')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки собственной страницы плагина</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>", 'type' => 'select', 'values' => array ( '0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(extra_get_param($plugin,'localsource'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки отображения</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Использовать кеширование данных<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "Период обновления кеша<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(extra_get_param($plugin,'cacheExpire'))?extra_get_param($plugin,'cacheExpire'):'60'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки кеширования</b>', 'entries' => $cfgX));

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('lastcomments', $cfg);
	print_commit_complete('lastcomments');
} else {
	generate_config_page('lastcomments', $cfg);
}
