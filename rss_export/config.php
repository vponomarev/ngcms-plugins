<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
$xfEnclosureValues = array('' => '');
//
// IF plugin 'XFIELDS' is enabled - load it to prepare `enclosure` integration
if (getPluginStatusActive('xfields')) {
	include_once(root . "/plugins/xfields/xfields.php");
	// Load XFields config
	if (is_array($xfc = xf_configLoad())) {
		foreach ($xfc['news'] as $fid => $fdata) {
			$xfEnclosureValues[$fid] = $fid . ' (' . $fdata['title'] . ')';
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
array_push($cfg, array('descr' => '<b>Плагин экспорта новостей в формате RSS</b><br>Полная лента новостей доступна по адресу: <b>' . generatePluginLink('rss_export', '', array(), array(), true, true) . (($demoCategory != '') ? '<br/>Лента новостей для категории <i>' . $catz[$demoCategory]['name'] . '</i>: ' . generatePluginLink('rss_export', 'category', array('category' => $demoCategory), array(), true, true) : '')));
array_push($cfgX, array('type' => 'select', 'name' => 'feed_title_format', 'title' => 'Формат заголовка ленты новостей', 'descr' => '<b>Сайт</b> - использовать заголовок сайта<br><b>Сайт+Категория</b> - использовать заголовок сайта+название категории (при выводе новостей из конкретной категории)<br><b>Ручной</b> - заголовок определяется Вами', 'values' => array('site' => 'Сайт', 'site_title' => 'Сайт+Категория', 'handy' => 'Ручной'), value => pluginGetVariable('rss_export', 'feed_title_format')));
array_push($cfgX, array('type' => 'input', 'name' => 'feed_title_value', 'title' => 'Ваш заголовок ленты новостей', 'descr' => 'Заголовок используется в случае выбора формата <b>"ручной"</b> в качестве заголовка ленты', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_export', 'feed_title_value')));
array_push($cfgX, array('type' => 'select', 'name' => 'news_title', 'title' => 'Формат заголовка новости', 'descr' => '<b>Название</b> - в заголовке указывается только название новости<br><b>Категория :: Название</b> - В заголовке указывается как категория так и название новости', 'values' => array('0' => 'Название', '1' => 'Категория :: Название'), value => pluginGetVariable('rss_export', 'news_title')));
array_push($cfgX, array('type' => 'input', 'name' => 'news_count', 'title' => 'Кол-во новостей для публикации в ленте', value => pluginGetVariable('rss_export', 'news_count')));
array_push($cfgX, array('type' => 'select', 'name' => 'use_hide', 'title' => 'Обрабатывать тег <b>[hide] ... [/hide]</b>', 'descr' => '<b>Да</b> - текст отмеченный тегом <b>hide</b> не отображается<br><b>Нет</b> - текст отмеченный тегом <b>hide</b> отображается', 'values' => array('0' => 'Нет', '1' => 'Да'), value => pluginGetVariable('rss_export', 'use_hide')));
array_push($cfgX, array('type' => 'select', 'name' => 'content_show', 'title' => 'Вид отображения новости', 'descr' => 'Вам необходимо указать какая именно информация будет отображаться внутри новости, экспортируемой через RSS', 'values' => array('0' => 'короткая+длинная', '1' => 'только короткая', '2' => 'только длинная'), value => pluginGetVariable('rss_export', 'content_show')));
array_push($cfgX, array('type' => 'input', 'name' => 'truncate', 'title' => 'Обрезать выводимую информацию', 'descr' => 'Кол-во символов до которых будет обрезаться выводимая в ленте информация.<br/>Значение по умолчанию: <b>0</b> - не обрезать', value => intval(pluginGetVariable('rss_export', 'truncate'))));
array_push($cfgX, array('type' => 'input', 'name' => 'delay', 'title' => 'Отсрочка вывода новостей в ленту', 'descr' => 'Вы можете задать время (<b>в минутах</b>) на которое будет откладываться вывод новостей в RSS ленту', value => pluginGetVariable('rss_export', 'delay')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Общие настройки</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'xfEnclosureEnabled', 'title' => "Генерация поля 'Enclosure' используя данные плагина xfields", 'descr' => "<b>Да</b> - включить генерацию<br /><b>Нет</b> - отключить генерацию</small>", 'type' => 'select', 'values' => array('1' => 'Да', '0' => 'Нет'), 'value' => intval(pluginGetVariable($plugin, 'xfEnclosureEnabled'))));
array_push($cfgX, array('name' => 'xfEnclosure', 'title' => "ID поля плагина <b>xfields</b>, которое будет использоваться для генерации поля <b>Enclosure</b>", 'type' => 'select', 'values' => $xfEnclosureValues, 'value' => pluginGetVariable($plugin, 'xfEnclosure')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Генерация поля <b>enclosure</b></b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "Использовать кеширование данных<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>", 'type' => 'select', 'values' => array('1' => 'Да', '0' => 'Нет'), 'value' => intval(pluginGetVariable($plugin, 'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "Период обновления кеша<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(pluginGetVariable($plugin, 'cacheExpire')) ? pluginGetVariable($plugin, 'cacheExpire') : '60'));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки кеширования</b>', 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
?>