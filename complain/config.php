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
array_push($cfg, array('descr' => 'Плагин любому посетителю отправить администратору/автору новости отчёт о проблеме на сайте'));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>", 'type' => 'select', 'values' => array ( '0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(extra_get_param($plugin,'localsource'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки отображения</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'extform', 'title' => "Режим отображения формы", 'descr' => "<b>Новость</b> - форма отчёта выводится в самой новости<br/><b>Отдельная страница</b> - в новости выводится только ссылка, форма же показывается на отдельной странице", 'type' => 'select', 'values' => array ( '0' => 'Новость', '1' => 'Отдельная страница'), 'value' => intval(extra_get_param($plugin,'extform'))));
array_push($cfgX, array('name' => 'errlist', 'title' => "Список ошибок", 'descr' => "Записывается в формате:<br/>КОД_ОШИБКИ<b>|</b>ТЕКСТ_ОШИБКИ<br/><b>КОД_ОШИБКИ</b> - уникальный цифровой идентификатор (от 1 до 255) ошибки<br/><b>ТЕКСТ_ОШИБКИ</b> - текст ошибки, показываемый пользователю.<br/>Пользователю и администратору будет отображаться текст, но в БД будет храниться только код", 'type' => 'text', 'html_flags' => 'cols=50 rows=6', 'value' => extra_get_param($plugin,'errlist')));
array_push($cfgX, array('name' => 'inform_author', 'title' => "Оповещать автора новости по email о проблеме", 'descr' => "<b>Да</b> - на каждый отчёт об ошибке будет сформировано email сообщение<br/><b>Нет</b> - email сообщение отправляться не будет", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'inform_author'))));
array_push($cfgX, array('name' => 'inform_admin', 'title' => "Оповещать <b>администраторов сайта</b> по email о проблеме", 'descr' => "<b>Да</b> - на каждый отчёт об ошибке будет сформировано email сообщение<br/><b>Нет</b> - email сообщение отправляться не будет", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'inform_admin'))));
array_push($cfgX, array('name' => 'inform_reporter', 'title' => "Оповещать о решении проблемы автора отчёта", 'descr' => "<b>Да</b> - автор будет получаеть email сообщение при реакции администрации на его отчёт<br/><b>Нет</b> - email сообщение отправляться не будет<br/><b>По запросу</b> - email сообщение будет отправляться, если оно запрошено автором", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да', '2' => 'По запросу'), 'value' => intval(extra_get_param($plugin,'inform_reporter'))));
array_push($cfgX, array('name' => 'allow_unreg', 'title' => "Разрешить незарегистрированным оставлять отчёты", 'descr' => "<b>Да</b> - незарегистрированный пользователь сможет оставлять отчёт<br/><b>Нет</b> - отчёт сможет оставить только зарегистрированный пользователь", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'allow_unreg'))));
array_push($cfgX, array('name' => 'allow_unreg_inform', 'title' => "Разрешить незарегистрированным получать оповещения", 'descr' => "<b>Да</b> - незарегистрированный пользователь сможет указать свой email адрес для получения писем о реакции администрации на отчёт<br/><b>Нет</b> - получить email оповещение незарегистрированный пользователь не сможет", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'allow_unreg_inform'))));
// array_push($cfgX, array('name' => 'allow_text', 'title' => "Разрешить <b>зарегистрированным</b> добавлять текстовое сообщение к отчёту об ошибке", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'allow_text'))));
// array_push($cfgX, array('name' => 'allow_text_unreg', 'title' => "Разрешить <b>незарегистрированным</b> добавлять текстовое сообщение к отчёту об ошибке", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'allow_text_unreg'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки оповещений</b>', 'entries' => $cfgX));


$cfgX = array();
array_push($cfgX, array('name' => 'admins', 'title' => "Список назначенных администраторов", 'descr' => "Укажите список логинов пользователей (по одному логину в строке), которым будут выданы административные права для работы с данным плагином<br/><i>Пробелы в конце строк недопустимы!</i>", 'type' => 'text', 'html_flags' => 'cols=50 rows=2', 'value' => extra_get_param($plugin,'admins')));
array_push($cfgX, array('name' => 'inform_admins', 'title' => "Оповещать <b>назначенных администраторов</b> по email о проблеме", 'descr' => "<b>Да</b> - на каждый отчёт об ошибке будет сформировано email сообщение<br/><b>Нет</b> - email сообщение отправляться не будет", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'inform_admin'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Управление доступом</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Разрешить кеширование формы<br />", 'descr' => '<b>Да</b> - кеширование разрешено<br /><b>Нет</b> - кеширование запрещено', 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin,'cache'))));
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