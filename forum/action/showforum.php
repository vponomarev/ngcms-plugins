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
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):'';
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):'';
	
	if(isset($params['page']))
		$pageNo = isset($params['page'])?intval($params['page']):0;
	else
		$pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
	
	$url = pluginGetVariable('forum', 'url');
	
	if(checkLinkAvailable('forum', 'showforum')){
		if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
			return redirect_forum(generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'showforum', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)), intval($pageNo)));
	}
	
	$tpath = locatePluginTemplates(array('show_forum', ':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['show_forum'].'show_forum.tpl');
	
	$forum = $mysql->record('SELECT `title`, `description`, `keywords`, `int_topic`, `moderators`, `lock_passwd`, `redirect_url` FROM `'.prefix.'_forum_forums` WHERE `id` = '.securemysql($id).' LIMIT 1');
	if(empty($forum))
		return $output = information('Этого раздела не существует', $title = 'Информация');
	
	if((isset($forum['lock_passwd']) && $forum['lock_passwd']) && empty($_SESSION['lock_passwd_'.$id]))
		return redirect_forum(link_lock_passwd($id));
	
	if((isset($forum['redirect_url']) && $forum['redirect_url']))
		return redirect_forum($forum['redirect_url']);
	
	$moderators = unserialize($forum['moderators']);
	if(array_key_exists(strtolower($userROW['name']), $moderators)){
		$MODE_PS = $MODE_PERM[$id];
	}else
		$MODE_PS = array();
	
	if(empty($FORUM_PS[$id]['forum_read']))
		return $output = permissions_forum('Доступ в форум запрещен');
	
	$SYSTEM_FLAGS['info']['title']['item'] = $forum['title'];
	$SYSTEM_FLAGS['meta']['description'] = $forum['description'];
	$SYSTEM_FLAGS['meta']['keywords'] = $forum['keywords'];
	
	$limitCount = intval(pluginGetVariable('forum', 'forum_per_page'));
	
	if (($limitCount < 2)||($limitCount > 2000)) $limitCount = 2;
	
	$count = $forum['int_topic'];
	//$count = $mysql->result('SELECT COUNT(*) FROM `'.prefix.'_forum_topics` WHERE `fid` = '.securemysql($id));
	
	$countPages = ceil($count / $limitCount);
	if($countPages < $pageNo)
		return $output = information('Подстраницы не существует', $title = 'Информация');
	
	if ($pageNo < 1) $pageNo = 1;
	if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;
	
	$navigations = LoadVariables();
	
	if ($countPages > 1 && $countPages >= $pageNo){
		$paginationParams = checkLinkAvailable('forum', 'showforum')?
			array('pluginName' => 'forum', 'pluginHandler' => 'showforum', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)):
			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showforum'), 'xparams' => array('id' => $id), 'paginator' => array('page', 1, false));
		//$pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
		$pages = generatePagination_forum($countPages, $pageNo, $paginationParams, $navigations, true);
	}
	
	foreach($mysql->select('SELECT t.*, u.avatar FROM `'.prefix.'_forum_topics` as t
		LEFT JOIN '.prefix.'_users AS u ON u.id = t.l_author_id
		WHERE t.`fid` = '.securemysql($id).' ORDER BY t.`l_date` DESC LIMIT '.$limitStart.', '.$limitCount) as $row)
		$topics[$row['pinned']][$row['id']] = $row;
	
	foreach($topics[1] as $row){
		
		$countPagesTopic = ceil(($row['int_post']+1) / $limitCount);
		
		$paginationParamsTopic = checkLinkAvailable('forum', 'showtopic')?
			array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => array('id' => $row['id']), 'xparams' => array(), 'paginator' => array('page', 0, false)):
			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showtopic'), 'xparams' => array('id' => $row['id']), 'paginator' => array('page', 1, false));
		
		$tEntry_imp[] = array (
			'topic_name' => $row['title'],
			'topic_link' => link_topic($row['id']),
			'topic_author' => $row['author'],
			'int_post' => $row['int_post'],
			'int_views' => $row['int_views'],
			'status' => status_forum($row['l_date']),
			'state' => $row['state'],
			'last_post_forum' => array(
				'topic_name' => $row['title'],
				'topic_link' => link_topic($row['l_post'], 'pid').'#'.$row['l_post'],
				'date' => $row['l_date'],
				'l_author_link' => link_profile($row['l_author_id'], '', $row['l_author']),
				'l_author' => $row['l_author'],
				'l_author_avatar' => array(
					'true' => ($row['avatar'] != '')?1:0,
					'print' => ($row['avatar'] != '')?avatars_url.'/'.$row['avatar']:avatars_url,
				)
			),
			'pag_topic' => ($countPagesTopic > 1)?generatePagination_forum($countPagesTopic, 1, $paginationParamsTopic, $navigations, true):0,
		);
	}
	//print "<pre>".var_export($row, true)."</pre>";
	foreach($topics[0] as $row){
		$countPagesTopic = ceil(($row['int_post']+1) / $limitCount);
		
		$paginationParamsTopic = checkLinkAvailable('forum', 'showtopic')?
			array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => array('id' => $row['id']), 'xparams' => array(), 'paginator' => array('page', 0, false)):
			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showtopic'), 'xparams' => array('id' => $row['id']), 'paginator' => array('page', 1, false));
		
		//print "<pre>".var_export($row, true)."</pre>";
		
		if(isset($MODE_PS) && $MODE_PS)
			$topic_send = $MODE_PS['m_topic_send'];
		elseif($FORUM_PS[$result['fid']]['topic_send'])
			$topic_send = true;
		else $topic_send = false;
		
		if(isset($MODE_PS) && $MODE_PS){
			$topic_modify = $MODE_PS['m_topic_modify'];
		}elseif($FORUM_PS[$id]['topic_modify']){
			$topic_modify = true;
		}elseif($FORUM_PS[$id]['topic_modify_your']){
			if($userROW['id'] == $row['author_id'])
				$topic_modify = true;
			else
				$topic_modify = false;
		} else $topic_modify = false;
		
		if(isset($MODE_PS) && $MODE_PS){
			$topic_remove = $MODE_PS['m_topic_remove'];
		}elseif($FORUM_PS[$id]['topic_remove']){
			$topic_remove = true;
		}elseif($FORUM_PS[$id]['topic_remove_your']){
			if($userROW['id'] == $row['author_id'])
				$topic_remove = true;
			else
				$topic_remove = false;
		} else $topic_remove = false;
		
		
		$tEntry[] = array (
			'topic_name' => $row['title'],
			'topic_link' => link_topic($row['id']),
			'topic_author' => $row['author'],
			'int_post' => $row['int_post'],
			'int_views' => $row['int_views'],
			'status' => status_forum($row['l_date']),
			'state' => $row['state'],
			
			'topic_send' => $topic_send,
			
			'topic_remove' => $topic_remove,
			
			'topic_modify' => $topic_modify,
			'topic_modify_link' => link_topic_modify($row['id']),
			
			'last_post_forum' => array(
				'topic_name' => $row['title'],
				'topic_link' => link_topic($row['l_post'], 'pid').'#'.$row['l_post'],
				'date' => $row['l_date'],
				'l_author_link' => link_profile($row['l_author_id'], '', $row['l_author']),
				'l_author' => $row['l_author'],
				'l_author_avatar' => array(
					'true' => ($row['avatar'] != '')?1:0,
					'print' => ($row['avatar'] != '')?avatars_url.'/'.$row['avatar']:avatars_url,
				)
			),
			'pag_topic' => ($countPagesTopic > 1)?generatePagination_forum($countPagesTopic, 1, $paginationParamsTopic, $navigations, true):0,
		);
	}
	
	print "<pre>".var_export($MODE_PS, true)."</pre>";
	print "<pre>".var_export($FORUM_PS[$id], true)."</pre>";
	
	//'topic_modify' => (isset($MODE_PS) && $MODE_PS)?$MODE_PS['m_topic_modify']:$FORUM_PS[$id]['topic_modify'],
	
	$tVars = array(
		'entries' => isset($tEntry)?$tEntry:'',
		'entries_imp' => isset($tEntry_imp)?$tEntry_imp:'',
		
		'pages' => isset($pages)?$pages:'',
		
		'send_topic' => link_add_topic($id),
		'link_rss' => link_rss($id),
		'home_link' => link_home(),
		
		'topic_send' => (isset($MODE_PS) && $MODE_PS)?$MODE_PS['m_topic_send']:$FORUM_PS[$id]['topic_send'],
		
		'forum_name' => $forum['title'],
		'forum_description' => $forum['description'],
		'tpl' => $tpath['url::'],
		'local' => array(
				'num_guest_loc' => $viewers['num_guest_loc'],
				'num_user_loc' => $viewers['num_user_loc'],
				'num_bot_loc' => $viewers['num_bot_loc'],
				'list_loc_user' => $viewers['list_loc_user'],
				'list_loc_bot' => $viewers['list_loc_bot']
		),
		'prevlink' => array(
					'true' => !empty($limitStart)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'showforum')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'showforum', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showforum'), 'xparams' => array('id' => $id), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)), 
												isset($navigations['prevlink'])?$navigations['prevlink']:''
											)
					),
		),
		'nextlink' => array(
					'true' => ($prev + 2 <= $countPages)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'showforum')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'showforum', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showforum'), 'xparams' => array('id' => $id), 'paginator' => array('page', 1, false)), $prev+2), 
												isset($navigations['nextlink'])?$navigations['nextlink']:''
											)
					),
		),
	);
	
	$output = $xt->render($tVars);