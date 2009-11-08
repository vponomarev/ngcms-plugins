<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

// Generate balance list
$blist = array('' => '�������� ������');
foreach ($mysql->select("select * from ".prefix."_balance_manager where monetary=1 order by id") as $brow) {
	$blist[$brow['id']] = $brow['id'].($brow['type']?' ('.$brow['type'].')':'');
}

// Fill configuration parameters
$cfg = array();

array_push($cfg, array('descr' => '������ ��������� �������� ������� �� ����������� ����� SMS ��� ������ ������� smscoin.com.<br>��� ����� ������� ������������ ������ <b>���:����</b>.<br/><b><u>����������� ��������� ��� ������� SMSCOIN:</u></b><br/>'.
	'<b>Result URL:</b> '.home.generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept', 'acceptor' => 'smscoin')).'<br/>'.
	'<b>Success URL:</b> '.home.generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept_form', 'acceptor' => 'smscoin', 'result_ok' => '1')).'<br/>'.
	'<b>Fail URL:</b> '.home.generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept_form', 'acceptor' => 'smscoin', 'result_fail' => '1')).'<br/>'
	));

$cfgX = array();
array_push($cfgX, array('name' => 'balance_no', 'title' => '����� ������� �� ������� ����������� ������', 'descr' => '� ������ ������������ ������ ���������� �������<br/><font color="red"><b>��� ���������� <u>��������</u> �������� (��������, �� WEBMONEY) � <u>�����������</u> �������� (�� SMSCOIN) ������������ ������������� ��� �������� �� SMSCOIN ��������� ���������� �������������� ���������� ������ - ��� �������� ����� ����� ��������� ����������� ������� �� �����������.</b><br/>������� ������������� � ���������� ������� finance</font>','type' => 'select', 'values' => $blist, value => extra_get_param('fin_smscoin','balance_no')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>���������� ���������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'post_url', 'title' => 'URL ��������� �����', 'descr' => '�������� ����� `<b>����� �����</b> � ���������� ������ <b>sms:����</b>�','type' => 'input', value => extra_get_param('fin_smscoin','post_url')));
array_push($cfgX, array('name' => 'bank_id', 'title' => '������������� ������ ������� <b>sms:����</b>', 'descr' => '���������� ������� ����������������� ����� ������ ������� <b>sms:����</b>','type' => 'input', value => intval(extra_get_param('fin_smscoin','bank_id'))));
array_push($cfgX, array('name' => 'secret_key', 'title' => '��������� ���', 'descr' => '������ ��������� ��� �������� � ���������� ������� <b>���:����</b>','type' => 'input', value => extra_get_param('fin_smscoin','secret_key')));
array_push($cfgX, array('name' => 'clear_mode', 'title' => '��� ����� ���������� �����', 'descr' => '<b>�����</b> - ���� ����������� �� �����, ������ ����� ������� ������������<br/><b>�������</b> - ���� ����������� �� �����, ������ ���������� �� �������� SMS �������','type' => 'select', 'values' => array(0 => '�����', 1 => '�������'), 'value' => extra_get_param('fin_smscoin','clear_mode')));
array_push($cfgX, array('name' => 'pay_rate', 'title' => '����������� ��������� �� `USD` (� ������� ������������ ������� � �������� SMSCOIN) � ������ ����� (������� ������: <b>'.pluginGetVariable('finance', 'syscurrency').'</b>)', 'descr' => '1 <b>USD</b> SMSCOIN = XX.XXX <b>'.pluginGetVariable('finance', 'syscurrency').'</b>','type' => 'input', value => pluginGetVariable('fin_smscoin','pay_rate')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>�������������� ��������� � �������� SMSCOIN</b>', 'entries' => $cfgX));

// RUN
if ($_REQUEST['action'] == 'commit') {

	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>