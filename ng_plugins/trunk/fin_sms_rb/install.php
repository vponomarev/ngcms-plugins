<?php

//
// Configuration file for plugin
//

plugins_load_config();
LoadPluginLang('fin_sms_rb', 'config');



$db_update = array(
 array(
  'table'  => 'smsrb_price',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    // ID ������
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    array('action' => 'cmodify', 'name' => 'country', 'type' => 'char(60)'),
    array('action' => 'cmodify', 'name' => 'operator', 'type' => 'char(60)'),
    array('action' => 'cmodify', 'name' => 'num', 'type' => 'char(20)'),
    array('action' => 'cmodify', 'name' => 'cost', 'type' => 'float'),
    array('action' => 'cmodify', 'name' => 'partnercost', 'type' => 'float'),
   )
 ),
 array(
  'table'  => 'smsrb_incoming',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    // ID ������
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    array('action' => 'cmodify', 'name' => 'smsid', 'type' => 'char(25)'),
    array('action' => 'cmodify', 'name' => 'operator', 'type' => 'char(60)'),
    array('action' => 'cmodify', 'name' => 'num', 'type' => 'char(20)'),
    array('action' => 'cmodify', 'name' => 'cost', 'type' => 'float'),
    array('action' => 'cmodify', 'name' => 'msg', 'type' => 'char(255)'),
    array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
    array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'actdt', 'type' => 'datetime'),
    // ��� �������:
    // 0 - ������ ������
    array('action' => 'cmodify', 'name' => 'service_type', 'type' => 'int', 'params' => 'default 0'),
    array('action' => 'cmodify', 'name' => 'scode', 'type' => 'char(30)'),
    // �������� ���������
    array('action' => 'cmodify', 'name' => 'answer', 'type' => 'char(255)'),
   )
 ),
 array(
  'table'  => 'smsrb_history',
  'action' => 'cmodify',
  'key'    => 'primary key(id)',
  'fields' => array(
    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'),
    array('action' => 'cmodify', 'name' => 'user_id', 'type' => 'int'),
    array('action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'),
    array('action' => 'cmodify', 'name' => 'success', 'type' => 'int', params => 'default 0'),
    array('action' => 'cmodify', 'name' => 'code_id', 'type' => 'int', params => 'default 0'),
    array('action' => 'cmodify', 'name' => 'action', 'type' => 'char(20)'),
    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text'),
   )
 ),
);

if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	if (fixdb_plugin_install('fin_sms_rb', $db_update)) {
		plugin_mark_installed('fin_sms_rb');
	}
} else {
	$text = "������ <b>fin_sms_rb</b> ��������� ������� �� ��� ���� ��������, ���������� �� ��������-SMS ������� <a href='http://russianbilling.com/' target='_blank'>RussianBilling.com</a><br/><br/>��� ��������� ��� ������� ������ ���:<br/><b>1.</b> ����� ���������� ������� ��� ���������� ���������� ������ <b>finance</b> - ��� ���� ������ �������� �� �����.<br/><b>2.</b> ������ ��� ��������� ������ ��������� � ��.";
	generate_install_page('fin_sms_rb', $text);
}

?>