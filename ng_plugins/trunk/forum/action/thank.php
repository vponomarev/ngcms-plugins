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
	
	$tpath = locatePluginTemplates(array('thank'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['thank'].'thank.tpl');
	
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):0;
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	if(empty($id))
		return $output = information('Этой страницы не существует', $title = 'Информация');
	
	//if(!is_array($userROW))
	//	return $output = information('У вас нет прав доступа', $title = 'Информация');
	
	$limitCount = intval(pluginGetVariable('forum', 'thank_per_page'));
	
	if (($limitCount < 2)||($limitCount > 2000)) $limitCount = 2;
	
	if(isset($params['page']))
		$pageNo = isset($params['page'])?intval($params['page']):0;
	else
		$pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
	
	$count = $mysql->result('SELECT COUNT(*) FROM `'.prefix.'_forum_thank` WHERE to_author_id = '.securemysql($id));
	
	$countPages = ceil($count / $limitCount);
	if($countPages < $pageNo)
		return $output = information('Подстраницы не существует', $title = 'Информация');
	
	if ($pageNo < 1) $pageNo = 1;
	if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;
	
	if ($countPages > 1 && $countPages >= $pageNo){
		$paginationParams = checkLinkAvailable('forum', 'thank')?
			array('pluginName' => 'forum', 'pluginHandler' => 'thank', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)):
			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'thank'), 'xparams' => array('id' => $id), 'paginator' => array('page', 1, false));
		
		$navigations = LoadVariables();
		$pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
	}
	
	$name = $mysql->result('SELECT name FROM '.prefix.'_users WHERE id = '.securemysql($id).' LIMIT 1');
	if(empty($name))
		return $output = information('Такого пользователя нет', $title = 'Информация');
	
	$SYSTEM_FLAGS['info']['title']['others'] = $name;
	
	foreach ($mysql->select('SELECT th.tid, th.pid, th.author, th.author_id, th.c_data, th.message, th.to_author_id, t.title FROM '.prefix.'_forum_thank AS th 
		LEFT JOIN '.prefix.'_forum_topics AS t ON t.id = th.tid
		WHERE th.to_author_id = '.securemysql($id).' 
		ORDER BY th.id DESC
		LIMIT '.$limitStart.', '.$limitCount
	) as $row)
	{
		$tEntry[] = array (
			'profile_link' => link_profile($row['author_id'], '', $row['author']),
			'profile' => $row['author'],
			'message' => bb_codes($row['message']),
			'Ttitle' => $row['title'],
			'topic_link' => link_topic($row['pid'], 'pid').'#'.$row['pid'],
			'pid' => $row['pid'],
			'c_data' => $row['c_data'],
		);
	
	}
	
	$tVars = array(
		'entries' => isset($tEntry)?$tEntry:'',
		'pages' => array(
			'true' => (isset($pages) && $pages)?1:0,
			'print' => isset($pages)?$pages:''
		),
		'to_author' => $name,
		'int_thank' => $count,
		'home_link' => link_home(),
		'prevlink' => array(
					'true' => !empty($limitStart)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'rep')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'rep', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'rep'), 'xparams' => array('id' => $id), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)), 
												isset($navigations['prevlink'])?$navigations['prevlink']:''
											)
					),
		),
		'nextlink' => array(
					'true' => ($prev + 2 <= $countPages)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'rep')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'rep', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'rep'), 'xparams' => array('id' => $id), 'paginator' => array('page', 1, false)), $prev+2), 
												isset($navigations['nextlink'])?$navigations['nextlink']:''
											)
					),
		),
	);
	
	$output = $xt->render($tVars);