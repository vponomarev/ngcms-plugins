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
		$id = isset($params['id'])?intval($params['id']):'';
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
	
	if(isset($params['metod']))
		$method = isset($params['metod'])?intval($params['metod']):0;
	else
		$method = isset($_REQUEST['metod'])?intval($_REQUEST['metod']):0;
	
	if(!is_array($userROW))
		return $output = information('� ��� ��� ���� �������', $title = '����������');
	
	switch($method){
		case 1:
			subscribe($userROW['id'], $id);
			return  $output = announcement_forum('�� �����������', link_topic($id), 0);
		break;
		case 2:
			$result = $mysql->result('SELECT 1 FROM '.prefix.'_forum_subscriptions WHERE tid = '.securemysql($id).' AND uid = '.securemysql($userROW['id']).' LIMIT 1');
			if(isset($result) && $result){
				$mysql->query('DELETE FROM '.prefix.'_forum_subscriptions WHERE tid = '.securemysql($id).' AND uid = '.securemysql($userROW['id']).'');
				return  $output = announcement_forum('�� ����������', link_topic($id), 0);
			} else return  $output = announcement_forum('�� � ��� �� ���������', link_topic($id), 0);
		break;
		default: return $output = information('������', $title = '����������');
	}