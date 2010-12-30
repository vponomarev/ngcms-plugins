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
array_push($cfg, array('descr' => 'Плагин позволяет отображать на страницах сайта рекламные ссылки сервиса ссылочной рекламы LINKFEED.RU<br/><i>Хотите помочь автору плагина? Зарегистрируйтесь на LINKFEED по <a target="_blank" href="http://www.linkfeed.ru/reg/39627">партнёрской ссылке</a>.</i>'));
array_push($cfg, array('name' => 'linkfeed_user', 'title' => "Укажите значение переменной &quot;<b>LINKFEED_USER</b>&quot;", 'descr' => 'В интерфейсе добавления сайта в разделе <b>Инструкции по установке кода: для сайтов с поддержкой PHP</b> вы увидите строку вида:<br/><pre style="margin: 4px; background-color: #F0F0F0;">  define(\'LINKFEED_USER\', \'50d82d9d08e7a03f59268cf6a743ea31b4296dd0\');</pre>Текст <b>50d82d9d08e7a03f59268cf6a743ea31b4296dd0</b> и является искомым значением.', 'html_flags' => 'size=40;', 'type' => 'input', 'value' => pluginGetVariable('ads_linkfeed', 'linkfeed_user')));
array_push($cfg, array('name' => 'multisite', 'title' => "Использовать мультисайтовый режим", 'descr' => "<b>Да</b> - если вы используете мультидоменный режим и хотите показывать рекламу отдельно на разных доменах<br /><b>Нет</b> - в ином случае", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(pluginGetVariable($plugin,'multisite'))));
array_push($cfg, array('name' => 'domains', 'title' => "Список доменных имён, на которых активировать плагин", 'descr' => "По одному доменному имени в строке.<br/>В случае, если доменны имена не заданы, плагин будет активироваться на всех доменах сайта.<br/><b>Пример:</b><br/>ngcms.ru<br/>www.ngcms.ru", 'type' => 'text', 'html_flags' => 'cols=50 rows=4', 'value' => pluginGetVariable($plugin,'domains')));
array_push($cfg, array('name' => 'bcount',  'title' => "Кол-во рекламных блоков", 'descr' => 'Укажите кол-во блоков, между которыми вы будете разделять рекламные ссылки', 'type' => 'input', 'value' => (intval(pluginGetVariable('ads_sape', 'bcount'))>0)?intval(pluginGetVariable('ads_sape', 'bcount')):1));
array_push($cfg, array('name' => 'blength', 'title' => "Кол-во ссылок в блоках (через запятую)", 'descr' => 'Укажите сколько ссылок показывать в каждом блоке. В последнем блоке всегда отображаются все оставшиеся ссылки.<br/>Пример: <b>2,1,4</b> означает, что:<br/>В первом блоке будет <b>2</b> ссылки<br/>Во втором блоке будет <b>1</b> ссылка<br/>В третьем блоке будет <b>4</b> ссылки', 'type' => 'input', 'value' => pluginGetVariable('ads_sape', 'blength')));

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

