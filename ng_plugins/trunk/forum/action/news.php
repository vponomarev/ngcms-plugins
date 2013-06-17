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
	
	$tpath = locatePluginTemplates(array('news'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['news'].'news.tpl');
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):0;
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	if(empty($id))
		return $output = information('id ������� �� ������', $title = '����������');
	
	if(empty($GROUP_PERM[$GROUP_STATUS]['news']))
		return $output = permissions_forum('������ � ������� ��������');
	
	//if(!is_array($userROW))
	//	return $output = information('������ ������� ����� ������ ��������������!', $title = '����������');
	
	$row = $mysql->record('SELECT * FROM '.prefix.'_forum_news WHERE id = '.securemysql($id).' LIMIT 1');
	if(empty($row))
		return $output = information('����� ������� �� ����������', $title = '����������');
	
	$SYSTEM_FLAGS['info']['title']['others'] = $row['title'];
	
	$tVars = array(
		'title' => $row['title'],
		'content' => bb_codes($row['content']),
		'local' => array(
			'num_guest_loc' => $viewers['num_guest_loc'],
			'num_user_loc' => $viewers['num_user_loc'],
			'num_bot_loc' => $viewers['num_bot_loc'],
			'list_loc_user' => $viewers['list_loc_user'],
			'list_loc_bot' => $viewers['list_loc_bot']
		),
	);
	
	$output = $xt->render($tVars);