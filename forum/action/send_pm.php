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
	 © Настоящий программист никогда не ставит 
	 комментариев. То, что писалось с трудом, должно 
	 пониматься с трудом. :))
	-----------------------------------------------------
	 Данный код защищен авторскими правами
	=====================================================
	*/
	if (!defined('NGCMS')) die ('HAL');
	
	if(empty($GROUP_PS['group_pm']))
		return $output = permissions_forum('У вас нет доступа к сообщениям');
	
	$tpath = locatePluginTemplates(array('send_pm'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['send_pm'].'send_pm.tpl');
	
	if(!is_array($userROW))
		return $output = information('У вас нет прав доступа', $title = 'Информация');
	
	$title = isset($_REQUEST['title'])?secureinput($_REQUEST['title']):'';
	$sendto  = isset($_REQUEST['sendto'])?trim($_REQUEST['sendto']):'';
	$message = isset($_REQUEST['message'])?secureinput($_REQUEST['message']):'';
	$savemessage = isset($_REQUEST['savemessage'])?intval($_REQUEST['savemessage']):'';
	$time = time() + ($config['date_adjust'] * 60);
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):0;
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	if(isset($params['reply']))
		$reply = isset($params['reply'])?intval($params['reply']):0;
	else
		$reply = isset($_REQUEST['reply'])?intval($_REQUEST['reply']):0;
	
	if(isset($params['quote']))
		$quote = isset($params['quote'])?intval($params['quote']):0;
	else
		$quote = isset($_REQUEST['quote'])?intval($_REQUEST['quote']):0;
	
	if($reply or $quote){
		$row = $mysql->record('select * from '.prefix.'_pm WHERE id = '.($quote?securemysql($quote):securemysql($reply)).' AND from_id = '.securemysql($id).' AND to_id = '.securemysql($userROW['id']).' LIMIT 1');
		if(empty($row))
			return $output = information('Ошибка в сообщении', $title = 'Информация');
			
		$title = 'Re:'.$row['subject'];
		if($quote && empty($message))
			$message = '[quote]'.$row['message'].'[/quote]';
	}
	
	if(isset($_REQUEST['submit'])){
		if(empty($message)) $error_text[] = 'Сообщение пусто';
		if(empty($title)) $error_text[] = 'Заголовок пустой';
		if(empty($sendto)) $error_text[] = 'Отправитель не указан';
		
		if(!$torow = $mysql->record('select * from '.prefix.'_users WHERE '.(is_numeric($sendto)?'id = '.securemysql($sendto):'name = '.securemysql($sendto)).' LIMIT 1'))
			$error_text[] = 'Отправитель не найден';
		
		if(is_numeric($sendto)){
			if($sendto == $userROW['id'])
				return $output = information('Самому себе отправлять не нужно', $title = 'Информация');
		} else {
			if($sendto == $userROW['name'])
				return $output = information('Самому себе отправлять не нужно', $title = 'Информация');
		}
		
		if (empty($error_text)){
			$mysql->query('insert into '.uprefix.'_pm (
					from_id,
					to_id,
					date,
					subject,
					message,
					folder
				) VALUES (
					'.securemysql($userROW['id']).',
					'.securemysql($torow['id']).',
					'.securemysql($time).',
					'.securemysql($title).',
					'.securemysql($message).',
					\'inbox\'
				)
			');
			
			if($savemessage)
				$mysql->query('insert into '.uprefix.'_pm (
					from_id,
					to_id,
					date,
					subject,
					message,
					viewed,
					folder
				) VALUES (
					'.securemysql($userROW['id']).',
					'.securemysql($torow['id']).',
					'.securemysql($time).',
					'.securemysql($title).',
					'.securemysql($message).',
					\'1\',
					\'outbox\'
				)
			');
				
			return $output = announcement_forum('Данные внесены', link_list_pm(0,0,'inbox'), 2);
		}
	}
	
	if($id){
		$row_2 = $mysql->record('select * from '.prefix.'_users WHERE id = '.securemysql($id).' LIMIT 1');
		$sendto = $row_2['name'];
	}
	
	$error_input = '';
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
	else $error_input = '';
	
	$tVars = array(
		'message' => $message,
		'title' => $title,
		'sendto' => $sendto,
		'error' => array(
			'true' => ($error_input)?1:0,
			'print' => $error_input
		)
	);
	
	$output = $xt->render($tVars);