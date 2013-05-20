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
	
	$tpath = locatePluginTemplates(array('send_rep'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['send_rep'].'send_rep.tpl');
	
	if(isset($params['pid']))
		$pid = isset($params['pid'])?intval($params['pid']):0;
	else
		$pid = isset($_REQUEST['pid'])?intval($_REQUEST['pid']):0;
	
	if(empty($pid))
		return $output = information('��������� �� �������', $title = '����������');
	
	if(!is_array($userROW))
		return $output = information('� ��� ��� ���� �������', $title = '����������');
	
	$row = $mysql->record('SELECT * FROM '.prefix.'_forum_posts WHERE id = '.securemysql($pid).' LIMIT 1');
	
	$row_2 = $mysql->result('SELECT 1 FROM '.prefix.'_users WHERE id = '.securemysql($row['author_id']).' LIMIT 1');
	if(empty($row_2))
		return $output = information('������ ������������ ���', $title = '����������');
	
	if($mysql->result('SELECT 1 FROM '.prefix.'_forum_thank WHERE pid = '.securemysql($pid).' and  author_id = '.securemysql($userROW['id']).' LIMIT 1'))
		return $output = announcement_forum('������ �������', link_topic($pid, 'pid').'#'.$pid, 2);
	
	if(empty($row))
		return $output = information('������ ��������� �� ����������', $title = '����������');
	
	if($userROW['id'] == $row['author_id'])
		return $output = information('�� ���� ������', $title = '����������');
	
	$mysql->query('insert into '.prefix.'_forum_thank (
			tid, 
			pid, 
			c_data, 
			message, 
			to_author_id, 
			author, 
			author_id
		) VALUES (
			'.securemysql($row['tid']).', 
			'.securemysql($pid).', 
			'.securemysql($row['c_data']).', 
			'.securemysql($row['message']).', 
			'.securemysql($row['author_id']).', 
			'.securemysql($userROW['name']).', 
			'.securemysql($userROW['id']).'
		)
	');
	
	$int_thank = $mysql->result('SELECT COUNT(*) FROM '.prefix.'_forum_thank WHERE to_author_id = '.securemysql($row['author_id']));
	
	$mysql->query('UPDATE '.prefix.'_users SET int_thank = '.securemysql($int_thank).' WHERE id = '.securemysql($row['author_id']).' LIMIT 1');
	
	return $output =  announcement_forum('������ �������', link_topic($pid, 'pid').'#'.$pid, 2);