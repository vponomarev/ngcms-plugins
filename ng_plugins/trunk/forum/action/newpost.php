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
	
	$tpath = locatePluginTemplates(array('addpost'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['addpost'].'addpost.tpl');
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):'';
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
	
	if(isset($params['pid']))
		$pid = isset($params['pid'])?intval($params['pid']):0;
	else
		$pid = isset($_REQUEST['pid'])?intval($_REQUEST['pid']):0;
	
	if(checkLinkAvailable('forum', 'addreply')){
		if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core'){
			if($pid)
				return redirect_forum(link_new_post($id, 'pid', $pid));
			else
				return redirect_forum(link_new_post($id));
			
		}
	}
	
	if(!is_array($userROW))
		return $output = information('У вас нет прав доступа', $title = 'Информация');
	
	if(empty($id ))
		return $output = information('id темы не указан не передан', $title = 'Информация');
	
	if(!$mysql->record('SELECT 1 FROM '.prefix.'_forum_posts WHERE tid = '.securemysql($id).' LIMIT 1'))
		return $output = information('Этой темы не существует', $title = 'Информация');
	
	if($userROW['status'] != 1){
		if(($mysql->result('SELECT state FROM '.prefix.'_forum_topics WHERE id = '.securemysql($id).' LIMIT 1')) == 'closed')
			return $output = information('Тема закрыта', $title = 'Информация');
	}
	
	$message = isset($_REQUEST['message'])?secureinput($_REQUEST['message']):'';
	if(empty($message)){
		if(isset($pid) && $pid){
			$rows = $mysql->record('SELECT author, message FROM '.prefix.'_forum_posts WHERE id = '.securemysql($pid).' LIMIT 1');
			if(isset($rows) && $rows)
				$message = '[quote='.$rows['author'].']'.$rows['message'].'[/quote]'."\n";
			else
				return $output = information('Цитаты не существует', $title = 'Информация');
		}
	}
	
	$time = time() + ($config['date_adjust'] * 60);
	
	if(isset($_REQUEST['submit'])){
		if(empty($message)) $error_text[] = 'Сообщение пусто';
		if (empty($error_text)){
			$row = $mysql->record('SELECT * FROM '.prefix.'_forum_posts WHERE tid = '.securemysql($id).' ORDER BY id DESC LIMIT 1');
			
			if($row['author_id'] == $userROW['id']){
				$mysql->query('UPDATE '.prefix.'_forum_posts SET 
					message = '.securemysql($row['message']."\n". '[color=red]Добавлено '.date("j-m-Y, H:i", $time)."[/color]\n".$message).'
					WHERE id = '.securemysql($row['id']).' LIMIT 1
				');
				
				$post_id = $row['id'];
			} else {
				$mysql->query('INSERT INTO '.prefix.'_forum_posts (
					author,
					author_id,
					message,
					author_ip,
					c_data,
					tid
				) values (
					'.securemysql($userROW['name']).',
					'.securemysql($userROW['id']).',
					'.securemysql($message).',
					'.securemysql($ip).',
					'.securemysql($time).',
					'.securemysql($id).')
				');
				
				$post_id = $mysql->lastid('forum_posts');
				$result = $mysql->record('SELECT fid, title FROM '.prefix.'_forum_topics WHERE id = '.securemysql($id).' LIMIT 1');
				update_users_mes();
				update_topic($time, $post_id, $userROW['name'], $userROW['id'], $id);
				update_forum($id, $result['title'], 0, $time, $post_id, $userROW['name'], $userROW['id'], $result['fid']);
				
				if(isset($_REQUEST['subscribe']))
					subscribe($userROW['id'], $id);
				
				generate_index_cache(true);
			}
			
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
						'.securemysql($id).',
						'.securemysql($post_id).',
						'.securemysql($time).',
						'.securemysql($file[0]).',
						'.securemysql($file[1]).',
						'.securemysql($file[2]).',
						'.securemysql($userROW['name']).',
						'.securemysql($userROW['id']).'
					)
				');
			
			send_subscribe($id, $post_id, $result['title'], $message, $userROW['name']);
			
			return $output = announcement_forum('Данные внесены', link_topic($post_id, 'pid').'#'.$post_id, 2);
		}
	}
	
	$limitCount = intval(pluginGetVariable('forum', 'newpost_per_page'));
	
	if (($limitCount < 2) or ($limitCount > 2000)) $limitCount = 2;
	
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_posts
		WHERE tid = '.securemysql($id).' ORDER BY id DESC 
		LIMIT '.$limitCount.'
	') as $row){
		$tEntry[] = array(
			'date' => show_date($row['c_data']),
			'author' => $row['author'],
			'message' => bb_codes($row['message']),
		
		);
	}
	
	$error_input = '';
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
	else $error_input = '';
	
	$tVars = array(
		'entries' => isset($tEntry)?$tEntry:'',
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