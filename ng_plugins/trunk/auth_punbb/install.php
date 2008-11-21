<?php

// #====================================================================================#
// # ������������ �������: auth_punBB [ punBB auth DB module ]                          #
// # ��������� � ������������� �: Next Generation CMS                                   #
// # �����: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#

// #====================================================================================#
// # ������� ������ �������                                                             #
// #====================================================================================#

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

plugins_load_config();

$db_update = array(
 array(
  'table'  => 'users',
  'action' => 'modify',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'punbb_userid', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'punbb_newpw', 'type' => 'char(40)'),
  )
 ),
);

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('auth_punbb', $db_update)) {
		plugin_mark_installed('auth_punbb');
	}	
} else {
	$text = '������ <b>auth_punbb</b> ��������� ������������ �� ������ punBB � �������� ��������� �� ��� ����������� �������������.<br />��������� ����� ������� � ��� �������� ����������� ��� ����������� �������������<br />';
	generate_install_page('auth_punbb', $text);
}

