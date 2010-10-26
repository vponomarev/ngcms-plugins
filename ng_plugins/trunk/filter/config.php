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
array_push($cfg, array('descr' => '� ������� ������� ������� �� ������ ����������� ������������� ����� � ������������, ��������� ������ ������������� ���� (����, ��������) ��� ��������� ���������� ������������ ���������� ����������� ������������������ (� �������, ������)<br><br><br>'));
array_push($cfg, array('name' => 'replace', 'title' => "������ ����<br><br><i>�� ������ ������ �������� �� ������ ����� � ����� ������� '|' - ��, �� ��� ��� ����������.</i><br>������:<br>�����|��#��",'type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param('filter','replace')));
array_push($cfg, array('name' => 'block', 'title' => "���������� ����<br><br><i>�� ������ ������ �������� �� ����� ��������� ������������������. ���� ��� ������������������ ����������� � ������ �����������, �� ����������� �����������.</i>",'type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param('filter','block')));
//array_push($cfg, array('name' => 'template', 'title' => '������ ����������','type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param('test','template')));


// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('filter', $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page('filter', $cfg);
}
