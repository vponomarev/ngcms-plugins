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
array_push($cfg, array('name' => 'extdate', 'title' => '�������������� ���������� ��� ���������� �����','descr' => '�������� ����������:<br>{day} - ���� (1 - 31)<br>{day0} - ���� (01 - 31)<br>{month} - ����� (1 - 12)<br>{month0} - ����� (01 - 12)<br>{year} - ��� (00 - 99)<br>{year2} - ��� (1980 - 2100)<br>{month_s} - ����� ������ (���, ���,...)<br>{month_l} - ����� ������ (������, �������,...)','type' => 'select', 'values' => array ( '0' => '����', '1' => '���'), 'value' => extra_get_param($plugin,'extdate')));
array_push($cfg, array('name' => 'newdate', 'title' => '�������� ������ ����', 'descr' => '��� ���������� ������� ��������� ���������� ������ ����������� ���� � �������� �� ���������', 'type' => 'input', 'value' => extra_get_param($plugin,'newdate')));


// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
?>