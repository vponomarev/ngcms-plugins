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
$cfgX = array();
array_push($cfg, array('descr' => '������ ��������� XML ����� ����� ��� ��������� ������� Google'));
array_push($cfgX, array('name' => 'main', 'title' => "��������� �������� �������� � ����� �����", 'descr' => "<b>��</b> - �������� ����� ����������� � ����� �����<br /><b>���</b> - �������� �� ����� ����������� � ����� �����", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'main'))));
array_push($cfgX, array('name' => 'main_pr', 'title' => "��������� �������� ��������", 'descr' => '�������� �� <b>0.0</b> �� <b>1.0</b>', 'type' => 'input', 'value' => (extra_get_param($plugin,'main_pr') == '')?'1.0':extra_get_param($plugin,'main_pr')));
array_push($cfgX, array('name' => 'mainp', 'title' => "��������� ����������� �������� �������� � ����� �����", 'descr' => "<b>��</b> - �������� ����� ����������� � ����� �����<br /><b>���</b> - �������� �� ����� ����������� � ����� �����", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'mainp'))));
array_push($cfgX, array('name' => 'mainp_pr', 'title' => "��������� ����������� �������� ��������", 'descr' => '�������� �� <b>0.0</b> �� <b>1.0</b>', 'type' => 'input', 'value' => (extra_get_param($plugin,'mainp_pr') == '')?'0.5':extra_get_param($plugin,'mainp_pr')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ��� �������� �������� �����</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cat', 'title' => "��������� �������� ��������� � ����� �����", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'cat'))));
array_push($cfgX, array('name' => 'cat_pr', 'title' => "��������� ������� ���������", 'type' => 'input', 'value' => (extra_get_param($plugin,'cat_pr') == '')?'0.5':extra_get_param($plugin,'cat_pr')));
array_push($cfgX, array('name' => 'catp', 'title' => "��������� ����������� ������� ��������� � ����� �����", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'catp'))));
array_push($cfgX, array('name' => 'catp_pr', 'title' => "��������� ����������� ���������", 'type' => 'input', 'value' => (extra_get_param($plugin,'catp_pr') == '')?'0.5':extra_get_param($plugin,'catp_pr')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ��� ������� ���������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'news', 'title' => "��������� �������� �������� � ����� �����", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'news'))));
array_push($cfgX, array('name' => 'news_pr', 'title' => "��������� ������� ��������", 'type' => 'input', 'value' => (extra_get_param($plugin,'news_pr') == '')?'0.3':extra_get_param($plugin,'news_pr')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ��� ������� ��������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'static', 'title' => "��������� ����������� �������� � ����� �����", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'static'))));
array_push($cfgX, array('name' => 'static_pr', 'title' => "��������� ����������� �������", 'type' => 'input', 'value' => (extra_get_param($plugin,'static_pr') == '')?'0.3':extra_get_param($plugin,'static_pr')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ��� ����������� �������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "������������ ����������� ����� �����<br /><small><b>��</b> - ����������� ������������<br /><b>���</b> - ����������� �� ������������</small>", 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => intval(extra_get_param($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => '������ ���������� ���� (� ��������)<br /><small>(����� ������� ������ ���������� ���������� ����. �������� �� ���������: <b>10800</b>, �.�. 3 ����)', 'type' => 'input', 'value' => intval(extra_get_param($plugin,'cacheExpire'))?extra_get_param($plugin,'cacheExpire'):'10800'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �����������</b>', 'entries' => $cfgX));


// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>