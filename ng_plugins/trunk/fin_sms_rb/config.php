<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

include_once root."includes/inc/httpget.inc.php";
include_once root."includes/inc/xml2dom.php";


// Generate balance list
$blist = array('0' => '�������� ������');
foreach ($mysql->select("select * from ".prefix."_balance_manager where monetary=1 order by id") as $brow) {
	$blist[$brow['id']] = $brow['id'].($brow['type']?' ('.$brow['type'].')':'');
}

// Fill configuration parameters
$cfg = array();

array_push($cfg, array('descr' => '������ ��������� �������� ������� ��������� ���������� ������� �����, ����������� ����������� SMS ��������� (��� ������ ������� <a href="http://russianbilling.com/" target="_blank">RussianBilling.com</a>).<br />������ ������ �������� ����������� � ������� <b>finance</b>.<br /><br />'));

$cfgX = array();
array_push($cfgX, array('name' => 'passkey', 'title' => '����-������ �������', 'descr' => '<font color="red">���� �������� ���������� ��������� ��� ����������� ������������!</font><br/>�������� ������������� ������������ ��������� � ���������� ������� RussianBilling','type' => 'input', value => extra_get_param('fin_sms_rb','passkey')));
array_push($cfgX, array('name' => 'prefix', 'title' => 'SMS-�������', 'descr' => '����� ���������� ������� �������, �� ������� �������� ������ ���������� ���� SMS','type' => 'input', value => extra_get_param('fin_sms_rb','passkey')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ����������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'bonus_mode', 'title' => '����� ���������� ������ �������������', 'descr' => '<b>���� �������</b>','type' => 'select', 'values' => array( '1' => '���� �������'), value => extra_get_param('fin_sms_rb','bonus_mode')));
array_push($cfgX, array('name' => 'balance_no', 'title' => '����� ������� �� ������� ����������� ������', 'descr' => '<b>����� �����</b> - ����������� ����� �����, ��������� � ��� �����<br /><b>���� �������</b> - ����������� �����, ������� �������� ��<br /><font color=red>� ������ ����������� <u>������</u> ���������� �������!</font>','type' => 'select', 'values' => $blist, value => extra_get_param('fin_sms_rb','balance_no')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>���������� ���������</b>', 'entries' => $cfgX));


// RUN
if (($_REQUEST['action'] == 'commit')) {

	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

