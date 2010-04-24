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
array_push($cfg, array('descr' => '������ ������������ ��� ����������� ������ (���������� �����) ����� <b>trade7.ru</b>'));
array_push($cfgX, array('name' => 'id', 'title' => "��� ������������� (id)", 'descr' => '�������� ��������� <b>id</b> �� ������ ������� �������', 'type' => 'input', 'value' => pluginGetVariable('ads_trade7', 'id')));
array_push($cfgX, array('name' => 'cs', 'title' => "��������� ����� (cs)", 'descr' => '�������� ��������� <b>cs</b> �� ������ ������� �������', 'type' => 'input', 'value' => pluginGetVariable('ads_trade7', 'cs')));
array_push($cfgX, array('name' => 'categories_2', 'title' => "������ ��������� �������� (categories_2) ��� ����������� � ������", 'descr' => '�������� ��������� <b>categories_2</b> �� ������ ������� �������', 'html_flags' => 'size=40;', 'type' => 'input', 'value' => pluginGetVariable('ads_trade7', 'categories_2')));
array_push($cfgX, array('name' => 'size', 'title' => "������ ������ (size)", 'descr' => '�������� ��������� <b>size</b> �� ������ ������� �������', 'type' => 'input', 'value' => pluginGetVariable('ads_trade7', 'size')));
array_push($cfgX, array('name' => 'default', 'title' => "�������� �� ���������", 'descr' => '��� ���������� ������ ���������� ����� � ������, ���� ������ ��������� ����� ����������', 'type' => 'text', 'html_flags' => 'cols=70 rows=3', 'value' => pluginGetVariable('ads_trade7', 'default')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �������������/�����������</b>', 'entries' => $cfgX));



$cfgX = array();
array_push($cfgX, array('name' => 'timeout_sec', 'title' => "������� (����� �����, � ��������)", 'descr' => '������� �� ����� ��������� � �������', 'type' => 'input', 'value' => intval(pluginGetVariable('ads_trade7', 'timeout_sec'))));
array_push($cfgX, array('name' => 'timeout_usec', 'title' => "������� (������� �����, � ������������)", 'descr' => '������� �� ����� ��������� � �������', 'type' => 'input', 'value' => intval(pluginGetVariable('ads_trade7', 'timeout_usec'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ��������</b>', 'entries' => $cfgX));

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

