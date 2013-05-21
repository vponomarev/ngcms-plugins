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
if (getPluginStatusActive('xfields')) {
	include_once(root."/plugins/xfields/xfields.php");

	// Load XFields config
	if (is_array($xfc=xf_configLoad())) {
		foreach ($xfc['news'] as $fid => $fdata) {
			$xfEnclosureValues[$fid] = $fid.' ('.$fdata['title'].')';
		}
	}
}

// For example - find 1st category with news for demo URL
$demoCategory = '';
foreach ($catz as $scanCat) {
	if ($scanCat['posts'] > 0) {
		$demoCategory = $scanCat['alt'];
		break;
	}
}

// Fill configuration parameters
$cfg = array();

$cfgX = array();
array_push($cfg, array('descr' => '<b>Плагин экспорта ленты новостей для поисковой системы Яndex</b><br>Полная лента новостей доступна по адресу: <b>'.generatePluginLink('rss_yandex', '', array(), array(), true, true).(($demoCategory != '')?'<br/>Лента новостей для категории <i>'.$catz[$demoCategory]['name'].'</i>: '.generatePluginLink('rss_yandex', 'category', array('category' => $demoCategory), array(), true, true):'')));
array_push($cfgX, array('type' => 'input',	'name' => 'feed_title', 'title' => 'Название RSS потока для полной ленты', 'descr' => 'Допустимые переменные:<br/><b>{{siteTitle}}</b> - название сайта<br/>Значение по умолчанию: <b>{{siteTitle}}</b>', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_yandex','feed_title')?pluginGetVariable('rss_yandex','feed_title'):'{{siteTitle}}'));
array_push($cfgX, array('type' => 'text',	'name' => 'news_title', 'title' => 'Заголовок (название) новости', 'descr' => 'Допустимые переменные:<br/><b>{{siteTitle}}</b> - название сайта<br/><b>{{newsTitle}}</b> - заголовок новости<br/><b>{{masterCategoryName}}</b> - название <b>главной</b> категории новости<br/>Значение по умолчанию: <b>{% if masterCategoryName %}{{masterCategoryName}} :: {% endif %}{{newsTitle}}</b>', 'html_flags' => 'style="width: 350px;"', 'value' => pluginGetVariable('rss_yandex','news_title')?pluginGetVariable('rss_yandex','news_title'):'{% if masterCategoryName %}{{masterCategoryName}} :: {% endif %}{{newsTitle}}'));
array_push($cfgX, array('type' => 'select',	'name' => 'full_format', 'title' => 'Формат генерации полного текста новости для ленты Яndex', 'descr' => '<b>Полная</b> - выводится только полная часть новости<br><b>Короткая+полная</b> - выводится короткая + полная часть новости', 'values' => array ( '0' => 'Полная', '1' => 'Полная+короткая'), value => pluginGetVariable('rss_yandex','full_format')));
array_push($cfgX, array('type' => 'input',	'name' => 'news_age', 'title' => 'Максимальный срок давности новостей для публикации в ленте', 'descr' => 'Яndex индексирует новости не старше <b>8 суток</b>.<br/>Значение по умолчанию: 10 суток', value => pluginGetVariable('rss_yandex','news_age')));
array_push($cfgX, array('type' => 'input',	'name' => 'delay', 'title' => 'Отсрочка вывода новостей в ленту', 'descr' => 'Вы можете задать время (<b>в минутах</b>) на которое будет откладываться вывод новостей в RSS ленту',value => pluginGetVariable('rss_yandex','delay')));
array_push($cfg,  array('mode' => 'group',	'title' => '<b>Общие настройки</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('type' => 'input', 'name' => 'feed_image_title', 'title' => 'Заголовок (title) для логотипа', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_yandex','feed_image_title')));
array_push($cfgX, array('type' => 'input', 'name' => 'feed_image_link', 'title' => 'URL с изображением логотипа', 'descr' => 'Желательный размер логотипа - 100 пикселей по максимальной стороне', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_yandex','feed_image_link')));
array_push($cfgX, array('type' => 'input', 'name' => 'feed_image_url', 'title' => 'Ссылка (link) для перехода по клику на логотип', 'descr' => 'Обычно - URL вашего сайта', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_yandex','feed_image_url')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Отображение логотипа</b>', 'entries' => $cfgX));


$cfgX = array();
array_push($cfgX, array('name' => 'xfEnclosureEnabled', 'title' => "Генерация поля 'Enclosure' используя данные плагина xfields", 'descr' => "<b>Да</b> - включить генерацию<br /><b>Нет</b> - отключить генерацию</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(pluginGetVariable($plugin,'xfEnclosureEnabled'))));
array_push($cfgX, array('name' => 'xfEnclosure', 'title' => "ID поля плагина <b>xfields</b>, которое будет использоваться для генерации поля <b>Enclosure</b>", 'type' => 'select', 'values' => $xfEnclosureValues, 'value' => pluginGetVariable($plugin,'xfEnclosure')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Генерация поля <b>enclosure</b> из поля xfields</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'textEnclosureEnabled', 'title' => "Вывод в поле 'Enclosure' всех изображений из текста новости (используя HTML тег &lt;img&gt;)", 'descr' => "<b>Да</b> - выводить все изображения<br /><b>Нет</b> - не выводить</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(pluginGetVariable($plugin,'textEnclosureEnabled'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Генерация поля <b>enclosure</b> из текста новости</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Использовать кеширование данных<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>", 'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), 'value' => intval(pluginGetVariable($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "Период обновления кеша<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(pluginGetVariable($plugin,'cacheExpire'))?pluginGetVariable($plugin,'cacheExpire'):'60'));
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