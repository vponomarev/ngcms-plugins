<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

include_once root."extras/fin_sms/inc/httpget.inc.php";
include_once root."extras/fin_sms/inc/xml2dom.php";


// Generate balance list
$blist = array('' => '�������� ������');
foreach ($mysql->select("select * from ".prefix."_balance_manager where monetary=1 order by id") as $brow) {
	$blist[$brow['id']] = $brow['id'].($brow['type']?' ('.$brow['type'].')':'');
}

// Fill configuration parameters
$cfg = array();

array_push($cfg, array('descr' => '������ ��������� �������� ������� �� ����������� ����� ������� ����������� ����� WebMoney.<br>� ����� � ����������� ���������� ������� ����������� �����, ������ ��������� ��������� ������ ������ � ����� ������.'));

$cfgX = array();
array_push($cfgX, array('name' => 'balance_no', 'title' => '����� ������� �� ������� ����������� ������', 'descr' => '� ������ ������������ ������ ���������� �������','type' => 'select', 'values' => $blist, value => extra_get_param('fin_wm','balance_no')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>���������� ���������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'currency', 'title' => '��� ������ �������� �� ������� ������������ ���� �����','type' => 'select', 'values' => array('WMZ', 'WMR'), value => extra_get_param('fin_wm','currency')));
array_push($cfgX, array('name' => 'wm_number', 'title' => '����� �������� �� ������� ����������� ����������', 'descr' => '������ ����������� ������ � ������ ���� ������. � �������, <b>Z349152268411</b>','type' => 'input', value => extra_get_param('fin_wm','wm_number')));
array_push($cfgX, array('name' => 'secret_key', 'title' => '��������� ��� ������� <b>Web Merchant Interface</b>', 'descr' => '������ ��������� ��� �������� � ���������� ������� https://merchant.webmoney.ru/ ��� ������ ��������','type' => 'input', value => extra_get_param('fin_wm','secret_key')));
array_push($cfgX, array('name' => 'test_mode', 'title' => '����� ������', 'descr' => '<b>��������</b> - ������������ �������� �������� ������� � ��������<br><b>��������</b> - �������� �� �����������, ���������� �������� ��������. ��� ���� �������� �������� <b><u>�����������</u></b> �� ���������� ������ ������������!','type' => 'select', values => array('0' => '��������', '1' => '��������'), value => extra_get_param('fin_wm','test_mode')));
array_push($cfgX, array('name' => 'sign_mode', 'title' => '����� ������������ �������', 'descr' => '<b>MD5</b> - ������������: <i>������</i>; �� �� ��������� ����� � �������� WebMoney<br><b>SIGN</b> - ������������: <i>�������</i>; ���������� ����� � �������� WebMoney<br><b>SIGN+AUTH</b> - ������������: <i>�������</i>; ���������� �������� ����������� ������� �� ������','type' => 'select', values => array('0' => 'MD5'), value => extra_get_param('fin_wm','sign_mode')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>�������������� ��������� � �������� WebMoney</b>', 'entries' => $cfgX));


// RUN 
if ($_REQUEST['action'] == 'commit') {
	
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>