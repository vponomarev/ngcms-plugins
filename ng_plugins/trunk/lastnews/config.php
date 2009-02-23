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
array_push($cfgX, array('name' => 'localsource', 'title' => "�������� ������� �� �������� ������ ����� ����� ������� ��� �����������<br /><small><b>������ �����</b> - ������ ����� �������� ����� ������� �� ������ ������� �����; � ������ ������������� - ������� ����� ����� �� ������������ �������� �������<br /><b>������</b> - ������� ����� ������� �� ������������ �������� �������</small>", 'type' => 'select', 'values' => array ( '0' => '������ �����', '1' => '������'), 'value' => intval(extra_get_param($plugin,'localsource'))));
array_push($cfgX, array('name' => 'dateformat', 'title' => "������ ���� ��� ����������� ���������� � �������", 'descr' =>"�������� �� ���������: \"{day0}.{month0}.{year}\"<br/>��������� ����������:<br/>{day} - ���� (1 - 31)<br>{day0} - ���� (01 - 31)<br>{month} - ����� (1 - 12)<br>{month0} - ����� (01 - 12)<br>{year} - ��� (00 - 99)<br>{year2} - ��� (1980 - 2100)<br>{month_s} - ����� ������ (���, ���,...)<br>{month_l} - ����� ������ (������, �������,...)", 'type' => 'input', 'value' => extra_get_param($plugin,'dateformat')));
array_push($cfgX, array('name' => 'number', 'title' => "���-�� �������� ��� �����������", 'descr' =>"�������� �� ���������: <b>10</b>", 'html_flags' => 'size=5', 'type' => 'input', 'value' => extra_get_param($plugin,'number')));
array_push($cfgX, array('name' => 'maxlength', 'title' => "����������� ����� �������� �������", 'descr' => "�������� �� ���������: <b>100</b><br/>(���� �������� ��������� ��������� �������, �� ��� ����� �������)", 'html_flags' => 'size=5', 'type' => 'input', 'value' => intval(extra_get_param($plugin,'maxlength'))?extra_get_param($plugin,'maxlength'):'100'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �����������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'pcall', 'title' => "���������� � ���������� ���������<br /><small><b>��</b> - � ������� �������� ����������� ���������� ���������� ������ ��������<br /><b>���</b> - ���������� ������ �������� ������������ ������</small>", 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => intval(extra_get_param($plugin,'pcall'))));
array_push($cfgX, array('name' => 'pcall_mode', 'title' => "����� ������", 'descr' => "��� ���������� ������� ����� �� ������� ����������� �������� ����� �������������<br/><b>�������</b> - ������� ������ � ������ ������� (<font color=\"red\">�������������</font>)<br /><b>��������</b> - �������� �������<br><b>������</b> - ������ �������</small>", 'type' => 'select', 'values' => array ( '0' => '�������', '1' => '��������', '2' => '������'), 'value' => intval(extra_get_param($plugin,'pcall_mode'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>����������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "������������ ����������� ������<br /><small><b>��</b> - ����������� ������������<br /><b>���</b> - ����������� �� ������������</small>", 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => intval(extra_get_param($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "������ ���������� ����<br /><small>(����� ������� ������ ���������� ���������� ����. �������� �� ���������: <b>60</b>)</small>", 'html_flags' => 'size=5', 'type' => 'input', 'value' => intval(extra_get_param($plugin,'cacheExpire'))?extra_get_param($plugin,'cacheExpire'):'60'));
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