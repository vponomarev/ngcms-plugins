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
array_push($cfg, array('descr' => '<b>������ ������������ �������� �������� � ������� RSS</b><br>� ������� �� ����������� ���������� �������� � ������� RSS, ������ ������ ��������� ����� ����������� ��� ��������� ��������� ��������'));
array_push($cfg, array('type' => 'select','name' => 'feed_title_format', 'title' => '������ ��������� ����� ��������', 'descr' => '<b>����</b> - ������������ ��������� �����<br><b>����+���������</b> - ������������ ��������� �����+�������� ��������� (��� ������ �������� �� ���������� ���������)<br><b>������</b> - ��������� ������������ ����', 'values' => array ( 'site' => '����', 'site_title' => '����+���������', 'handy' => '������'), value => extra_get_param('rss_export','feed_title_format')));
array_push($cfg, array('type' => 'input', 'name' => 'feed_title_value', 'title' => '��� ��������� ����� ��������', 'descr' => '��������� ������������ � ������ ������ ������� <b>"������"</b> � �������� ��������� �����', 'html_flags' => 'style="width: 250px;"', 'value' => extra_get_param('rss_export','feed_title_value')));
array_push($cfg, array('type' => 'select','name' => 'news_title', 'title' => '������ ��������� �������', 'descr' => '<b>��������</b> - � ��������� ����������� ������ �������� �������<br><b>��������� :: ��������</b> - � ��������� ����������� ��� ��������� ��� � �������� �������', 'values' => array ( '0' => '��������', '1' => '��������� :: ��������'), value => extra_get_param('rss_export','news_title')));
array_push($cfg, array('type' => 'input', 'name' => 'news_count', 'title' => '���-�� �������� ��� ���������� � �����', value => extra_get_param('rss_export','news_count')));
array_push($cfg, array('type' => 'select','name' => 'use_hide', 'title' => '������������ ��� <b>[hide] ... [/hide]</b>', 'descr' => '<b>��</b> - ����� ���������� ����� <b>hide</b> �� ������������<br><b>���</b> - ����� ���������� ����� <b>hide</b> ������������', 'values' => array ( '0' => '���', '1' => '��'), value => extra_get_param('rss_export','use_hide')));
array_push($cfg, array('type' => 'select','name' => 'content_show', 'title' => '��� ����������� �������', 'descr' => '��� ���������� ������� ����� ������ ���������� ����� ������������ ������ �������, �������������� ����� RSS', 'values' => array ( '0' => '��������+�������', '1' => '������ ��������', '2' => '������ �������'), value => extra_get_param('rss_export','content_show')));

// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>