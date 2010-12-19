<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

// Set default values if values are not set [for new variables]
$pluginDefaults = array(
	'global_default' => 1,
	'default_news' => 2,
	'default_categories' => 2,
);

foreach ($pluginDefaults as $k => $v) {
	if (pluginGetVariable('comments', $k) == null)
		pluginSetVariable('comments', $k, $v);
}

// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => '������ ��������� ������� ���������� ������ � �������������.<br/><font color="red"><b>�������� ��������:</b> ������ ��� ����� ������ ���������� �����-������� �� <u>��������� ������� �����</u></font>.'));

$cfgX = array();
array_push($cfgX, array('name' => 'regonly', 'title' => "����������� ������ ��� ������������������", 'descr' => '<b>��</b> - ����������� ����� ��������� ������ ������������������ ������������<br/><b>���</b> - ����������� ����� �������� ����� ����������', 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(pluginGetVariable($plugin,'regonly'))));
array_push($cfgX, array('name' => 'guest_edup_lock', 'title' => "��������� ������ ������������ <b>email</b>'� ������������������ �������������", 'descr' => '<b>��</b> - ����� �� ������ � �������� email ������ ��������� ����� ��� ������������������ �������������<br/><b>���</b> - ����� ����� ������������ ����� �������� email �����', 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(pluginGetVariable($plugin,'guest_edup_lock'))));
array_push($cfgX, array('name' => 'backorder', 'title' => "����������� ����������� ������������", 'descr' => "<b>������</b> - ����������� � ������� ����������<br/><b>��������</b> - ����� ����� ������������ �������", 'type' => 'select', 'values' => array ( '0' => '������', '1' => '��������'), 'value' => intval(pluginGetVariable($plugin,'backorder'))));
array_push($cfgX, array('name' => 'maxlen', 'title' => "������������ ������", 'descr' => "������� ������������ ���-�� �������� ��� ������������ (��������: <b>200</b>; <b>0</b> - �� ������������)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => pluginGetVariable($plugin, 'maxlen')));
array_push($cfgX, array('name' => 'maxwlen', 'title' => "������������ ���� � ������������", 'descr' => "� ������ ���������� ��������� �����, � ����� ����� ������������� ����� ����������� ������ (��������: <b>50</b>)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => pluginGetVariable($plugin, 'maxwlen')));
array_push($cfgX, array('name' => 'multi', 'title' => "��������� ������������� �����������", 'descr' => "<b>��</b> - ������������ ����� ��������� ��������������� ��������� ������������<br/><b>���</b> - ������������ ��������� ��������� ��������������� ��������� ������������ (���������� ��������� ����������� ������� ������������)", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(pluginGetVariable($plugin,'multi'))));
array_push($cfgX, array('name' => 'author_multi', 'title' => "��������� ������������� ����������� <u>��� ������</u>", 'descr' => "<b>��</b> - ����� ����� ��������� ��������������� ��������� ������������<br/><b>���</b> - ������ ��������� ��������� ��������������� ��������� ������������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(pluginGetVariable($plugin,'author_multi'))));
array_push($cfgX, array('name' => 'timestamp', 'title' => "������ ����������� ����/�������", 'descr' => "������ �� ������ �������: <a href=\"http://php.net/date/\" target=\"_blank\">php.net/date</a><br/>�������� �� ���������: <b>j.m.Y - H:i</b>", 'type' => 'input', 'value' => pluginGetVariable($plugin,'timestamp')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>����� ���������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'global_default', 'title' => "�� ��������� �����������", 'descr' => '<b>���������</b> - ����� ��������� � ������� ���� ��� ���� �� ���������<br/><b>���������</b> - ����� ��������� ������� ���� ��� ���� �� ���������', 'type' => 'select', 'values' => array ( '0' => '���������', '1' => '���������'), 'value' => intval(pluginGetVariable($plugin,'global_default'))));
array_push($cfgX, array('name' => 'default_news', 'title' => "�������� ����������� ��� ���������� ��������", 'descr' => '��� ���������� �������� �� ��������� ����� ���������������:<br/><b>���������</b> - ����������� ����� ���������<br/><b>���������</b> - ����������� ����� ���������<br/><b>�� ���������</b> - ���� ����������/������� ������������ ����� ������� �� �������� ������� ��������� �������', 'type' => 'select', 'values' => array ( '0' => '���������', '1' => '���������', '2' => '�� ���������'), 'value' => intval(pluginGetVariable($plugin,'default_news'))));
array_push($cfgX, array('name' => 'default_categories', 'title' => "�������� ����������� ��� ���������� ���������", 'descr' => '��� ���������� ��������� �� ��������� ����� ���������������:<br/><b>���������</b> - �� ��������� ����������� � �������� ���� ��������� ���������<br/><b>���������</b> - �� ��������� ����������� � ���� ��������� ����� ���������<br/><b>�� ���������</b> - ���� ����������/������� ������������ ����� ������� �� ��������� "�� ��������� �����������"', 'type' => 'select', 'values' => array ( '0' => '���������', '1' => '���������', '2' => '�� ���������'), 'value' => intval(pluginGetVariable($plugin,'default_categories'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �� ���������</b>', 'entries' => $cfgX, 'toggle' => true, 'toggle.mode' => 'hide'));

$cfgX = array();
array_push($cfgX, array('name' => 'multipage', 'title' => "������������ ��������������� �����������", 'descr' => '<b>��</b> - �� �������� ������� ����� ������������ ������ ����� ������������, ��������� ����� �������� �� ��������� ���������<br/><b>���</b> - ��� ����������� ����� ������������ �� �������� �������', 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(pluginGetVariable($plugin,'multipage'))));
array_push($cfgX, array('name' => 'multi_mcount', 'title' => "���-�� ������������ �� �������� �������", 'descr' => "������� ���-�� ������������, ������������ �� �������� �������<br/>(<b>0</b> - �� ���������� �� ������ �����������)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => pluginGetVariable($plugin, 'multi_mcount')));
array_push($cfgX, array('name' => 'multi_scount', 'title' => "���-�� ������������ �� �������� � �������������", 'descr' => "������� ���-�� ������������, ������������ �� ������ �������� � �������������<br/>(<b>0</b> - ���������� ��� �� ����� ��������)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => pluginGetVariable($plugin, 'multi_scount')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������������� �����������</b>', 'entries' => $cfgX, 'toggle' => true, 'toggle.mode' => 'hide'));

$cfgX = array();
array_push($cfgX, array('name' => 'inform_author', 'title' => "��������� ������ �� email � ����� �����������", 'descr' => "<b>��</b> - ��� ���������� ������� ����������� ����� ����� �������� e-mail ���������<br/><b>���</b> - ����� �� ����� �������� e-mail �����������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(pluginGetVariable($plugin,'inform_author'))));
array_push($cfgX, array('name' => 'inform_admin', 'title' => "��������� �������������� � ����� �����������", 'descr' => "<b>��</b> - ��� ���������� ������� ����������� ������������� ����� �������� e-mail ���������<br/><b>���</b> - �������������(�) �� ����� �������� e-mail �����������", 'type' => 'select', 'values' => array ( '0' => '���', '1' => '��'), 'value' => intval(pluginGetVariable($plugin,'inform_admin'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ����������</b>', 'entries' => $cfgX, 'toggle' => true, 'toggle.mode' => 'hide'));


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>