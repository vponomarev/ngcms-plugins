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
$cfgX = array();
array_push($cfg, array('descr' => '������ ��������� ������� ���������� ������ � �������������'));

$cfgX = array();
array_push($cfgX, array('name' => 'regonly', 'title' => "����������� ������ ��� ������������������", 'descr' => '<b>��</b> - ����������� ����� ��������� ������ ������������������ ������������<br/><b>���</b> - ����������� ����� �������� ����� ����������', 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'regonly'))));
array_push($cfgX, array('name' => 'backorder', 'title' => "����������� ����������� ������������", 'descr' => "<b>������</b> - ����������� � ������� ����������<br/><b>��������</b> - ����� ����� ������������ �������", 'type' => 'select', 'values' => array ( '0' => '������', '1' => '��������'), 'value' => intval(extra_get_param($plugin,'backorder'))));
array_push($cfgX, array('name' => 'maxlen', 'title' => "������������ ������", 'descr' => "������� ������������ ���-�� �������� ��� ������������ (��������: <b>200</b>)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'maxlen')));
array_push($cfgX, array('name' => 'maxwlen', 'title' => "������������ ���� � ������������", 'descr' => "� ������ ���������� ��������� �����, � ����� ����� ������������� ����� ����������� ������ (��������: <b>50</b>)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'maxwlen')));
array_push($cfgX, array('name' => 'flood_time', 'title' => "������ �� �����", 'descr' => "����������� ����������� ���������� ������� (� ��������) ����� ������������� ������������", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'flood_time')));
array_push($cfgX, array('name' => 'multi', 'title' => "��������� ������������� �����������", 'descr' => "<b>��</b> - ������������ ����� ��������� ��������������� ��������� ������������<br/><b>���</b> - ������������ ��������� ��������� ��������������� ��������� ������������ (���������� ��������� ����������� ������� ������������)", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'multi'))));
array_push($cfgX, array('name' => 'author_multi', 'title' => "��������� ������������� ����������� <u>��� ������</u>", 'descr' => "<b>��</b> - ����� ����� ��������� ��������������� ��������� ������������<br/><b>���</b> - ������ ��������� ��������� ��������������� ��������� ������������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'author_multi'))));


//array_push($cfgX, array('name' => 'extform', 'title' => "����� ����������� �����", 'descr' => "<b>�������</b> - ����� ������ ��������� � ����� �������<br/><b>��������� ��������</b> - � ������� ��������� ������ ������, ����� �� ������������ �� ��������� ��������", 'type' => 'select', 'values' => array ( '0' => '�������', '1' => '��������� ��������'), 'value' => intval(extra_get_param($plugin,'extform'))));
//array_push($cfgX, array('name' => 'errlist', 'title' => "������ ������", 'descr' => "������������ � �������:<br/>���_������<b>|</b>�����_������<br/><b>���_������</b> - ���������� �������� ������������� (�� 1 �� 255) ������<br/><b>�����_������</b> - ����� ������, ������������ ������������.<br/>������������ � �������������� ����� ������������ �����, �� � �� ����� ��������� ������ ���", 'type' => 'text', 'html_flags' => 'cols=50 rows=6', 'value' => extra_get_param($plugin,'errlist')));
/*
array_push($cfgX, array('name' => 'inform_author', 'title' => "��������� ������ ������� �� email � ��������", 'descr' => "<b>��</b> - �� ������ ����� �� ������ ����� ������������ email ���������<br/><b>���</b> - email ��������� ������������ �� �����", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'inform_author'))));
array_push($cfgX, array('name' => 'inform_reporter', 'title' => "��������� � ������� �������� ������ ������", 'descr' => "<b>��</b> - ����� ����� ��������� email ��������� ��� ������� ������������� �� ��� �����<br/><b>���</b> - email ��������� ������������ �� �����<br/><b>�� �������</b> - email ��������� ����� ������������, ���� ��� ��������� �������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��', '2' => '�� �������'), 'value' => intval(extra_get_param($plugin,'inform_reporter'))));
array_push($cfgX, array('name' => 'allow_unreg', 'title' => "��������� �������������������� ��������� ������", 'descr' => "<b>��</b> - �������������������� ������������ ������ ��������� �����<br/><b>���</b> - ����� ������ �������� ������ ������������������ ������������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'allow_unreg'))));
array_push($cfgX, array('name' => 'allow_unreg_inform', 'title' => "��������� �������������������� �������� ����������", 'descr' => "<b>��</b> - �������������������� ������������ ������ ������� ���� email ����� ��� ��������� ����� � ������� ������������� �� �����<br/><b>���</b> - �������� email ���������� �������������������� ������������ �� ������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'allow_unreg_inform'))));
array_push($cfgX, array('name' => 'allow_text', 'title' => "��������� <b>������������������</b> ��������� ��������� ��������� � ������ �� ������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'allow_text'))));
array_push($cfgX, array('name' => 'allow_text_unreg', 'title' => "��������� <b>��������������������</b> ��������� ��������� ��������� � ������ �� ������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'allow_text_unreg'))));
*/
array_push($cfg,  array('mode' => 'group', 'title' => '<b>����� ���������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'inform_author', 'title' => "��������� ������ �� email � ����� �����������", 'descr' => "<b>��</b> - ��� ���������� ������� ����������� ����� ����� �������� e-mail ���������<br/><b>���</b> - ����� �� ����� �������� e-mail �����������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'inform_author'))));
array_push($cfgX, array('name' => 'inform_admin', 'title' => "��������� �������������� � ����� �����������", 'descr' => "<b>��</b> - ��� ���������� ������� ����������� ������������� ����� �������� e-mail ���������<br/><b>���</b> - �������������(�) �� ����� �������� e-mail �����������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(extra_get_param($plugin,'inform_admin'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ����������</b>', 'entries' => $cfgX));


//$cfgX = array();
//array_push($cfgX, array('name' => 'cache', 'title' => "��������� ����������� �����<br />", 'descr' => '<b>��</b> - ����������� ���������<br /><b>���</b> - ����������� ���������', 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => intval(extra_get_param($plugin,'cache'))));
//array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �����������</b>', 'entries' => $cfgX));


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>