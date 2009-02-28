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
array_push($cfg, array('descr' => '������ ��������� ���������� �� ��������� ����� ��������� ������ ������� ��������� ������� SAPE.RU (Sapient Solution)<br/><i>������ ������ ������ �������? ����������������� �� SAPE �� <a target="_blank" href="http://www.sape.ru/r.02705ab902.php">���������� ������</a>.</i>'));
array_push($cfg, array('name' => 'sape_user', 'title' => "������� �������� ���������� &quot;<b>SAPE_USER</b>&quot;", 'descr' => '� ���������� ���������� ����� � ������� <b>��������� ���� ����������� �������������� ������</b> �� ������� ������ ����:<br/><pre style="margin: 4px; background-color: #F0F0F0;">define(\'_SAPE_USER\', \'d871be55f20c3ebbc752f57e7962382b\');</pre>����� <b>d871be55f20c3ebbc752f57e7962382b</b> � �������� ������� ���������.', 'html_flags' => 'size=40;', 'type' => 'input', 'value' => extra_get_param('ads_sape', 'sape_user')));
array_push($cfg, array('name' => 'multisite', 'title' => "������������ �������������� �����", 'descr' => "<b>��</b> - ���� �� ����������� �������������� ����� � ������ ���������� ������� �������� �� ������ �������<br /><b>���</b> - � ���� ������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'multisite'))));
array_push($cfg, array('name' => 'bcount',  'title' => "���-�� ��������� ������", 'descr' => '������� ���-�� ������, ����� �������� �� ������ ��������� ��������� ������', 'type' => 'input', 'value' => (intval(extra_get_param('ads_sape', 'bcount'))>0)?intval(extra_get_param('ads_sape', 'bcount')):1));
array_push($cfg, array('name' => 'blength', 'title' => "���-�� ������ � ������ (����� �������)", 'descr' => '������� ������� ������ ���������� � ������ �����. � ��������� ����� ������ ������������ ��� ���������� ������.<br/>������: <b>2,1,4</b> ��������, ���:<br/>� ������ ����� ����� <b>2</b> ������<br/>�� ������ ����� ����� <b>1</b> ������<br/>� ������� ����� ����� <b>4</b> ������', 'type' => 'input', 'value' => extra_get_param('ads_sape', 'blength')));

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

