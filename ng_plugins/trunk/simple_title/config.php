<?php
plugins_load_config();
$cfg = array();
array_push($cfg, array('name' => 'c_title', 'title' => 'Title ��� ���������', 'descr' => '��������� ������ %1% � %3%','type' => 'input', 'value' => extra_get_param('simple_title','c_title')));
array_push($cfg, array('name' => 'n_title', 'title' => 'Title ��� �������', 'descr' => '��� ��� ���������','type' => 'input', 'value' => extra_get_param('simple_title','n_title')));
array_push($cfg, array('name' => 'm_title', 'title' => 'Title ��� ������� ��������<br>������ %3%','type' => 'input', 'value' => extra_get_param('simple_title','m_title')));
array_push($cfg, array('name' => 'static_title', 'title' => 'Title ��� ����������� ��������<br>������ %3%','type' => 'input', 'value' => extra_get_param('simple_title','static_title')));

array_push($cfg, array('descr' => '�����:<br><b>%1%</b> - ��� ���������<br><b>%2%</b> - ��� �������<br><b>%3%</b> - ��������� �����<br><b>%4%</b> - ��������� ����������� ��������<br>'));
if ($_REQUEST['action'] == 'commit') {
	commit_plugin_config_changes('simple_title', $cfg);
	print "Changes commited: <a href='admin.php?mod=extra-config&plugin=simple_title'>�����</a>\n";
} else {
	generate_config_page('simple_title', $cfg);
}
?>