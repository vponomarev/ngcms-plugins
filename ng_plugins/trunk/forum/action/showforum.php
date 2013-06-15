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
	
	$forum = $mysql->record('SELECT `title`, `description`, `int_topic` FROM `'.prefix.'_forum_forums` WHERE `id` = '.securemysql($id).' LIMIT 1');
	if(empty($forum))
		return $output = information('Этого раздела не существует', $title = 'Информация');
	
	$SYSTEM_FLAGS['info']['title']['item'] = $forum['title'];
	
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
		WHERE `fid` = '.securemysql($id).' ORDER BY `l_date` DESC LIMIT '.$limitStart.', '.$limitCount) as $row)
		$topics[$row['pinned']][$row['id']] = $row;
	
	foreach($topics[1] as $row){
		
		$countPagesTopic = ceil(($row['int_post']+1) / $limitCount);
		
		$paginationParamsTopic = checkLinkAvailable('forum', 'showtopic')?
			array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => array('id' => $row['id']), 'xparams' => array(), 'paginator' => array('page', 0, false)):
			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showtopic'), 'xparams' => array('id' => $row['id']), 'paginator' => array('page', 1, false));
		
		$important[] = array (
			'Ttitle' => $row['title'],
			'topic_link' => link_topic($row['id']),
			'author' => $row['author'],
			'int_post' => $row['int_post'],
			'int_views' => $row['int_views'],
			'status' => status_forum($row['l_date']),
			'state' => $row['state'],
			'last_post_forum' => array(
				'topic_name' => $row['title'],
				'topic_link' => link_topic($row['l_post'], 'pid').'#'.$row['l_post'],
				'date' => $row['l_date'],
				'profile_link' => link_profile($row['l_author_id'], '', $row['l_author']),
				'profile' => $row['l_author'],
				'profile_avatar' => array(
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
		
		$tEntry[] = array (
			'Ttitle' => $row['title'],
			'topic_link' => link_topic($row['id']),
			'author' => $row['author'],
			'int_post' => $row['int_post'],
			'int_views' => $row['int_views'],
			'status' => status_forum($row['l_date']),
			'state' => $row['state'],
			'last_post_forum' => array(
				'topic_name' => $row['title'],
				'topic_link' => link_topic($row['l_post'], 'pid').'#'.$row['l_post'],
				'date' => $row['l_date'],
				'profile_link' => link_profile($row['l_author_id'], '', $row['l_author']),
				'profile' => $row['l_author'],
				'profile_avatar' => array(
					'true' => ($row['avatar'] != '')?1:0,
					'print' => ($row['avatar'] != '')?avatars_url.'/'.$row['avatar']:avatars_url,
				)
			),
			'pag_topic' => ($countPagesTopic > 1)?generatePagination_forum($countPagesTopic, 1, $paginationParamsTopic, $navigations, true):0,
		);
	}
	
	$tVars = array(
		'entries_imp' => isset($important)?$important:'',
		'entries' => isset($tEntry)?$tEntry:'',
		'pages' => array(
			'true' => (isset($pages) && $pages)?1:0,
			'print' => isset($pages)?$pages:''
		),
		'addtopic' => link_add_topic($id),
		'link_rss' => link_rss($id),
		'home_link' => link_home(),
		'Ftitle' => $forum['title'],
		'Fdesc' => $forum['description'],
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