<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();


$xfEnclosureValues = array( '' => '');
//
// IF plugin 'XFIELDS' is enabled - load it to prepare `enclosure` integration
if (status('xfields')) {
	include_once(root."/plugins/xfields/xfields.php");

	// Load XFields config
	if (is_array($xfc=xf_configLoad())) {
		foreach ($xfc['news'] as $fid => $fdata) {
			$xfEnclosureValues[$fid] = $fid.' ('.$fdata['title'].')';
		}
	}
}

// Fill configuration parameters
$cfg = array();

$cfgX = array();
array_push($cfg, array('descr' => '<b>Плагин расширенного экспорта новостей в формате RSS</b><br>В отличии от встроенного генератора новостей в формате RSS, данный плагин позволяет гибко настраивать все параметры генерации новостей'));
array_push($cfgX, array('type' => 'select','name' => 'feed_title_format', 'title' => 'Формат заголовка ленты новостей', 'descr' => '<b>Сайт</b> - использовать заголовок сайта<br><b>Сайт+Категория</b> - использовать заголовок сайта+название категории (при выводе новостей из конкретной категории)<br><b>Ручной</b> - заголовок определяется Вами', 'values' => array ( 'site' => 'Сайт', 'site_title' => 'Сайт+Категория', 'handy' => 'Ручной'), value => extra_get_param('rss_export','feed_title_format')));
array_push($cfgX, array('type' => 'input', 'name' => 'feed_title_value', 'title' => 'Ваш заголовок ленты новостей', 'descr' => 'Заголовок используется в случае выбора формата <b>"ручной"</b> в качестве заголовка ленты', 'html_flags' => 'style="width: 250px;"', 'value' => extra_get_param('rss_export','feed_title_value')));
array_push($cfgX, array('type' => 'select','name' => 'news_title', 'title' => 'Формат заголовка новости', 'descr' => '<b>Название</b> - в заголовке указывается только название новости<br><b>Категория :: Название</b> - В заголовке указывается как категория так и название новости', 'values' => array ( '0' => 'Название', '1' => 'Категория :: Название'), value => extra_get_param('rss_export','news_title')));
array_push($cfgX, array('type' => 'input', 'name' => 'news_count', 'title' => 'Кол-во новостей для публикации в ленте', value => extra_get_param('rss_export','news_count')));
array_push($cfgX, array('type' => 'select','name' => 'use_hide', 'title' => 'Обрабатывать тег <b>[hide] ... [/hide]</b>', 'descr' => '<b>Да</b> - текст отмеченный тегом <b>hide</b> не отображается<br><b>Нет</b> - текст отмеченный тегом <b>hide</b> отображается', 'values' => array ( '0' => 'Нет', '1' => 'Да'), value => extra_get_param('rss_export','use_hide')));
array_push($cfgX, array('type' => 'select','name' => 'content_show', 'title' => 'Вид отображения новости', 'descr' => 'Вам необходимо указать какая именно информация будет отображаться внутри новости, экспортируемой через RSS', 'values' => array ( '0' => 'короткая+длинная', '1' => 'только короткая', '2' => 'только длинная'), value => extra_get_param('rss_export','content_show')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Общие настройки</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'xfEnclosureEnabled', 'title' => "Генерация поля 'Enclosure' используя данные плагина xfields", 'descr' => "<b>Да</b> - включить генерацию<br /><b>Нет</b> - отключить генерацию</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin,'xfEnclosureEnabled'))));
array_push($cfgX, array('name' => 'xfEnclosure', 'title' => "ID поля плагина <b>xfields</b>, которое будет использоваться для генерации поля <b>Enclosure</b>", 'type' => 'select', 'values' => $xfEnclosureValues, 'value' => extra_get_param($plugin,'xfEnclosure')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Генерация поля <b>enclosure</b></b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Использовать кеширование данных<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "Период обновления кеша<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(extra_get_param($plugin,'cacheExpire'))?extra_get_param($plugin,'cacheExpire'):'60'));
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