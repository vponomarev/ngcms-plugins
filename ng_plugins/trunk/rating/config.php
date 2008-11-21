<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

// Fill configuration parameters
$skList = array();
if ($skDir = opendir(extras_dir.'/rating/tpl/skins')) {
	while ($skFile = readdir($skDir)) {
		if (!preg_match('/^\./', $skFile)) {
			$skList[$skFile] = $skFile;
		}
	}
	closedir($skDir);
}	


// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => '������ ��������� ����������� ����������� ������� ��� �������� �� �����.'));
array_push($cfgX, array('name' => 'regonly', 'title' => '������� ������ ��� ������������������','descr' => '<b>��</b> - ����������� ������ ����� ������ ������������������ ������������<br><b>���</b> - ����������� ������ ����� ���','type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => extra_get_param($plugin,'regonly')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => "�������� ������� �� �������� ������ ����� ����� ������� ��� �����������<br /><small><b>������ �����</b> - ������ ����� �������� ����� ������� �� ������ ������� �����; � ������ ������������� - ������� ����� ����� �� ������������ �������� �������<br /><b>������</b> - ������� ����� ������� �� ������������ �������� �������</small>", 'type' => 'select', 'values' => array ( '0' => '������ �����', '1' => '������'), 'value' => intval(extra_get_param($plugin,'localsource'))));
array_push($cfgX, array('name' => 'localskin', 'title' => "�������� �������� ���������<br /><small>��������� ���� ����� �������������� ��� ��������� <b>������</b> � ���������� ����</small>", 'type' => 'select', 'values' => $skList, 'value' => extra_get_param($plugin,'localskin')?extra_get_param($plugin,'localskin'):'basic'));
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