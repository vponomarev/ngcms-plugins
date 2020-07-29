<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
pluginsLoadConfig();
$count = extra_get_param($plugin, 'count');
if ((intval($count) < 1) || (intval($count) > 20))
	$count = 3;
// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => 'Плагин позволяет редактировать часто изменяемые блоки на сайте. Обычно это реклама.<br />После включения плагина во всех шаблонах станут доступны переменные {ads1}, {ads2}, ..., {ads<b>N</b>}<br/><br/>[<font color="red"><b> !!! Внимание !!! </b></font>]<br/> При использовании <b><u>отложенного режима</u></b> загрузки рекламных блоков, вам необходимо ближе к концу шаблона <b>main.tpl</b> (обычно - сразу перед текстом <b>[/sitelock]</b>) вставить переменную: <b>{plugin_ads_defer}</b>'));
array_push($cfg, array('name' => 'count', 'title' => "Кол-во рекламных блоков", 'type' => 'input', 'value' => $count));
for ($i = 1; $i <= $count; $i++) {
	$cfgX = array();
	array_push($cfgX, array('name' => 'ads' . $i . '_type', 'type' => 'select', 'title' => 'Размещение переменной {ads' . $i . '}', 'descr' => ($i == 1) ? '<b>Нигде</b> - не отображать нигде<br/><b>Морда</b> - только на головной странице<br /><b>!Морда</b> - везде кроме морды<br /><b>Сквозная</b> - на всех страницах<br /><b>В статической странице</b> - Внутри шаблона статической страницы<br /><b>Новость.короткая</b> - в короткой новости (шаблон news.short.tpl)<br/><b>Новость.полная</b> - в полной новости (шаблон news.full.tpl)<br/><b>Новость</b> - в короткой и полной новости (шаблоны news.short.tpl, news.full.tpl)' : '', 'values' => array('' => 'Нигде', 'root' => 'Морда', 'noroot' => '!Морда', 'all' => 'Сквозная', 'static' => 'В статической странице', 'news.short' => 'Новость.короткая', 'news.full' => 'Новость.полная', 'news' => 'Новость'), value => extra_get_param('ads', 'ads' . $i . '_type')));
	array_push($cfgX, array('name' => 'ads' . $i, 'title' => "Динамически изменяемый текст<br /><small>(переменная <b>{ads" . $i . "}</b>)</small>", 'type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param($plugin, 'ads' . $i)));
	array_push($cfgX, array('name' => 'ads' . $i . '_defer', 'type' => 'select', 'title' => 'Отложенная загрузка динамической JavaScript рекламы', 'descr' => ($i == 1) ? '<b>Нет</b> - это обычный рекламный блок<br /><b>Да</b> - это рекламный JavaScript блок и вы хотите активировать отложенную загрузку рекламы (позволит значительно ускорить отрисовку страниц в случае с использованием медленных рекламных сетей)' : '', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), value => extra_get_param('ads', 'ads' . $i . '_defer')));
	array_push($cfgX, array('name' => 'ads' . $i . '_deferblk', 'title' => "Вставляемый на странице HTML элемент <b>отложенной загрузки</b>", 'descr' => "Этот блок будет вставляться на место рекламы при использовании отложенного режима. Этот элемент <u><b>обязан</b></u> быть блочным и <b><u>обязан</u></b> иметь ID: <b>adsTarget" . $i . "</b><br/><b>Значение по умолчанию:</b> &nbsp; &nbsp;<font color='blue'><b>&lt;div id=&quot;adsTarget" . $i . "&quot;&gt;&lt;/div&gt;</b></font>", 'type' => 'text', 'html_flags' => 'rows=1 cols=60', 'value' => extra_get_param($plugin, 'ads' . $i . '_deferblk')));
	array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки рекламного блока № <b>' . $i . '</b>', 'entries' => $cfgX));
}
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

