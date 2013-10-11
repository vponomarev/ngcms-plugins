<?php
if (!defined('NGCMS'))
{
	die ('HAL');
}

function plugin_simple_title_pro_install($action) {
	
	$checkVer = explode('.', substr(engineVersion, 0,5));
	if($checkVer['0'] == 0 && $checkVer['1'] == 9 && $checkVer['2'] = 3)
		$check = true;
	else
		$check = false;
		
	$db_update = array(
		array(
			'table'		=> 'simple_title_pro',
			'action'	=> 'cmodify',
			'key'		=> 'primary key(id), KEY `cat_id` (`cat_id`), KEY `news_id` (`news_id`), KEY `static_id` (`static_id`)',
			'fields'	=> array(
				array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'),
				array('action'	=> 'cmodify', 'name' => 'title', 'type' => 'varchar(100)', 'params' => 'NOT NULL DEFAULT \'\''),
				array('action'	=> 'cmodify', 'name' => 'cat_id', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
				array('action'	=> 'cmodify', 'name' => 'news_id', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
				array('action'	=> 'cmodify', 'name' => 'static_id', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
			)
		)
	);

	switch ($action){
		case 'confirm':
			if($check)
				generate_install_page('simple_title_pro', '����� ����������');
			else
				msg(array("type" => "error", "info" => "������ CMS �� ������������� ����������<br />� ��� ����������� ".$checkVer['0'].".".$checkVer['1'].".<b>".$checkVer['2']."</b>. ��������� 0.9.3!"));
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('simple_title_pro', $db_update, 'install', ($action=='autoapply')?true:false)){
				plugin_mark_installed('simple_title_pro');
				$_SESSION['simple_title_pro']['info'] = '�� ����� � ��������� � ������ ���.<br/>
				���������� � ����� ������� �� �������������, ��� ��������� ��������� �� �������� � �� ������ ������ ��� � ����<br />
				�������� �� ���� ������ ���������� �����������. ��� ��������� ������, ��� �������� �� �����, �� ����� �������� � �� ������� �����. ��� ��������: ��� �������������� ��� � ���� ������ ����� ������������� ��������!<br/>
				�� ������, ����������� ���������� �� �������� �� ������ ��� ��� �� ICQ: 209388634 ��� jabber: rozard@ngcms.ru
				';
			} else {
				return false;
			}
			
			$params = array(
				'c_title' => '%home% / %cat% [/ %num%]',
				'n_title' => '%home% / %cat% / %title%  [/ %num%]',
				'm_title' => '%home% %num%',
				'static_title' => '%home% / %static%',
				'num_title' => '�������� %count%',
				'o_title' => '%home% / %other% %html% [/ %num%]',
				'e_title' => '%home% / %other%',
				'html_secure' => '/ %html%',
				'cache' => '1', 
				'num_cat' => 20,
				'num_news' => 20,
				'num_static' => 20,
			);
			foreach ($params as $k => $v) {
				extra_set_param('simple_title_pro', $k, $v);
			}
			extra_commit_changes();
			break;
	}
	return true;
}