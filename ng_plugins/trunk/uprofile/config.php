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
array_push($cfg, array('descr' => '������ ��������� ������������� ������������� ����� ������� � ������������� ����'));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "�������� ������� �� �������� ������ ����� ����� ������� ��� �����������<br /><small><b>������ �����</b> - ������ ����� �������� ����� ������� �� ������ ������� �����; � ������ ������������� - ������� ����� ����� �� ������������ �������� �������<br /><b>������</b> - ������� ����� ������� �� ������������ �������� �������</small>", 'type' => 'select', 'values' => array ( '0' => '������ �����', '1' => '������'), 'value' => intval(extra_get_param($plugin,'localsource'))));
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