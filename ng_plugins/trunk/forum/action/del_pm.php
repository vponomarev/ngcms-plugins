<?php
	/*
	=====================================================
	 NG FORUM v.alfa
	-----------------------------------------------------
	 Author: Nail' R. Davydov (ROZARD)
	-----------------------------------------------------
	 Jabber: ROZARD@ya.ru
	 E-mail: ROZARD@list.ru
	-----------------------------------------------------
	 � ��������� ����������� ������� �� ������ 
	 ������������. ��, ��� �������� � ������, ������ 
	 ���������� � ������. :))
	-----------------------------------------------------
	 ������ ��� ������� ���������� �������
	=====================================================
	*/
	if (!defined('NGCMS')) die ('HAL');
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):0;
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	if(empty($id))
		return $output = information('id ��������� �� �������', $title = '����������');
	
	if(!is_array($userROW))
		return $output = information('� ��� ��� ���� �������', $title = '����������');
	
	$mysql->query('DELETE FROM '.prefix.'_pm WHERE ((`from_id`='.securemysql($userROW['id']).' AND `folder`=\'outbox\') OR (`to_id`='.securemysql($userROW['id']).') AND `folder`=\'inbox\') AND id = \''.intval($id).'\' LIMIT 1');
	
	return $output = announcement_forum('��������� �������', link_list_pm(0,0,'inbox'), 2);