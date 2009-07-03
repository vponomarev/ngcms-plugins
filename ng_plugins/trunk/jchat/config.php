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
array_push($cfg, array('descr' => '������ ��������� ����� �� ����� AJAX-style ���.<br/>��������! ��� ������� ���-�� ����������� ������ ��� ����� ��������� ������������ �������� �� ������!'));
array_push($cfg, array('name' => 'access', 'title' => "�������� ������", 'descr' => '������� ����� ������� ����� � ������:<br/><b>���������</b> - ����� �� ����� ������ ���-�����<br/><b>��������</b> - ����� ������ ������������� ���-����<br/><b>��������+������</b> - ����� ����� ������ ��������� �����', 'type' => 'select', 'values' => array ('0' => '���������', '1' => '��������', '2' => '�������� + ������'), 'value' => extra_get_param($plugin,'access')));
array_push($cfg, array('name' => 'refresh', 'title' => "������ ���������� ������� � �������������", 'descr' => '������� ������ (� ��������) ����� ������������ ���-����� � �����������.<br/>������������� ��������: <b>30</b> ������<br/>����������� ��������: �� <b>5</b> �� <b>1800</b><br/><font color="red">��������! ��� ������ ��� �������� - ��� ���� �������� �� ������!</font>', 'type' => 'input', 'value' => extra_get_param($plugin,'refresh')));
array_push($cfg, array('name' => 'history', 'title' => "���-�� ��������� � ������� ���� ��� �����������", 'descr' => '��� ���������� (��������������) ����� ������������ � ����������. ��� ���������� ������ ��������� ����� ���������.<br/>�������� �� ���������: <b>30</b><br/>����������� ��������: �� <b>1</b> �� <b>500</b>', 'type' => 'input', 'value' => extra_get_param($plugin,'history')));
array_push($cfg, array('name' => 'rate_limit', 'title' => "����������� ����� ����� ����������� (� ��������)", 'descr' => '����������� ��������� �� ���� <b>IP �����</b>, ������������� ��� ������ �� �����.<br/>�������� �� ���������: <b>0</b> (����-������ ���������)', 'type' => 'input', 'value' => extra_get_param($plugin,'rate_limit')));
array_push($cfg, array('name' => 'maxidle', 'title' => "������������ ����� ����������� ���� (� ��������) ��� ������������ ������������", 'descr' => '����� ��������� ��������, ����� ������������ ��������� �������� � ����� � �������� ��� �� - ��� ������ �������� �� ������.<br/>����� �������� ����� �������� �� ������ �������� ������������ ����� (� ��������), � ������� �������� ���������������� ��� ����� �����������.<br/>�������� �� ���������: <b>0</b> (��� ����������� ����������)', 'type' => 'input', 'value' => extra_get_param($plugin,'maxidle')));
array_push($cfg, array('name' => 'maxwlen', 'title' => "������������ ����� ����� � ���������", 'descr' => '����� ������� ���������� �������� ����� ����������� ��������. ���������� ��� ��������� ��������� ��������������� ����������.<br/>�������� �� ���������: <b>40</b>', 'type' => 'input', 'value' => extra_get_param($plugin,'maxwlen')));
array_push($cfg, array('name' => 'maxlen', 'title' => "������������ ����� ���������", 'descr' => '������������ �� ������ ��������� ��������� ������� ��������� ��������.<br/>�������� �� ���������: <b>500</b> ��������', 'type' => 'input', 'value' => extra_get_param($plugin,'maxlen')));


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

