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
	
	if(isset($params['pid']))
		$pid = isset($params['pid'])?intval($params['pid']):'';
	else
		$pid = isset($_REQUEST['pid'])?intval($_REQUEST['pid']):'';
	
	if(empty($id))
		return redirect_forum(link_home());
	
	if($_SESSION['lock_passwd_'.$id] == true)
		return redirect_forum(link_forum($id));
	
	$lock_passwd = isset($_REQUEST['lock_passwd'])?secureinput($_REQUEST['lock_passwd']):'';
	
	$forum = $mysql->record('SELECT `lock_passwd` FROM `'.prefix.'_forum_forums` WHERE `id` = '.securemysql($id).' LIMIT 1');
	if(empty($forum['lock_passwd']) && $forum['lock_passwd'] <> 0)
		return redirect_forum(link_forum($id));
	
	if(isset($_REQUEST['submit'])){
		if(empty($lock_passwd)) $error_text['empty_passwd'] = true;
		if ($forum['lock_passwd'] === $lock_passwd){
			$_SESSION['lock_passwd_'.$id] = true;
			if(empty($pid))
				return redirect_forum(link_forum($id));
			else
				return redirect_forum(link_topic($pid, 'pid'));
		} else $error_text['error_passwd'] = true;
	}
	
	$tpath = locatePluginTemplates(array('lock_passwd'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['lock_passwd'].'lock_passwd.tpl');
	
	$tVars = array(
		'lock_passwd' => $lock_passwd,
		'error_text' => $error_text
	);
	
	$output = $xt->render($tVars);