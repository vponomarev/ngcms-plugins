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
array_push($cfg, array('descr' => '������ ������������ ����������� �����������/������� � ���������� ������� �� ����������.'));
array_push($cfg, array('name' => 'period', 'title' => '������������� ������� ��������', 'descr' => '������ �������� ����� <i>���� ���������</i> � <i>���� ����������</i>.<br>��� ���� ������������ ������ - ��� ���� �������� �� ��, �� ��� ���� ����� ����� ������������ ����� ����������/������ � ����������','type' => 'select', 'values' => array ( '0' => '�� ���������', '5m' => '5 �����', '10m' => '10 �����', '15m' => '15 �����', '30m' => '30 �����', '1h' => '1 ���', '2h' => '2 ����', '3h' => '3 ����', '6h' => '6 �����', '12h' => '12 �����'), value => pluginGetVariable($plugin,'period')));

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	//commit_plugin_config_changes($plugin, $cfg);


	$regRun = array();
	switch ($_REQUEST['period']) {
		case '5m'  : $regRun = array('*', '*'); break;
		case '10m' : $regRun = array('0,10,20,30,40,50', '*'); break;
		case '15m' : $regRun = array('0,15,30,45', '*'); break;
		case '30m' : $regRun = array('0,30', '*'); break;
		case '1h'  : $regRun = array('0', '*'); break;
		case '2h'  : $regRun = array('0', '0,2,4,6,8,10,12,14,16,18,20,22'); break;
		case '3h'  : $regRun = array('0', '0,3,6,9,12,15,18,21'); break;
		case '4h'  : $regRun = array('0', '0,4,8,12,16,20'); break;
		case '6h'  : $regRun = array('0', '0,6,12,18'); break;
		case '8h'  : $regRun = array('0', '0,8,16'); break;
		case '12h' : $regRun = array('0', '0,12'); break;
		default	   : $regRun = array('0', '0'); break;
	}

	commit_plugin_config_changes($plugin, $cfg);

	$cron->unregisterTask('nsched');
	$cron->registerTask('nsched', 'run', $regRun[0], $regRun[1], '*', '*', '*');

	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
