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
	
	if(checkLinkAvailable('forum', 'login')){
		if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
			return redirect_forum(link_login());
	}
	
	$tpath = locatePluginTemplates(array('login'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['login'].'login.tpl');
	
	$time = time() + ($config['date_adjust'] * 60);
	
	$username = isset($_REQUEST['username'])?secureinput($_REQUEST['username']):'';
	$password = isset($_REQUEST['password'])?secureinput($_REQUEST['password']):'';
	$password = EncodePassword($password);
	
	if(is_array($userROW))
		return $output = information('�� � ��� ��� ������������', $title = '����������');
	
	if(isset($_REQUEST['submit'])){
		if(!empty($_REQUEST['forum_captcha_sess'])){
			$sql = 'select * from '.prefix.'_users WHERE name = '.securemysql($username).' and pass='.securemysql($password).' limit 1';
			$row = $mysql->record($sql);
			
			if(isset($row) && $row){
				$auth_db->save_auth($row);
				return $output = announcement_forum('�� ����� �� �����', link_home(), 2, true);
			}else{
				$error_text[] = '������������ ����� ��� ������';
				
				add_banned_users();
				
				if($ban[$ip] == '1')
					$error_text[] = '������ ��������))<br /> �������� 2 �������!!!';
				elseif($ban[$ip] == '2')
					$error_text[] = '������ ��������))<br /> ������� �� �������!!!';
				elseif($ban[$ip] == '3')
					$error_text[] = '� ���� ��� �� � �������!!! <br />��� ���)))';
			}
		} else {
			add_banned_users();
			
			if($ban[$ip] == '1')
				return $output = information('����� ������, �� ��������)))<br /> � ��� �������� 2 �������!!!');
			elseif($ban[$ip] == '2')
				return $output = information('������ ���� �����)))<br /> � ��� �������� 1 �������!!!');
			elseif($ban[$ip] == '3')
				return $output = information('� �� ���� ����������)))<br /> �� ���������� ���!!!');
		}
	}
	
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
	else $error_input = '';
	
	$tVars = array(
		'error' => $error_input,
		'username' => $username,
	);
	
	$output = $xt->render($tVars);