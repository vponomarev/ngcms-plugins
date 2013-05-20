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
	
	$tpath = locatePluginTemplates(array('addtopic'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['addtopic'].'addtopic.tpl');
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):0;
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	if(checkLinkAvailable('forum', 'newtopic')){
		if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
			return redirect_forum(link_add_topic($id));
	}
	
	if(!is_array($userROW))
		return $output = information('У вас нет прав доступа', $title = 'Информация');
	
	if(empty($id ))
		return $output = information('id раздела не указан не передан', $title = 'Информация');
	
	if(!$mysql->record('SELECT 1 FROM '.prefix.'_forum_forums WHERE id = '.securemysql($id).' LIMIT 1'))
		return $output = information('Этого раздела не существует', $title = 'Информация');
	
	$subject = isset($_REQUEST['subject'])?secureinput($_REQUEST['subject']):'';
	$message = isset($_REQUEST['message'])?secureinput($_REQUEST['message']):'';
	$time = time() + ($config['date_adjust'] * 60);
	
	if(isset($_REQUEST['submit'])){
		if(empty($subject)) $error_text[] = 'Тема пуста';
		if(empty($message)) $error_text[] = 'Сообщение пусто';
		
		if(empty($error_text)){
			$mysql->query('insert into '.prefix.'_forum_topics (
					author,
					author_id,
					title,
					c_data,
					l_date,
					l_author_id,
					l_author,
					fid
				)values(
					'.securemysql($userROW['name']).', 
					'.securemysql($userROW['id']).', 
					'.securemysql($subject).', 
					'.securemysql($time).', 
					'.securemysql($time).', 
					'.securemysql($userROW['id']).', 
					'.securemysql($userROW['name']).', 
					'.securemysql($id).'
				)
			');
			$topic_id = $mysql->lastid('forum_topics');
			
			$mysql->query('insert into '.prefix.'_forum_posts (
					author, 
					author_id, 
					author_ip, 
					message, 
					c_data, 
					tid
				)values(
					'.securemysql($userROW['name']).', 
					'.securemysql($userROW['id']).', 
					'.securemysql($ip).', 
					'.securemysql($message).', 
					'.securemysql($time).', 
					'.securemysql($topic_id).'
				)
			');
			
			$post_id = $mysql->lastid('forum_posts');
			
			update_forum($topic_id, $subject, 1, $time, $post_id, $userROW['name'], $userROW['id'], $id);
			$mysql->query('UPDATE '.prefix.'_forum_topics SET l_post = '.securemysql($post_id).' WHERE id = '.securemysql($topic_id).' LIMIT 1');
			
			//Под удаление
			update_users_mes();
			
			if(isset($_REQUEST['subscribe']))
				subscribe($userROW['id'], $id);
			
			$file = forum_upload_files();
			
			if($file)
				$mysql->query('INSERT INTO '.prefix.'_forum_attach (
						tid,
						pid,
						c_data,
						file,
						size,
						location,
						author,
						author_id
					) values (
						'.securemysql($topic_id).',
						'.securemysql($post_id).',
						'.securemysql($time).',
						'.securemysql($file[0]).',
						'.securemysql($file[1]).',
						'.securemysql($file[2]).',
						'.securemysql($userROW['name']).',
						'.securemysql($userROW['id']).'
					)
				');
			
			generate_index_cache(true);
			
			return $output = announcement_forum('Данные внесены', link_forum($id), 2);
		}
	}
	$error_input = '';
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
	else $error_input = '';
	
	$tVars = array(
		'subject' => $subject,
		'message' => array(
			'true' => ($message)?1:0,
			'print' => $message
		),
		'preview' => array(
			'true' => isset($_REQUEST['preview'])?1:0,
			'print' => bb_codes($message)
		),
		'error' => array(
			'true' => ($error_input)?1:0,
			'print' => $error_input
		)
	);
	
	$output = $xt->render($tVars);
