<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();


// Load XFields config
if (!function_exists('xf_configLoad')) {
	print "XFields plugin is not loaded now!";
} else {
	$XFc = xf_configLoad();

	$xfCatList = array( '' => ' // �� ������� //');
	foreach ($XFc['news'] as $k => $v) {
		if ($v['type'] == 'images')
			continue;

		$xfCatList[$k]= $k.' - '.$v['title'];
	}

	$xfNTableList = array( '' => ' // �� ������� //');
	foreach ($XFc['tdata'] as $k => $v) {
		if ($v['type'] == 'images')
			continue;

		$xfNTableList[$k]= $k.' - '.$v['title'];
	}
}


// Check if `feedback` plugin is installed
$feedbackFormList = array();
if (getPluginStatusInstalled('feedback')) {
	foreach ($mysql->select("select * from ".prefix."_feedback order by id", 1) as $frow) {
		$feedbackFormList [$frow['id']]= $frow['id'].' - '.$frow['title'];
	}
	if (!count($feedbackFormList)) {
		$feedbackFormList [0]= '// ����� �� ������� //';
	}
} else {
	$feedbackFormList [0]= '// ������ �� ���������� //';
}

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => '������ ��������� ���������� "������� �������"<br/><b>��������!</b><br/>��� ������ ������� <i>basket</i> �� ������ �������� � ��������� �������: <b>feedback</b> � <b>xfields</b>.'));

/*
$cfgX = array();
array_push($cfgX, array('name' => 'catalog_flag', 'type' => 'select', 'title' => '�������� ������� ��� ��������� ��������', 'descr' => '<b>��</b> - ������� ����� ������� ��� ��������� ��������<br/><b>���</b> - ������� �� ����� ������� ��� ��������� ��������', 'values' => array ( 0 => '���', 1 => '��'), value => pluginGetVariable('basket','catalog_flag')));
array_push($cfgX, array('name' => 'catalog_activated', 'title' => "��������� ������� � �������� ��..", 'type' => 'select', 'descr' => '<b>���� �������</b> - "�������� � �������" ����� �������� ��� ���� ���������<br/><b>���� <i>xfields</i></b> - "�������� � �������" ����� ����� ������ �� ������, � ������� �������� ���������� ���� <b>> 0</b> (������ ����)', 'values' => array(0 => '���� �������', 1 => '���� xfields'), 'value' => pluginGetVariable('basket','catalog_activated')));
array_push($cfgX, array('name' => 'catalog_xfield', 'title' => "���� xfields", 'type' => 'select', 'descr' => '���� ��� ��������� "��������� ������� ��.."', 'values' => $xfCatList, 'value' => pluginGetVariable('basket','catalog_xfield')));
array_push($cfgX, array('name' => 'catalog_price', 'title' => "���� � �����", 'type' => 'select', 'descr' => '���� xfields � ����� ������', 'values' => $xfCatList, 'value' => pluginGetVariable('basket','catalog_price')));
array_push($cfgX, array('name' => 'catalog_itemname', 'type' => 'input', 'title' => '������ ��������� ������������ ������:', 'descr' => '��������� ����������:<br/><b>{title}</b> - ������������ �������� ��������<br/><b>{x:NAME}</b> (��� <b>NAME</b> - �������� ���� XFIELDS) - ������� ���. ����', 'html_flags' => 'style="width: 300px;"', 'value' => pluginGetVariable('basket','catalog_itemname')?pluginGetVariable('basket','catalog_itemname'):'{title}'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>������ � ���������</b>', 'entries' => $cfgX));
*/

