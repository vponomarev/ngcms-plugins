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
array_push($cfg, array('descr' => '������ �������� ���������� �������. ������������ ������������ �� ���-�� ���������� �������.'));
array_push($cfgX, array('name' => 'number', 'title' => "���-�� �������� ��� �����������<br /><small>(������� �������� ����� ������������ � ����� '��������')</small>", 'type' => 'input', 'value' => intval(extra_get_param($plugin,'number'))?extra_get_param($plugin,'number'):'10'));
array_push($cfgX, array('name' => 'maxlength', 'title' => "����������� ����� �������� �������<br /><small>(���� �������� ��������� ��������� �������, �� ��� ����� �������)</small>", 'type' => 'input', 'value' => intval(extra_get_param($plugin,'maxlength'))?extra_get_param($plugin,'maxlength'):'100'));
array_push($cfgX, array('name' => 'counter', 'title' => "���������� ������� ����������<br /><b>��</b> - ������� ����� ������������<br /><b>���</b> - ������� �� ����� ������������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'counter'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "�������� ������� �� �������� ������ ����� ����� ������� ��� �����������<br /><small><b>������ �����</b> - ������ ����� �������� ����� ������� �� ������ ������� �����; � ������ ������������� - ������� ����� ����� �� ������������ �������� �������<br /><b>������</b> - ������� ����� ������� �� ������������ �������� �������</small>", 'type' => 'select', 'values' => array ( '0' => '������ �����', '1' => '������'), 'value' => intval(extra_get_param($plugin,'localsource'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �����������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "������������ ����������� ������<br /><small><b>��</b> - ����������� ������������<br /><b>���</b> - ����������� �� ������������</small>", 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => intval(extra_get_param($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "������ ���������� ����<br /><small>(����� ������� ������ ���������� ���������� ����. �������� �� ���������: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(extra_get_param($plugin,'cacheExpire'))?extra_get_param($plugin,'cacheExpire'):'60'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �����������</b>', 'entries' => $cfgX));


// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
