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
array_push($cfg, array('descr' => '<b>������ �������� ����� �������� ��� ��������� ������� �ndex</b><br>������ ����� �������� �������� �� ������: <b>'.generatePluginLink('rss_yandex', '', array(), array(), true, true).(($demoCategory != '')?'<br/>����� �������� ��� ��������� <i>'.$catz[$demoCategory]['name'].'</i>: '.generatePluginLink('rss_yandex', 'category', array('category' => $demoCategory), array(), true, true):'')));
array_push($cfgX, array('type' => 'input',	'name' => 'feed_title', 'title' => '�������� RSS ������ ��� ������ �����', 'descr' => '���������� ����������:<br/><b>{{siteTitle}}</b> - �������� �����<br/>�������� �� ���������: <b>{{siteTitle}}</b>', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_yandex','feed_title')?pluginGetVariable('rss_yandex','feed_title'):'{{siteTitle}}'));
array_push($cfgX, array('type' => 'text',	'name' => 'news_title', 'title' => '��������� (��������) �������', 'descr' => '���������� ����������:<br/><b>{{siteTitle}}</b> - �������� �����<br/><b>{{newsTitle}}</b> - ��������� �������<br/><b>{{masterCategoryName}}</b> - �������� <b>�������</b> ��������� �������<br/>�������� �� ���������: <b>{% if masterCategoryName %}{{masterCategoryName}} :: {% endif %}{{newsTitle}}</b>', 'html_flags' => 'style="width: 350px;"', 'value' => pluginGetVariable('rss_yandex','news_title')?pluginGetVariable('rss_yandex','news_title'):'{% if masterCategoryName %}{{masterCategoryName}} :: {% endif %}{{newsTitle}}'));
array_push($cfgX, array('type' => 'select',	'name' => 'full_format', 'title' => '������ ��������� ������� ������ ������� ��� ����� �ndex', 'descr' => '<b>������</b> - ��������� ������ ������ ����� �������<br><b>��������+������</b> - ��������� �������� + ������ ����� �������', 'values' => array ( '0' => '������', '1' => '������+��������'), value => pluginGetVariable('rss_yandex','full_format')));
array_push($cfgX, array('type' => 'input',	'name' => 'news_age', 'title' => '������������ ���� �������� �������� ��� ���������� � �����', 'descr' => '�ndex ����������� ������� �� ������ <b>8 �����</b>.<br/>�������� �� ���������: 10 �����', value => pluginGetVariable('rss_yandex','news_age')));
array_push($cfgX, array('type' => 'input',	'name' => 'delay', 'title' => '�������� ������ �������� � �����', 'descr' => '�� ������ ������ ����� (<b>� �������</b>) �� ������� ����� ������������� ����� �������� � RSS �����',value => pluginGetVariable('rss_yandex','delay')));
array_push($cfg,  array('mode' => 'group',	'title' => '<b>����� ���������</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('type' => 'input', 'name' => 'feed_image_title', 'title' => '��������� (title) ��� ��������', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_yandex','feed_image_title')));
array_push($cfgX, array('type' => 'input', 'name' => 'feed_image_link', 'title' => 'URL � ������������ ��������', 'descr' => '����������� ������ �������� - 100 �������� �� ������������ �������', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_yandex','feed_image_link')));
array_push($cfgX, array('type' => 'input', 'name' => 'feed_image_url', 'title' => '������ (link) ��� �������� �� ����� �� �������', 'descr' => '������ - URL ������ �����', 'html_flags' => 'style="width: 250px;"', 'value' => pluginGetVariable('rss_yandex','feed_image_url')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>����������� ��������</b>', 'entries' => $cfgX));


$cfgX = array();
array_push($cfgX, array('name' => 'xfEnclosureEnabled', 'title' => "��������� ���� 'Enclosure' ��������� ������ ������� xfields", 'descr' => "<b>��</b> - �������� ���������<br /><b>���</b> - ��������� ���������</small>", 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => intval(pluginGetVariable($plugin,'xfEnclosureEnabled'))));
array_push($cfgX, array('name' => 'xfEnclosure', 'title' => "ID ���� ������� <b>xfields</b>, ������� ����� �������������� ��� ��������� ���� <b>Enclosure</b>", 'type' => 'select', 'values' => $xfEnclosureValues, 'value' => pluginGetVariable($plugin,'xfEnclosure')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ���� <b>enclosure</b> �� ���� xfields</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'textEnclosureEnabled', 'title' => "����� � ���� 'Enclosure' ���� ����������� �� ������ ������� (��������� HTML ��� &lt;img&gt;)", 'descr' => "<b>��</b> - �������� ��� �����������<br /><b>���</b> - �� ��������</small>", 'type' => 'select', 'values' => array ( '1' => '��', '0' => '���'), 'value' => intval(pluginGetVariable($plugin,'textEnclosureEnabled'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>��������� ���� <b>enclosure</b> �� ������ �������</b>', 'entries' => $cfgX));

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