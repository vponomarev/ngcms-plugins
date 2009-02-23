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
array_push($cfgX, array('name' => 'localsource', 'title' => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>", 'type' => 'select', 'values' => array ( '0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(extra_get_param($plugin,'localsource'))));
array_push($cfgX, array('name' => 'dateformat', 'title' => "Формат даты при отображении информации о новости", 'descr' =>"Значение по умолчанию: \"{day0}.{month0}.{year}\"<br/>Доступные переменные:<br/>{day} - день (1 - 31)<br>{day0} - день (01 - 31)<br>{month} - месяц (1 - 12)<br>{month0} - месяц (01 - 12)<br>{year} - год (00 - 99)<br>{year2} - год (1980 - 2100)<br>{month_s} - текст месяца (Янв, Фев,...)<br>{month_l} - текст месяца (Январь, Февраль,...)", 'type' => 'input', 'value' => extra_get_param($plugin,'dateformat')));
array_push($cfgX, array('name' => 'number', 'title' => "Кол-во новостей для отображения", 'descr' =>"Значение по умолчанию: <b>10</b>", 'html_flags' => 'size=5', 'type' => 'input', 'value' => extra_get_param($plugin,'number')));
array_push($cfgX, array('name' => 'maxlength', 'title' => "Ограничение длины названия новости", 'descr' => "Значение по умолчанию: <b>100</b><br/>(если название превышает указанные пределы, то оно будет урезано)", 'html_flags' => 'size=5', 'type' => 'input', 'value' => intval(extra_get_param($plugin,'maxlength'))?extra_get_param($plugin,'maxlength'):'100'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки отображения</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'pcall', 'title' => "Интеграция с новостными плагинами<br /><small><b>Да</b> - в плагине появится возможность испольвать переменные других плагинов<br /><b>Нет</b> - переменные других плагинов использовать нельзя</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin,'pcall'))));
array_push($cfgX, array('name' => 'pcall_mode', 'title' => "Режим вызова", 'descr' => "Вам необходимо выбрать какой из режимов отображения новостей будет эмулироваться<br/><b>экспорт</b> - экспорт данных в другие плагины (<font color=\"red\">рекомендуется</font>)<br /><b>короткая</b> - короткая новость<br><b>полная</b> - полная новость</small>", 'type' => 'select', 'values' => array ( '0' => 'экспорт', '1' => 'короткая', '2' => 'полная'), 'value' => intval(extra_get_param($plugin,'pcall_mode'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Интеграция</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Использовать кеширование данных<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "Период обновления кеша<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>60</b>)</small>", 'html_flags' => 'size=5', 'type' => 'input', 'value' => intval(extra_get_param($plugin,'cacheExpire'))?extra_get_param($plugin,'cacheExpire'):'60'));
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