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
array_push($cfg, array('descr' => 'Плагин позволяет отображать на страницах сайта рекламные ссылки сервиса ссылочной рекламы SAPE.RU (Sapient Solution)<br/><i>Хотите помочь автору плагина? Зарегистрируйтесь на SAPE по <a target="_blank" href="http://www.sape.ru/r.02705ab902.php">партнёрской ссылке</a>.</i>'));
array_push($cfg, array('name' => 'sape_user', 'title' => "Укажите значение переменной &quot;<b>SAPE_USER</b>&quot;", 'descr' => 'В интерфейсе добавления сайта в разделе <b>Установка кода отображения гипертекстовых ссылок</b> вы увидите строку вида:<br/><pre style="margin: 4px; background-color: #F0F0F0;">define(\'_SAPE_USER\', \'d871be55f20c3ebbc752f57e7962382b\');</pre>Текст <b>d871be55f20c3ebbc752f57e7962382b</b> и является искомым значением.', 'html_flags' => 'size=40;', 'type' => 'input', 'value' => extra_get_param('ads_sape', 'sape_user')));
array_push($cfg, array('name' => 'multisite', 'title' => "Использовать мультисайтовый режим", 'descr' => "<b>Да</b> - если вы используете мультидоменный режим и хотите показывать рекламу отдельно на разных доменах<br /><b>Нет</b> - в ином случае", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'multisite'))));
array_push($cfg, array('name' => 'bcount',  'title' => "Кол-во рекламных блоков", 'descr' => 'Укажите кол-во блоков, между которыми вы будете разделять рекламные ссылки', 'type' => 'input', 'value' => (intval(extra_get_param('ads_sape', 'bcount'))>0)?intval(extra_get_param('ads_sape', 'bcount')):1));
array_push($cfg, array('name' => 'blength', 'title' => "Кол-во ссылок в блоках (через запятую)", 'descr' => 'Укажите сколько ссылок показывать в каждом блоке. В последнем блоке всегда отображаются все оставшиеся ссылки.<br/>Пример: <b>2,1,4</b> означает, что:<br/>В первом блоке будет <b>2</b> ссылки<br/>Во втором блоке будет <b>1</b> ссылка<br/>В третьем блоке будет <b>4</b> ссылки', 'type' => 'input', 'value' => extra_get_param('ads_sape', 'blength')));

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

