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
array_push($cfg, array('descr' => '������ ��������� ���������� �� ����� ������� � ��������� RSS �����.<br>����� ��������� ������� �� ���� �������� ������ �������� ���������� {feed}'.($HAVE_PEAR_XML_RSS?'':'<br><br><font color=red><b>��������!</b><br>���� ������� �� ������������ ������ XML_RSS ���������� PEAR. ��� ������ ������� ����� ����������� <i><b>helper</b></i> ��������� ����� dev.2z-project.com ��� ��������� ������ ����� ������������������. ���������� �� ����������� ������ XML_RSS</font>')));
array_push($cfg, array('name' => 'feed', 'title' => "URL RSS ���� ��� �����������", 'type' => 'input', 'value' => extra_get_param($plugin,'feed')));
array_push($cfg, array('name' => 'count', 'title' => "���-�� �������� ��� ����������� �� ����", 'type' => 'input', 'value' => extra_get_param($plugin,'count')));
array_push($cfg, array('name' => 'skip', 'title' => "���-�� �������� ���������� ��� ����������� �� ����", 'description' => '����� ���������� ��������� ���������� �������� (� ������) � ������, ���� � ���� ������ ������� �������� �������', 'type' => 'input', 'value' => extra_get_param($plugin,'skip')));
array_push($cfg, array('name' => 'cacheExpire', 'title' => "���� �������� ������ � ����", 'descr' => '������������� �������� ��������� �� ����� 60, ����� �������� ������� "����������" ��-�� ���������� ��������� � RSS �����', 'type' => 'input', 'value' => extra_get_param($plugin,'cachetime') ? extra_get_param($plugin,'cacheExpire') : 180));
array_push($cfg, array('name' => 'mantemplate', 'title' => "������������ ����������� ������", 'descr' => '', 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => extra_get_param($plugin,'mantemplate')));
array_push($cfg, array('name' => 'template', 'title' => "������ ��� ����������� ��������", 'descr' => '��������� ��� ��������� ����� <b>������������ ����������� ������</b>.<br>������� �� ���� ������������ ����� �������� ������<br>��������� ����������:<br><b>{link}</b> - ������ �� �������<br><b>{title}</b> - ������������ �������<br><b>{description}</b></b> - �������� �������', 'type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param($plugin,'template')));
array_push($cfg, array('name' => 'templatedelim', 'title' => "������ ��� ����������� ����� ���������", 'descr' => '��������� ��� ��������� ����� <b>������������ ����������� ������</b>.<br>', 'type' => 'text', 'html_flags' => 'rows=4 cols=60', 'value' => extra_get_param($plugin,'templatedelim')));

// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>