$cfgX = array();
array_push($cfgX, array('name' => 'ntable_flag', 'type' => 'select', 'title' => '�������� ������� ��� ������ ���. ����� ������ ��������', 'descr' => '<b>��</b> - ������� ����� ������� ��� ��������� �������<br/><b>���</b> - ������� �� ����� ������� ��� ��������� �������', 'values' => array ( 0 => '���', 1 => '��'), value => pluginGetVariable('basket','ntable_flag')));
array_push($cfgX, array('name' => 'ntable_activated', 'title' => "��������� ������� � ������� ��..", 'type' => 'select', 'descr' => '<b>���� �������</b> - "�������� � �������" ����� �������� ��� ���� ���������<br/><b>���� <i>xfields</i></b> - "�������� � �������" ����� ����� ������ �� ������, � ������� �������� ���������� ���� <b>> 0</b> (������ ����)', 'values' => array(0 => '���� �������', 1 => '���� xfields'), 'value' => pluginGetVariable('basket','ntable_activated')));
array_push($cfgX, array('name' => 'ntable_xfield', 'title' => "���� xfields", 'type' => 'select', 'descr' => '���� ��� ��������� "��������� ������� ��.."', 'values' => $xfNTableList, 'value' => pluginGetVariable('basket','ntable_xfield')));
array_push($cfgX, array('name' => 'ntable_price', 'title' => "���� � �����", 'type' => 'select', 'descr' => '���� xfields � ����� ������', 'values' => $xfNTableList, 'value' => pluginGetVariable('basket','ntable_price')));
array_push($cfgX, array('name' => 'ntable_itemname', 'type' => 'input', 'title' => '������ ��������� ������������ ������:', 'descr' => '��������� ����������:<br/><b>{title}</b> - ������������ �������� ��������<br/><b>{xt:NAME}</b> (��� <b>NAME</b> - �������� ���� XFIELDS) - ������� ���. ���� <u>�������</u><br/><b>{x:NAME}</b> (��� <b>NAME</b> - �������� ���� XFIELDS) - ������� ���. ���� �� ������������ <u>�������</u>', 'html_flags' => 'style="width: 300px;"', 'value' => pluginGetVariable('basket','ntable_itemname')?pluginGetVariable('basket','ntable_itemname'):'{title}'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>������ � ��������� ���. ����� ������ ��������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'news_flag', 'type' => 'select', 'title' => '�������� ������� ��� ������ ���. ����� ������ ��������', 'descr' => '<b>��</b> - ������� ����� ������� ��� ��������� �������<br/><b>���</b> - ������� �� ����� ������� ��� ��������� �������', 'values' => array ( 0 => '���', 1 => '��'), value => pluginGetVariable('basket','news_flag')));
array_push($cfgX, array('name' => 'news_activated', 'title' => "��������� ������� � �������� ��..", 'type' => 'select', 'descr' => '<b>���� �������</b> - "�������� � �������" ����� �������� ��� ���� ��������<br/><b>���� <i>xfields</i></b> - "�������� � �������" ����� ����� ������ �� �������, � ������� �������� ���������� ���� <b>> 0</b> (������ ����)', 'values' => array(0 => '���� �������', 1 => '���� xfields'), 'value' => pluginGetVariable('basket','news_activated')));
array_push($cfgX, array('name' => 'news_xfield', 'title' => "���� xfields", 'type' => 'select', 'descr' => '���� ��� ��������� "��������� ������� ��.."', 'values' => $xfCatList, 'value' => pluginGetVariable('basket','news_xfield')));
array_push($cfgX, array('name' => 'news_price', 'title' => "���� � �����", 'type' => 'select', 'descr' => '���� xfields � ����� ������', 'values' => $xfCatList, 'value' => pluginGetVariable('basket','news_price')));
array_push($cfgX, array('name' => 'news_itemname', 'type' => 'input', 'title' => '������ ��������� ������������ ������:', 'descr' => '��������� ����������:<br/><b>{title}</b> - ������������ �������� ��������<br/><b>{x:NAME}</b> (��� <b>NAME</b> - �������� ���� XFIELDS) - ������� ���. ����', 'html_flags' => 'style="width: 300px;"', 'value' => pluginGetVariable('basket','ntable_itemname')?pluginGetVariable('basket','news_itemname'):'{title}'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>������ � ���. ������ ������ ��������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'feedback_form', 'type' => 'select', 'title' => '����� �������� ����� ��� ���������� ������', 'descr' => '������ <b>basket</b> �������� ������ �� ���������� ������� �������.<br/>�������� ������ ������������ ����� ����� �������� ����� ������� <b>feedback</b>.<br/>�������� ����� �������� ����� ����� ������� ����� ������������� �������� ������', 'values' => $feedbackFormList, value => pluginGetVariable('basket','feedback_form')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ����������</b>', 'entries' => $cfgX));



// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

