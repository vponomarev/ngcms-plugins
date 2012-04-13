<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();


// Load XFields config
if (!function_exists('xf_configLoad')) {
	print "XFields plugin is not loaded now!";
} else {
	$XFc = xf_configLoad();

	$xfCatList = array( '' => ' // не выбрано //');
	foreach ($XFc['news'] as $k => $v) {
		if ($v['type'] == 'images')
			continue;

		$xfCatList[$k]= $k.' - '.$v['title'];
	}

	$xfNTableList = array( '' => ' // не выбрано //');
	foreach ($XFc['tdata'] as $k => $v) {
		if ($v['type'] == 'images')
			continue;

		$xfNTableList[$k]= $k.' - '.$v['title'];
	}
}


// Check if `feedback` plugin is installed
$feedbackFormList = array();
if (getPluginStatusInstalled('feedback')) {
	foreach ($mysql->select("select * from ".prefix."_feedback order by id", 1) as $frow) {
		$feedbackFormList [$frow['id']]= $frow['id'].' - '.$frow['title'];
	}
	if (!count($feedbackFormList)) {
		$feedbackFormList [0]= '// формы не найдены //';
	}
} else {
	$feedbackFormList [0]= '// плагин не установлен //';
}

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => 'Плагин реализует функционал "корзины товаров"<br/><b>Внимание!</b><br/>Для работы плагина <i>basket</i> вы должны включить и настроить плагины: <b>feedback</b> и <b>xfields</b>.'));

/*
$cfgX = array();
array_push($cfgX, array('name' => 'catalog_flag', 'type' => 'select', 'title' => 'Включить корзину для элементов каталога', 'descr' => '<b>Да</b> - корзина будет активна для элементов каталога<br/><b>Нет</b> - корзина не будет активна для элементов каталога', 'values' => array ( 0 => 'Нет', 1 => 'Да'), value => pluginGetVariable('basket','catalog_flag')));
array_push($cfgX, array('name' => 'catalog_activated', 'title' => "Активация корзины в каталоге по..", 'type' => 'select', 'descr' => '<b>Всем записям</b> - "положить в корзину" будет доступно для всех элементов<br/><b>Полю <i>xfields</i></b> - "положить в корзину" можно будет только те записи, в которых значение указанного поля <b>> 0</b> (больше нуля)', 'values' => array(0 => 'Всем записям', 1 => 'Полю xfields'), 'value' => pluginGetVariable('basket','catalog_activated')));
array_push($cfgX, array('name' => 'catalog_xfield', 'title' => "Поле xfields", 'type' => 'select', 'descr' => 'Поле для параметра "активация корзины по.."', 'values' => $xfCatList, 'value' => pluginGetVariable('basket','catalog_xfield')));
array_push($cfgX, array('name' => 'catalog_price', 'title' => "Поле с ценой", 'type' => 'select', 'descr' => 'Поле xfields с ценой товара', 'values' => $xfCatList, 'value' => pluginGetVariable('basket','catalog_price')));
array_push($cfgX, array('name' => 'catalog_itemname', 'type' => 'input', 'title' => 'Формат заголовка наименования товара:', 'descr' => 'Доступные переменные:<br/><b>{title}</b> - наименование элемента каталога<br/><b>{x:NAME}</b> (где <b>NAME</b> - название поля XFIELDS) - вывести доп. поле', 'html_flags' => 'style="width: 300px;"', 'value' => pluginGetVariable('basket','catalog_itemname')?pluginGetVariable('basket','catalog_itemname'):'{title}'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Работа с каталогом</b>', 'entries' => $cfgX));
*/

