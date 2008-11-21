<?php

// #====================================================================================#
// # ������������ �������: nsched [ News SCHEDuller ]                                   #
// # ��������� � ������������� �: Next Generation CMS                                   #
// # �����: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#

// #====================================================================================#
// # ������� ������ �������                                                             #
// #====================================================================================#
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

plugins_load_config();

LoadPluginLang('voting', 'install');


plugins_load_config();

LoadPluginLang('nsched', 'install');

$db_update = array(
 array(
  'table'  => 'news',
  'action' => 'modify',
  'fields' => array(
    array('action' => 'drop', 'name' => 'nsched_activate'),
    array('action' => 'drop', 'name' => 'nsched_deactivate'),
  )
 ),
);

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('nsched', $db_update, 'deinstall')) {
		plugin_mark_deinstalled('nsched');
	}	
} else {
	$text = '��� �������� ������� <b>nsched</b> ��� ���������� � ���������� ����������/�������� �������� ����� ��������!<br><br>';
	generate_install_page('nsched', $text, 'deinstall');
}
