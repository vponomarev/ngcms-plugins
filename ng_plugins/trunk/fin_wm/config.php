<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

//include_once root."extras/fin_sms/inc/httpget.inc.php";
//include_once root."extras/fin_sms/inc/xml2dom.php";


// Generate balance list
$blist = array('' => '�������� ������');
foreach ($mysql->select("select * from ".prefix."_balance_manager where monetary=1 order by id") as $brow) {
	$blist[$brow['id']] = $brow['id'].($brow['type']?' ('.$brow['type'].')':'');
}

// Fill configuration parameters
$cfg = array();

array_push($cfg, array('descr' => '������ ��������� �������� ������� �� ����������� ����� ������� ����������� ����� WebMoney.<br>� ����� � ����������� ���������� ������� ����������� �����, ������ ��������� ��������� ������ ������ � ����� ������.<br/><b><u>����������� ��������� ��� ������� WebMoney:</u></b><br/>'.
	'<b>Result URL:</b> '.home.generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept', 'acceptor' => 'wm')).'<br/>'.
	'<b>Success URL:</b> '.home.generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept_form', 'acceptor' => 'wm', 'result_ok' => '1')).'<br/>'.
	'<b>Fail URL:</b> '.home.generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept_form', 'acceptor' => 'wm', 'result_fail' => '1')).'<br/>'
	));

$cfgX = array();
array_push($cfgX, array('name' => 'balance_no', 'title' => '����� ������� �� ������� ����������� ������', 'descr' => '� ������ ������������ ������ ���������� �������','type' => 'select', 'values' => $blist, value => extra_get_param('fin_wm','balance_no')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>���������� ���������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'secret_key', 'title' => '��������� ��� ������� <b>Web Merchant Interface</b>', 'descr' => '������ ��������� ��� �������� � ���������� ������� https://merchant.webmoney.ru/ ��� ������ ��������','type' => 'input', value => extra_get_param('fin_wm','secret_key')));
array_push($cfgX, array('name' => 'test_mode', 'title' => '����� ������', 'descr' => '<b>��������</b> - ������������ �������� �������� ������� � ��������<br><b>��������</b> - �������� �� �����������, ���������� �������� ��������. ��� ���� �������� �������� <b><u>�����������</u></b> �� ���������� ������ ������������!','type' => 'select', values => array('0' => '��������', '1' => '��������'), value => extra_get_param('fin_wm','test_mode')));
array_push($cfgX, array('name' => 'sign_mode', 'title' => '����� ������������ �������', 'descr' => '<b>MD5</b> - ������������: <i>������</i>; �� �� ��������� ����� � �������� WebMoney<br><b>SIGN</b> - ������������: <i>�������</i>; ���������� ����� � �������� WebMoney<br><b>SIGN+AUTH</b> - ������������: <i>�������</i>; ���������� �������� ����������� ������� �� ������','type' => 'select', values => array('0' => 'MD5'), value => extra_get_param('fin_wm','sign_mode')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>�������������� ��������� � �������� WebMoney</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'allow_wmz', 'title' => '��������� ���� ������� �� `Z` (USD) ������','type' => 'select', 'values' => array(0 => '���', 1 => '��'), value => pluginGetVariable('fin_wm','allow_wmz')));
array_push($cfgX, array('name' => 'wmz_number', 'title' => '����� `Z` �������� �� ������� ����������� ����������', 'descr' => '������ ����������� ������ � ������ ���� ������. � �������, <b>Z349152268411</b>','type' => 'input', value => pluginGetVariable('fin_wm','wmz_number')));
array_push($cfgX, array('name' => 'wmz_rate', 'title' => '����������� ��������� �� `WMZ` � ������ ����� (������� ������: <b>'.pluginGetVariable('finance', 'syscurrency').'</b>)', 'descr' => '1 <b>WMZ</b> = XX.XXX <b>'.pluginGetVariable('finance', 'syscurrency').'</b>','type' => 'input', value => pluginGetVariable('fin_wm','wmz_rate')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>���� `WMZ` (USD)</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'allow_wmr', 'title' => '��������� ���� ������� �� `R` (RUR) ������','type' => 'select', 'values' => array(0 => '���', 1 => '��'), value => pluginGetVariable('fin_wm','allow_wmr')));
array_push($cfgX, array('name' => 'wmr_number', 'title' => '����� `R` �������� �� ������� ����������� ����������', 'descr' => '������ ����������� ������ � ������ ���� ������. � �������, <b>R349152268411</b>','type' => 'input', value => pluginGetVariable('fin_wm','wmr_number')));
array_push($cfgX, array('name' => 'wmr_rate', 'title' => '����������� ��������� �� `WMR` � ������ ����� (������� ������: <b>'.pluginGetVariable('finance', 'syscurrency').'</b>)', 'descr' => '1 <b>WMR</b> = XX.XXX <b>'.pluginGetVariable('finance', 'syscurrency').'</b>','type' => 'input', value => pluginGetVariable('fin_wm','wmr_rate')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>���� `WMR` (RUR)</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'allow_wme', 'title' => '��������� ���� ������� �� `E` (EUR) ������','type' => 'select', 'values' => array(0 => '���', 1 => '��'), value => pluginGetVariable('fin_wm','allow_wme')));
array_push($cfgX, array('name' => 'wme_number', 'title' => '����� `E` �������� �� ������� ����������� ����������', 'descr' => '������ ����������� ������ � ������ ���� ������. � �������, <b>E349152268411</b>','type' => 'input', value => pluginGetVariable('fin_wm','wme_number')));
array_push($cfgX, array('name' => 'wme_rate', 'title' => '����������� ��������� �� `WME` � ������ ����� (������� ������: <b>'.pluginGetVariable('finance', 'syscurrency').'</b>)', 'descr' => '1 <b>WME</b> = XX.XXX <b>'.pluginGetVariable('finance', 'syscurrency').'</b>','type' => 'input', value => pluginGetVariable('fin_wm','wme_rate')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>���� `WME` (EUR)</b>', 'entries' => $cfgX));


//array_push($cfgX, array('name' => 'currency', 'title' => '��� ������ �������� �� ������� ������������ ���� �����','type' => 'select', 'values' => array('WMZ', 'WMR'), value => extra_get_param('fin_wm','currency')));
//array_push($cfgX, array('name' => 'wm_number', 'title' => '����� �������� �� ������� ����������� ����������', 'descr' => '������ ����������� ������ � ������ ���� ������. � �������, <b>Z349152268411</b>','type' => 'input', value => extra_get_param('fin_wm','wm_number')));

// RUN
if ($_REQUEST['action'] == 'commit') {

	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>