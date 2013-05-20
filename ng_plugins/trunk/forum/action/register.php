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
	
	if(checkLinkAvailable('forum', 'register')){
		if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
			return redirect_forum(link_register());
	}
	
	if(is_array($userROW))
		return $output = information('���������������� �� ����� ����������������', $title = '����������');
	
	$tpath = locatePluginTemplates(array('register', 'captcha'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['register'].'register.tpl');
	
	$time = time() + ($config['date_adjust'] * 60);
	$name = isset($_REQUEST['name'])?$_REQUEST['name']:'';
	$mail = isset($_REQUEST['mail'])?secureinput($_REQUEST['mail']):'';
	$password = isset($_REQUEST['password'])?secureinput($_REQUEST['password']):'';
	$confirm = isset($_REQUEST['confirm'])?secureinput($_REQUEST['confirm']):'';
	$captcha = isset ($_REQUEST['captcha'])?intval($_REQUEST['captcha']):'';
	$forum_captcha_sess = isset ($_REQUEST['forum_captcha_sess'])?intval($_REQUEST['forum_captcha_sess']):'';
	
	if(isset($_REQUEST['submit'])){
		if( preg_match("/[^(\w)|(\x7F-\xFF)]/", $name) )
			$error_text['name'] = '�� ����������� ���������� �������';
		
		if(strlen($name) < 3 )
			$error_text['name'] = '������� �������� �����';
		
		if(strlen($name) > 30 )
			$error_text['name'] = '����� �� ������ ��������� 30 �����';
		
		if(strlen($password) < 3)
			$error_text['password'] = '������� �������� ������';
		elseif ($password != $confirm)
			$error_text['password'] = '������ �� ���������';
		
		if(empty($mail))
			$error_text['mail'] = 'e-mail �� ������';
		else {
			if( !filter_var($mail, FILTER_VALIDATE_EMAIL) )
				$error_text['mail'] = '�������� e-mail';
		}
		
		if($captcha == $_SESSION['captcha'])
			unset($_SESSION['captcha']);
		else
			$error_text['captcha'] = '����������� ��� ������ �����������.';
		
		if ($forum_captcha_sess != '1'){
			add_banned_users();
			$error_text['bot'] = '����� ����������� ���������';
		}
		
		$row = $mysql->record('SELECT * from '.prefix.'_users WHERE lower(name) = '.securemysql(strtolower($name)).' or mail= '.securemysql($mail));
		if(is_array($row)){
			if(strtolower($row['mail']) == strtolower($mail)) {
				$error_text['mail'] = '���� e-mail ��� ��������������';
			}
			$error_text['name'] = '���� ����� ��� ��������������';
		}
		
		$regstatus = 4;
		
		if( empty($error_text) ){
			$mysql->query('INSERT INTO '.prefix.'_users (
				name,
				pass, 
				mail, 
				status, 
				reg, 
				last) VALUES (
				'.securemysql($name).', 
				'.securemysql(EncodePassword($password)).', 
				'.securemysql($mail).', 
				'.securemysql($regstatus).', 
				'.securemysql($time).', 
				\'\')
			');
			return $output = announcement_forum('�� ��������������� � ������ ����� �� �����', link_login(), 2);
		}
	}
	
	if(isset($error_text) && is_array($error_text)){
		foreach($error_text as $key => $error )
				$error_input[$key] = msg(array("type" => "error", "text" => $error), 0, 2);
	} else $error_input = '';
	
	$rand = rand(00000, 99999);
	
	$_SESSION['captcha'] = $rand;
	
	$tVars = array(
		'name' => $name,
		'mail' => $mail,
		'url_captcha' => admin_url.'/captcha.php',
		'error' => array(
			'name' => array(
				'true' => isset($error_input['name'])?1:0,
				'print' =>  isset($error_input['name'])?$error_input['name']:'',
			),
			'mail' => array(
				'true' => isset($error_input['mail'])?1:0,
				'print' =>  isset($error_input['mail'])?$error_input['mail']:'',
			),
			'password' => array(
				'true' => isset($error_input['password'])?1:0,
				'print' =>  isset($error_input['password'])?$error_input['password']:'',
			),
			'captcha' => array(
				'true' => isset($error_input['captcha'])?1:0,
				'print' =>  isset($error_input['captcha'])?$error_input['captcha']:'',
			),
			'bot' => array(
				'true' => isset($error_input['bot'])?1:0,
				'print' =>  isset($error_input['bot'])?$error_input['bot']:'',
			)
		),
	);
	
	$output = $xt->render($tVars);