$cfgX = array();
array_push($cfgX, array('name' => 'ntable_flag', 'type' => 'select', 'title' => 'Включить корзину для таблиц доп. полей внутри новостей', 'descr' => '<b>Да</b> - корзина будет активна для элементов таблицы<br/><b>Нет</b> - корзина не будет активна для элементов таблицы', 'values' => array ( 0 => 'Нет', 1 => 'Да'), value => pluginGetVariable('basket','ntable_flag')));
array_push($cfgX, array('name' => 'ntable_activated', 'title' => "Активация корзины в таблице по..", 'type' => 'select', 'descr' => '<b>Всем записям</b> - "положить в корзину" будет доступно для всех элементов<br/><b>Полю <i>xfields</i></b> - "положить в корзину" можно будет только те записи, в которых значение указанного поля <b>> 0</b> (больше нуля)', 'values' => array(0 => 'Всем записям', 1 => 'Полю xfields'), 'value' => pluginGetVariable('basket','ntable_activated')));
array_push($cfgX, array('name' => 'ntable_xfield', 'title' => "Поле xfields", 'type' => 'select', 'descr' => 'Поле для параметра "активация корзины по.."', 'values' => $xfNTableList, 'value' => pluginGetVariable('basket','ntable_xfield')));
array_push($cfgX, array('name' => 'ntable_price', 'title' => "Поле с ценой", 'type' => 'select', 'descr' => 'Поле xfields с ценой товара', 'values' => $xfNTableList, 'value' => pluginGetVariable('basket','ntable_price')));
array_push($cfgX, array('name' => 'ntable_itemname', 'type' => 'input', 'title' => 'Формат заголовка наименования товара:', 'descr' => 'Доступные переменные:<br/><b>{title}</b> - наименование элемента каталога<br/><b>{xt:NAME}</b> (где <b>NAME</b> - название поля XFIELDS) - вывести доп. поле <u>таблицы</u><br/><b>{x:NAME}</b> (где <b>NAME</b> - название поля XFIELDS) - вывести доп. поле из оригинальной <u>новости</u>', 'html_flags' => 'style="width: 300px;"', 'value' => pluginGetVariable('basket','ntable_itemname')?pluginGetVariable('basket','ntable_itemname'):'{title}'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Работа с таблицами доп. полей внутри новостей</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'news_flag', 'type' => 'select', 'title' => 'Включить корзину для таблиц доп. полей внутри новостей', 'descr' => '<b>Да</b> - корзина будет активна для элементов таблицы<br/><b>Нет</b> - корзина не будет активна для элементов таблицы', 'values' => array ( 0 => 'Нет', 1 => 'Да'), value => pluginGetVariable('basket','news_flag')));
array_push($cfgX, array('name' => 'news_activated', 'title' => "Активация корзины в новостях по..", 'type' => 'select', 'descr' => '<b>Всем записям</b> - "положить в корзину" будет доступно для всех новостей<br/><b>Полю <i>xfields</i></b> - "положить в корзину" можно будет только те новости, в которых значение указанного поля <b>> 0</b> (больше нуля)', 'values' => array(0 => 'Всем записям', 1 => 'Полю xfields'), 'value' => pluginGetVariable('basket','news_activated')));
array_push($cfgX, array('name' => 'news_xfield', 'title' => "Поле xfields", 'type' => 'select', 'descr' => 'Поле для параметра "активация корзины по.."', 'values' => $xfCatList, 'value' => pluginGetVariable('basket','news_xfield')));
array_push($cfgX, array('name' => 'news_price', 'title' => "Поле с ценой", 'type' => 'select', 'descr' => 'Поле xfields с ценой товара', 'values' => $xfCatList, 'value' => pluginGetVariable('basket','news_price')));
array_push($cfgX, array('name' => 'news_itemname', 'type' => 'input', 'title' => 'Формат заголовка наименования товара:', 'descr' => 'Доступные переменные:<br/><b>{title}</b> - наименование элемента каталога<br/><b>{x:NAME}</b> (где <b>NAME</b> - название поля XFIELDS) - вывести доп. поле', 'html_flags' => 'style="width: 300px;"', 'value' => pluginGetVariable('basket','ntable_itemname')?pluginGetVariable('basket','news_itemname'):'{title}'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Работа с доп. полями внутри новостей</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'feedback_form', 'type' => 'select', 'title' => 'Форма обратной связи для оформления заказа', 'descr' => 'Плагин <b>basket</b> отвечает только за наполнение корзины товаров.<br/>Отправка заказа производится через форму обратной связи плагина <b>feedback</b>.<br/>Выберите форму обратной связи через которую будет производиться отправка заказа', 'values' => $feedbackFormList, value => pluginGetVariable('basket','feedback_form')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки интеграции</b>', 'entries' => $cfgX));



// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

