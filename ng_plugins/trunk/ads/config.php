<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

$count = extra_get_param($plugin,'count');
if ((intval($count) < 1)||(intval($count) > 20))
	$count = 3;

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => 'ѕлагин позвол€ет редактировать часто измен€емые блоки на сайте. ќбычно это реклама.<br />ѕосле включени€ плагина во всех шаблонах станут доступны переменные {ads1}, {ads2}, ..., {ads<b>N</b>}<br/><br/>[<font color="red"><b> !!! ¬нимание !!! </b></font>]<br/> ѕри использовании <b><u>отложенного режима</u></b> загрузки рекламных блоков, вам необходимо ближе к концу шаблона <b>main.tpl</b> (обычно - сразу перед текстом <b>[/sitelock}</b>) вставить переменную: <b>{plugin_ads_defer}</b>'));
array_push($cfg, array('name' => 'count', 'title' => " ол-во рекламных блоков", 'type' => 'input', 'value' => $count));

for ($i = 1; $i <= $count; $i++) {
	$cfgX = array();
	array_push($cfgX, array('name' => 'ads'.$i.'_type', 'type' => 'select', 'title' => '–азмещение переменной {ads'.$i.'}', 'descr' => ($i==1)?'<b>ћорда</b> - только на головной странице<br /><b>!ћорда</b> - везде кроме морды<br /><b>—квозна€</b> - на всех страницах<br /><b>¬ статической странице</b> - ¬нутри шаблона статической страницы<br /><b>Ќигде</b> - не отображать нигде':'', 'values' => array ( '' => 'Ќигде', 'root' => 'ћорда', 'noroot' => '!ћорда', 'all' => '¬езде', 'static' => '¬ статической странице'), value => extra_get_param('ads','ads'.$i.'_type')));
	array_push($cfgX, array('name' => 'ads'.$i, 'title' => "ƒинамически измен€емый текст<br /><small>(переменна€ <b>{ads".$i."}</b>)</small>", 'type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param($plugin,'ads'.$i)));
	array_push($cfgX, array('name' => 'ads'.$i.'_defer', 'type' => 'select', 'title' => 'ќтложенна€ загрузка динамической JavaScript рекламы', 'descr' => ($i==1)?'<b>Ќет</b> - это обычный рекламный блок<br /><b>ƒа</b> - это рекламный JavaScript блок и вы хотите активировать отложенную загрузку рекламы (позволит значительно ускорить отрисовку страниц в случае с использованием медленных рекламных сетей)':'', 'values' => array ( '0' => $lang['noa'], '1' => $lang['yesa']), value => extra_get_param('ads','ads'.$i.'_defer')));
	array_push($cfgX, array('name' => 'ads'.$i.'_deferblk', 'title' => "¬ставл€емый на странице HTML элемент <b>отложенной загрузки</b>", 'descr' => "Ётот блок будет вставл€тьс€ на место рекламы при использовании отложенного режима. Ётот элемент <u><b>об€зан</b></u> быть блочным и <b><u>об€зан</u></b> иметь ID: <b>adsTarget".$i."</b><br/><b>«начение по умолчанию:</b> &nbsp; &nbsp;<font color='blue'><b>&lt;div id=&quot;adsTarget".$i."&quot;&gt;&lt;/div&gt;</b></font>", 'type' => 'text', 'html_flags' => 'rows=1 cols=60', 'value' => extra_get_param($plugin,'ads'.$i.'_deferblk')));
	array_push($cfg,  array('mode' => 'group', 'title' => '<b>Ќастройки рекламного блока є <b>'.$i.'</b>', 'entries' => $cfgX));
}

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

