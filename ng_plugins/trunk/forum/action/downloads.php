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
	
	if(empty($id ))
		return $output = information('id ����� �� ������', $title = '����������');
	
	if(!is_array($userROW))
		return $output = information('������ ����� ����� ������ �����������������', $title = '����������');
	
	$row = $mysql->record('SELECT * FROM '.prefix.'_forum_attach WHERE id = '.securemysql($id).' LIMIT 1');
	
	if(empty($row))
		return $output = information('������ ����� ��� � ����', $title = '����������');
	
	$file = files_dir . 'forum/' . $row['location'] . '/' . $row['file'];
	if(!is_file($file)){
		//$mysql->query('DELETE FROM '.prefix.'_forum_attach WHERE id = '.securemysql($id).' LIMIT 1');
		return $output = information('������ ����� ��� � �� ���� ������', $title = '����������');
	}
	
	$mysql->query('UPDATE '.prefix.'_forum_attach SET downloads = downloads + 1 WHERE id = '.securemysql($id).' LIMIT 1');
	
	header('Content-Type: application/force-download');
	header ("Accept-Ranges: bytes");
	header ("Content-Length: ".filesize($file));  
	header('Content-Disposition: attachment; filename='.$row['file']);
	echo file_get_contents($file);
	exit;