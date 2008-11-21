<?php

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

@include_once("XML/RSS.php");
$HAVE_PEAR_XML_RSS = 0;
if (class_exists('XML_RSS')) {
	$HAVE_PEAR_XML_RSS = 1;
}	


// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => 'ѕлагин позвол€ет отображать на сайте новости с различных RSS фидов.<br>ѕосле включени€ плагина во всех шаблонах станет доступна переменна€ {feed}'.($HAVE_PEAR_XML_RSS?'':'<br><br><font color=red><b>¬нимание!</b><br>¬аша система не поддерживает модул€ XML_RSS подсистемы PEAR. ƒл€ работы плагина будет использован <i><b>helper</b></i> компонент сайта dev.2z-project.com что несколько снизит общую производительность. ”становите по возможности модуль XML_RSS</font>')));
array_push($cfg, array('name' => 'feed', 'title' => "URL RSS фида дл€ отображени€", 'type' => 'input', 'value' => extra_get_param($plugin,'feed')));
array_push($cfg, array('name' => 'count', 'title' => " ол-во новостей дл€ отображени€ из фида", 'type' => 'input', 'value' => extra_get_param($plugin,'count')));
array_push($cfg, array('name' => 'skip', 'title' => " ол-во новостей пропускать при отображении из фида", 'description' => 'ћожно пропустить некоторое количество новостей (в начале) в случае, если в фиде первые новости содержат рекламу', 'type' => 'input', 'value' => extra_get_param($plugin,'skip')));
array_push($cfg, array('name' => 'cacheExpire', 'title' => "—рок хранени€ данных в кеше", 'descr' => '–екомендуетс€ значение оставл€ть не менее 60, иначе возможно сильное "торможение" из-за посто€нных обращений к RSS ленте', 'type' => 'input', 'value' => extra_get_param($plugin,'cachetime') ? extra_get_param($plugin,'cacheExpire') : 180));
array_push($cfg, array('name' => 'mantemplate', 'title' => "»спользовать собственный шаблон", 'descr' => '', 'type' => 'select', 'values' => array ( '1' => 'ƒа', '0' => 'Ќет'), 'value' => extra_get_param($plugin,'mantemplate')));
array_push($cfg, array('name' => 'template', 'title' => "Ўаблон при отображении новостей", 'descr' => 'ƒействует при включении опции <b>использовать собственный шаблон</b>.<br>Ќовости из фида отображаютс€ через заданный шаблон<br>ƒоступные переменные:<br><b>{link}</b> - ссылка на новость<br><b>{title}</b> - наименование новости<br><b>{description}</b></b> - описание новости', 'type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param($plugin,'template')));
array_push($cfg, array('name' => 'templatedelim', 'title' => "Ўаблон дл€ разделител€ между новост€ми", 'descr' => 'ƒействует при включении опции <b>использовать собственный шаблон</b>.<br>', 'type' => 'text', 'html_flags' => 'rows=4 cols=60', 'value' => extra_get_param($plugin,'templatedelim')));

// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>