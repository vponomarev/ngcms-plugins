<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

// Preload engine
include_once 'cron.php';

global $CRONDATA;
$cronLines = array();
foreach ($cron=cron_load() as $k => $v) {
 $cronLines[] = $v[0];
}


// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => '������ ��������� ���������� ����������� unix ��������� cron, � ������ - ��������� �������� ������������� ������'));
array_push($cfg, array('name' => 'crondata', 'title' => "������ �������� �����<br /><small>������ ������ ������������ �� ���� ����� ���������� ����������� ��������. �� ���� ���������� ��������� �� �������� ������ ����������� ���� ���������� ��������, ���� - <b>*</b>, ��� �������� '� ����� ������'.<br />������ ����������:<br /><b>min</b> - ����� ������<br /><b>hour</b> - ����� ����<br /><b>day</b> - ����� ���<br /><b>month</b> - ����� ������<br /><b>DOW</b> - ���� ������ [�� ��������������]<br /><b>plugin</b> - ID ������� ������� ���� ���������<br /><b>plugin CMD</b> - ������� ������������ �������<br /><br /><u>������:</u><br /> <b><font color=blue>15 0 * * * test help</font></b><br /> - �������� ������ ���� � 00:15 ������ <i>test</i> � ���������� <i>help</i></small>", 'type' => 'text', 'html_flags' => 'rows=10 cols=100', 'value' => implode("\n",$cronLines)));

// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	//commit_plugin_config_changes($plugin, $cfg);
	$CRONDATA = array();
	foreach (explode("\n",$_REQUEST['crondata']) as $v) {
		array_push($CRONDATA, array($v));
	}	
	cron_save();

	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>