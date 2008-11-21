<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

$count = extra_get_param($plugin,'count');
if ((intval($count) < 1)||(intval($count) > 20))
	$count = 3;

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => '������ ��������� ������������� ����� ���������� ����� �� �����. ������ ��� �������.<br />����� ��������� ������� �� ���� �������� ������ �������� ���������� {ads1}, {ads2}, ..., {ads<b>N</b>}<br/><br/>[<font color="red"><b> !!! �������� !!! </b></font>]<br/> ��� ������������� <b><u>����������� ������</u></b> �������� ��������� ������, ��� ���������� ����� � ����� ������� <b>main.tpl</b> (������ - ����� ����� ������� <b>[/sitelock}</b>) �������� ����������: <b>{plugin_ads_defer}</b>'));
array_push($cfg, array('name' => 'count', 'title' => "���-�� ��������� ������", 'type' => 'input', 'value' => $count));

for ($i = 1; $i <= $count; $i++) {
	$cfgX = array();
	array_push($cfgX, array('name' => 'ads'.$i.'_type', 'type' => 'select', 'title' => '���������� ���������� {ads'.$i.'}', 'descr' => ($i==1)?'<b>�����</b> - ������ �� �������� ��������<br /><b>!�����</b> - ����� ����� �����<br /><b>��������</b> - �� ���� ���������<br /><b>� ����������� ��������</b> - ������ ������� ����������� ��������<br /><b>�����</b> - �� ���������� �����':'', 'values' => array ( '' => '�����', 'root' => '�����', 'noroot' => '!�����', 'all' => '�����', 'static' => '� ����������� ��������'), value => extra_get_param('ads','ads'.$i.'_type')));
	array_push($cfgX, array('name' => 'ads'.$i, 'title' => "����������� ���������� �����<br /><small>(���������� <b>{ads".$i."}</b>)</small>", 'type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param($plugin,'ads'.$i)));
	array_push($cfgX, array('name' => 'ads'.$i.'_defer', 'type' => 'select', 'title' => '���������� �������� ������������ JavaScript �������', 'descr' => ($i==1)?'<b>���</b> - ��� ������� ��������� ����<br /><b>��</b> - ��� ��������� JavaScript ���� � �� ������ ������������ ���������� �������� ������� (�������� ����������� �������� ��������� ������� � ������ � �������������� ��������� ��������� �����)':'', 'values' => array ( '0' => $lang['noa'], '1' => $lang['yesa']), value => extra_get_param('ads','ads'.$i.'_defer')));
	array_push($cfgX, array('name' => 'ads'.$i.'_deferblk', 'title' => "����������� �� �������� HTML ������� <b>���������� ��������</b>", 'descr' => "���� ���� ����� ����������� �� ����� ������� ��� ������������� ����������� ������. ���� ������� <u><b>������</b></u> ���� ������� � <b><u>������</u></b> ����� ID: <b>adsTarget".$i."</b><br/><b>�������� �� ���������:</b> &nbsp; &nbsp;<font color='blue'><b>&lt;div id=&quot;adsTarget".$i."&quot;&gt;&lt;/div&gt;</b></font>", 'type' => 'text', 'html_flags' => 'rows=1 cols=60', 'value' => extra_get_param($plugin,'ads'.$i.'_deferblk')));
	array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ���������� ����� � <b>'.$i.'</b>', 'entries' => $cfgX));
}

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

