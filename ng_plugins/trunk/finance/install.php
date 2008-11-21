<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();
LoadPluginLang('finance', 'config');

/*

��������� ������� ������������ ��������� ��������� � ��������� ��

 array =
  array =
   table	- ������������ ������� SQL
   action	- �������� � ��������
   		create	- �������. ���� ���� - ������
   		cmodify - �������. ���� ���� - ����������
   		modify	- �������� ���� � �������. ���� ������� ��� - ������
   		drop	- ������� �������
   fields	- ������ � ������ �������
   	action		- ��������
   			create	- �������. ���� ���� - ������
   			cmodify - �������. ���� ���� - ��������
   			cleave	- �������. ���� ���� - �������� ��� ����
   			drop	- �������
	name		- �������� ����
	type		- ��� ����
	params		- ��������� �������� (� �������, 'not null auto_increment')


*/




$db_update = array(
 array(
  'table'  => 'users',
  'action' => 'modify',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'balance',  'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'balance1', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'balance2', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'balance3', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'balance4', 'type' => 'int', 'params' => 'default 0'),
  ) 
 ),
 array(
  'table'  => 'news',
  'action' => 'cmodify',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'fin_price', 'type' => 'char(40)', 'params' => "default ''"),
  )
 ),
 array(
  'table'  => 'balance_manager',
  'action' => 'cmodify',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'monetary', 'type' => 'int', 'params' => 'default "1"'),
    array('action' => 'cmodify', 'name' => 'type', 'type' => 'char(30)'),
    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
  )
 ),
 array(
  'table'  => 'subscribe_manager',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    // ID ������
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    // user_id = ��� ������������ ��� ���� ������� ��������
    array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int'),
    // special_access_type = �������, ��� ������ ��� �� �� �� � �� ����������� ����� (������, SMS ��� �����-�� �����������)
    array('action' => 'cmodify', 'name' => 'special_access_type', 'type' => 'int'),
    // ID ������ �� ������� ��� ������. ���� 0 - �� ������������
    array('action' => 'cmodify', 'name' => 'access_group_id', 'type' => 'int'),
    // ID �������� �� �������� ��� ������. ���� 0 - �� ������������.
    array('action' => 'cmodify', 'name' => 'access_element_id', 'type' => 'int'),
    // ���� ��������� ��������
    array('action' => 'cmodify', 'name' => 'subscription_date', 'type' => 'datetime'),
    // ���� �������� ��������
    array('action' => 'cmodify', 'name' => 'expiration_date', 'type' => 'datetime'),
   )
 ),
 array(
  'table'  => 'finance_history',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
    // ��� ��������: 1 - ������, 2 - ������
    array('action' => 'cmodify', 'name' => 'operation_type', 'type' => 'int'),
    // � ������ ������� �����������/����������� ������ (-1 - �� �������)
    array('action' => 'cmodify', 'name' => 'balance_no', 'type' => 'int', 'params' => 'default -1'),
    // ����� ��������/����������
    array('action' => 'cmodify', 'name' => 'sum', 'type' => 'int', 'params' => 'default 0'),
    // � ������ �������� - ��������� "����"
    // subscribe_ref - ������ �� ������� ������� subscribe_manager
    array('action' => 'cmodify', 'name' => 'subscribe_ref', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'special_access_type', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'access_group_id', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'access_element_id', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
   )
 ),
// array(
//  'table'  => 'pricelist',
//  'action' => 'cmodify',
//  'key'    => 'primary key(id)',
//  'fields' => array(
//    // ID ������
//    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
//    // ID ������ �� ������� ��� ������. ���� 0 - �� ������������
//    array('action' => 'cmodify', 'name' => 'access_group_id', 'type' => 'int'),
//    // ID �������� �� �������� ��� ������. ���� 0 - �� ������������.
//    array('action' => 'cmodify', 'name' => 'access_element_id', 'type' => 'int'),
//    // ��� ������������ ��������. ������ ��������� ������ � ���. �������� ����� ���� � ������ ����� - ������
//    array('action' => 'cmodify', 'name' => 'type', 'type' => 'char(30)'),
//    // ���� � ������� ������� ���� (��� ������ � �������)
//    array('action' => 'cmodify', 'name' => 'tprice', 'type' => 'int'),
//    // ���� � ������ ��� ������� ��������
//    array('action' => 'cmodify', 'name' => 'price', 'type' => 'int'),
//    // ��������
//    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
//   )
//  )
);


if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('finance', $db_update)) {
		plugin_mark_installed('finance');
	}
} else {
	$text = "������ <b>finance</b> ������������� ������� ������������ ��� ���������� ���������.<br />� �������������� �������, ��������������� ������ ��������, ����� ����������� ��������� ������ ��������, �������������� ����� � �.�.<br /><br />��������! ��� ��������� ������ ���������� ��������� � �� �������!";
	generate_install_page('finance', $text);
}

?>