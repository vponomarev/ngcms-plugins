<?php

// #====================================================================================#
// # ������������ �������: auth_vb [ vBulletin auth DB module ]                         #
// # ��������� � ������������� �: NGCMS                                                 #
// # �����: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#

// #====================================================================================#
// # ������� ������ �������                                                             #
// #====================================================================================#

plugins_load_config();

$db_update = array(
 array(
  'table'  => 'users',
  'action' => 'modify',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'vb_userid', 'type' => 'int'),
  )
 ),
);

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('auth_vb', $db_update)) {
		plugin_mark_installed('auth_vb');
	}	
} else {
	$text = '������ <b>auth_vb</b> ��������� ������������ �� ������ vBulletin � �������� ��������� �� ��� ����������� �������������.<br />��������� ����� ������� � ��� �������� ����������� ��� ����������� �������������<br />';
	generate_install_page('auth_vb', $text);
}

?>