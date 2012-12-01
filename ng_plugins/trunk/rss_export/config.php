<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();


$xfEnclosureValues = array( '' => '');
//
// IF plugin 'XFIELDS' is enabled - load it to prepare `enclosure` integration
if (getPluginStatusActive('xfields')) {
	include_once(root."/plugins/xfields/xfields.php");

	// Load XFields config
	if (is_array($xfc=xf_configLoad())) {
		foreach ($xfc['news'] as $fid => $fdata) {
			$xfEnclosureValues[$fid] = $fid.' ('.$fdata['title'].')';
		}
	}
}

// For example - find 1st category with news for demo URL
$demoCategory = '';
foreach ($catz as $scanCat) {
	if ($scanCat['posts'] > 0) {
		$demoCategory = $scanCat['alt'];
		break;
	}
}

// Fill configuration parameters
$cfg = array();

$cfgX = array();
array_push($cfg, array('descr' => '<b>������ �������� �������� � ������� RSS</b><br>������ ����� �������� �������� �� ������: <b>'.generatePluginLink('rss_export', '', array(), array(), true, true).(($demoCategory != '')?'<br/>����� �������� ��� ��������� <i>'.$catz[$demoCategory]['name'].'</i>: '.generatePluginLink('rss_export', 'category', array('category' => $demoCategory), array(), true, true):'')));
array_push($cfgX, array('type' => 'select','name' => 'feed_title_format', 'title' => '������ ��������� ����� ��������', 'descr' => '<b>����</b> - ������������ ��������� �����<br><b>����+���������</b> - ������������ ��������� �����+�������� ��������� (��� ������ �������� �� ���������� ���������)<br><b>������</b> - ��������� ������������ ����', 'values' => array ( 'site' => '����', 'site_title' => '����+���������', 'handy' => '������'), value => pluginGetVariable('rss_export','feed_title_format')));
array_push($cfgX, array('type' => 'input', 'name' => 'feed_title_value', 'title' => '��� ��������� ����� ��������', 'descr' => '��������� ������������ � ������ ������ ������� <b>"������"</b> � �������� ��������� �����', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_export','feed_title_value')));
array_push($cfgX, array('type' => 'select','name' => 'news_title', 'title' => '������ ��������� �������', 'descr' => '<b>��������</b> - � ��������� ����������� ������ �������� �������<br><b>��������� :: ��������</b> - � ��������� ����������� ��� ��������� ��� � �������� �������', 'values' => array ( '0' => '��������', '1' => '��������� :: ��������'), value => pluginGetVariable('rss_export','news_title')));
array_push($cfgX, array('type' => 'input', 'name' => 'news_count', 'title' => '���-�� �������� ��� ���������� � �����', value => pluginGetVariable('rss_export','news_count')));
array_push($cfgX, array('type' => 'select','name' => 'use_hide', 'title' => '������������ ��� <b>[hide] ... [/hide]</b>', 'descr' => '<b>��</b> - ����� ���������� ����� <b>hide</b> �� ������������<br><b>���</b> - ����� ���������� ����� <b>hide</b> ������������', 'values' => array ( '0' => '���', '1' => '��'), value => pluginGetVariable('rss_export','use_hide')));
array_push($cfgX, array('type' => 'select','name' => 'content_show', 'title' => '��� ����������� �������', 'descr' => '��� ���������� ������� ����� ������ ���������� ����� ������������ ������ �������, �������������� ����� RSS', 'values' => array ( '0' => '��������+�������', '1' => '������ ��������', '2' => '������ �������'), value => pluginGetVariable('rss_export','content_show')));
array_push($cfgX, array('type' => 'input','name' => 'truncate', 'title' => '�������� ��������� ����������', 'descr' => '���-�� �������� �� ������� ����� ���������� ��������� � ����� ����������.<br/>�������� �� ���������: <b>0</b> - �� ��������', value => intval(pluginGetVariable('rss_export','truncate'))));
array_push($cfgX, array('type' => 'input', 'name' => 'delay', 'title' => '�������� ������ �������� � �����', 'descr' => '�� ������ ������ ����� (<b>� �������</b>) �� ������� ����� ������������� ����� �������� � RSS �����',value => pluginGetVariable('rss_export','delay')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>����� ���������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'xfEnclosureEnabled', 'title' => "��������� ���� 'Enclosure' ��������� ������ ������� xfields", 'descr' => "<b>��</b> - �������� ���������<br /><b>���</b> - ��������� ���������</small>", 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => intval(pluginGetVariable($plugin,'xfEnclosureEnabled'))));
array_push($cfgX, array('name' => 'xfEnclosure', 'title' => "ID ���� ������� <b>xfields</b>, ������� ����� �������������� ��� ��������� ���� <b>Enclosure</b>", 'type' => 'select', 'values' => $xfEnclosureValues, 'value' => pluginGetVariable($plugin,'xfEnclosure')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ���� <b>enclosure</b></b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => "������������ ����������� ������<br /><small><b>��</b> - ����������� ������������<br /><b>���</b> - ����������� �� ������������</small>", 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => intval(pluginGetVariable($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => "������ ���������� ����<br /><small>(����� ������� ������ ���������� ���������� ����. �������� �� ���������: <b>60</b>)</small>", 'type' => 'input', 'value' => intval(pluginGetVariable($plugin,'cacheExpire'))?pluginGetVariable($plugin,'cacheExpire'):'60'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� �����������</b>', 'entries' => $cfgX));

// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>