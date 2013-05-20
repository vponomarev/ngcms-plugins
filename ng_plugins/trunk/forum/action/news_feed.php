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
	
	$tpath = locatePluginTemplates(array( 'news_feed'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	
	$limitCount = intval(pluginGetVariable('forum', 'news_per_page'));
	
	if (($limitCount < 2) or ($limitCount > 2000)) $limitCount = 2;
	
	$count = $mysql->result('SELECT COUNT(*) FROM `'.prefix.'_forum_news`');
	
	if(isset($params['page']))
		$pageNo = isset($params['page'])?intval($params['page']):0;
	else
		$pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
	
	$countPages = ceil($count / $limitCount);
	if($countPages < $pageNo)
		return $output = information('Подстраницы не существует', $title = 'Информация');
	
	if ($pageNo < 1) $pageNo = 1;
	if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;
	
	if ($countPages > 1 && $countPages >= $pageNo){
		$paginationParams = checkLinkAvailable('forum', 'news_feed')?
			array('pluginName' => 'forum', 'pluginHandler' => 'news_feed', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)):
			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'news_feed'), 'xparams' => array(), 'paginator' => array('page', 1, false));
		
		$navigations = LoadVariables();
		$pages = generatePagination($pageNo, 1, $countPages, 5, $paginationParams, $navigations, true);
	}
	
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_news ORDER BY id DESC LIMIT '.$limitStart.', '.$limitCount) as $row){
		$tEntry[] = array(
			'title' => $row['title'],
			'content' => bb_codes($row['content']),
			'link_news' => link_news($row['id']),
		);
	}
	
	$xt = $twig->loadTemplate($tpath['news_feed'].'news_feed.tpl');
	$tVars = array(
		'entries' => $tEntry,
		'local' => array(
			'num_guest_loc' => $viewers['num_guest_loc'],
			'num_user_loc' => $viewers['num_user_loc'],
			'num_bot_loc' => $viewers['num_bot_loc'],
			'list_loc_user' => $viewers['list_loc_user'],
			'list_loc_bot' => $viewers['list_loc_bot']
		),
		'pages' => array(
			'true' => (isset($pages) && $pages)?1:0,
			'print' => isset($pages)?$pages:''
		),
		'prevlink' => array(
					'true' => !empty($limitStart)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'news_feed')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'news_feed', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'news_feed'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)), 
												isset($navigations['prevlink'])?$navigations['prevlink']:''
											)
					),
		),
		'nextlink' => array(
					'true' => ($prev + 2 <= $countPages)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'news_feed')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'news_feed', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'news_feed'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev+2), 
												isset($navigations['nextlink'])?$navigations['nextlink']:''
											)
					),
		),
	);
	
	$output = $xt->render($tVars);