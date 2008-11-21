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
array_push($cfg, array('descr' => '<b>Плагин расширенного экспорта новостей в формате RSS</b><br>В отличии от встроенного генератора новостей в формате RSS, данный плагин позволяет гибко настраивать все параметры генерации новостей'));
array_push($cfg, array('type' => 'select','name' => 'feed_title_format', 'title' => 'Формат заголовка ленты новостей', 'descr' => '<b>Сайт</b> - использовать заголовок сайта<br><b>Сайт+Категория</b> - использовать заголовок сайта+название категории (при выводе новостей из конкретной категории)<br><b>Ручной</b> - заголовок определяется Вами', 'values' => array ( 'site' => 'Сайт', 'site_title' => 'Сайт+Категория', 'handy' => 'Ручной'), value => extra_get_param('rss_export','feed_title_format')));
array_push($cfg, array('type' => 'input', 'name' => 'feed_title_value', 'title' => 'Ваш заголовок ленты новостей', 'descr' => 'Заголовок используется в случае выбора формата <b>"ручной"</b> в качестве заголовка ленты', 'html_flags' => 'style="width: 250px;"', 'value' => extra_get_param('rss_export','feed_title_value')));
array_push($cfg, array('type' => 'select','name' => 'news_title', 'title' => 'Формат заголовка новости', 'descr' => '<b>Название</b> - в заголовке указывается только название новости<br><b>Категория :: Название</b> - В заголовке указывается как категория так и название новости', 'values' => array ( '0' => 'Название', '1' => 'Категория :: Название'), value => extra_get_param('rss_export','news_title')));
array_push($cfg, array('type' => 'input', 'name' => 'news_count', 'title' => 'Кол-во новостей для публикации в ленте', value => extra_get_param('rss_export','news_count')));
array_push($cfg, array('type' => 'select','name' => 'use_hide', 'title' => 'Обрабатывать тег <b>[hide] ... [/hide]</b>', 'descr' => '<b>Да</b> - текст отмеченный тегом <b>hide</b> не отображается<br><b>Нет</b> - текст отмеченный тегом <b>hide</b> отображается', 'values' => array ( '0' => 'Нет', '1' => 'Да'), value => extra_get_param('rss_export','use_hide')));
array_push($cfg, array('type' => 'select','name' => 'content_show', 'title' => 'Вид отображения новости', 'descr' => 'Вам необходимо указать какая именно информация будет отображаться внутри новости, экспортируемой через RSS', 'values' => array ( '0' => 'короткая+длинная', '1' => 'только короткая', '2' => 'только длинная'), value => extra_get_param('rss_export','content_show')));

// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>