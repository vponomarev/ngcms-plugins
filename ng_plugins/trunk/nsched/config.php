<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

// Preload engine
include_once root.'plugins/cron/cron.php';

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => '������ ������������ ����������� �����������/������� � ���������� ������� �� ����������.'));
array_push($cfg, array('name' => 'period', 'title' => '������������� ������� ��������', 'descr' => '������ �������� ����� <i>���� ���������</i> � <i>���� ����������</i>.<br>��� ���� ������������ ������ - ��� ���� �������� �� ��, �� ��� ���� ����� ����� ������������ ����� ����������/������ � ����������','type' => 'select', 'values' => array ( '0' => '�� ���������', '5m' => '5 �����', '10m' => '10 �����', '15m' => '15 �����', '30m' => '30 �����', '1h' => '1 ���', '2h' => '2 ����', '3h' => '3 ����', '6h' => '6 �����', '12h' => '12 �����'), value => extra_get_param($plugin,'period')));

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	//commit_plugin_config_changes($plugin, $cfg);
	$regRun = array();
	switch ($_REQUEST['period']) {
		case '5m'  : $regRun = array( array('*', '*') ); break;
		case '10m' : $regRun = array( array('0', '*'), array('10', '*'), array('20', '*'), array('30','*'), array('40','*'), array('50','*')); break;
		case '15m' : $regRun = array( array('0', '*'), array('15', '*'), array('30', '*'), array('45','*')); break;
		case '30m' : $regRun = array( array('0', '*'), array('30', '*')); break;
		case '1h'  : $regRun = array( array('0', '*')); break;
		case '2h'  : $regRun = array(); for ($i=0; $i<12; $i++) { array_push($regRun, array('0', $i*2)); } break;
		case '3h'  : $regRun = array(); for ($i=0; $i<8; $i++)  { array_push($regRun, array('0', $i*3)); } break;
		case '4h'  : $regRun = array(); for ($i=0; $i<6; $i++)  { array_push($regRun, array('0', $i*4)); } break;
		case '6h'  : $regRun = array(); for ($i=0; $i<4; $i++)  { array_push($regRun, array('0', $i*6)); } break;
		case '8h'  : $regRun = array(); for ($i=0; $i<3; $i++)  { array_push($regRun, array('0', $i*8)); } break;
		case '12h' : $regRun = array(); for ($i=0; $i<2; $i++)  { array_push($regRun, array('0', $i*12)); } break;
	}

	commit_plugin_config_changes($plugin, $cfg);
	cron_unregister_task('nsched');
	foreach ($regRun as $v) {
		cron_register_task('nsched', 'run', $v[0], $v[1], '*', '*', '*');
	}
	cron_save();

	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
