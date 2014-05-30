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
	
	$tpath = locatePluginTemplates(array('search'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['search'].'search.tpl');
	
	if(empty($GROUP_PS['group_search']))
		return $output = permissions_forum('Пользование поиском для вас запрещено');
	
	if(isset($_REQUEST['submit']) && $_REQUEST['submit']){
		$keywords = secure_search_forum($_REQUEST['keywords']);
		$forum_id = securenum($_REQUEST['forum_id']);
		if(empty($forum_id))
			$forum_id = 0;
		
		$search_in = secure_search_forum($_REQUEST['search_in']);
		if(empty($search_in))
			$search_in = 'all';
		
		$search = substr($keywords, 0, 64);
 		if( strlen($search) < 3 )
			return $output = information('Слишком короткое слово', $title = 'Информация');
		
		$keywords = array();
		
		$get_url = $search;
		
		$search = str_replace(" +", " ", $search);
		$stemmer = new Lingua_Stem_Ru();
		
		$tmp = explode( " ", $search );
		
		foreach ( $tmp as $wrd )
			$keywords[] = $stemmer->stem_word($wrd);
		
		$string = implode( "* ", $keywords );
		$string = $string.'*';
		
		$text = implode('|', $keywords);
		
		if(isset($params['page']))
			$pageNo = isset($params['page'])?intval($params['page']):0;
		else
			$pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
		
		$limitCount = intval(pluginGetVariable('forum', 'search_per_page'));
		
		if (($limitCount < 2)||($limitCount > 2000)) $limitCount = 2;
		
		if($forum_id)
			$forums_id = " AND b.`fid` = '{$forum_id}'";
		else
			$forums_id = NULL;
		
		switch($search_in){
			case 'all':$sql_count = "SELECT COUNT(*) FROM ".prefix."_forum_posts AS a INNER JOIN ".prefix."_forum_topics AS b
									ON a.tid = b.id 
									WHERE MATCH (a.message, b.title) AGAINST ('{$string}' IN BOOLEAN MODE){$forums_id}";
									break;
			case 'post':$sql_count = "SELECT COUNT(*) FROM ".prefix."_forum_posts a INNER JOIN ".prefix."_forum_topics AS b
									ON a.tid = b.id 
									WHERE MATCH (a.message) AGAINST ('{$string}' IN BOOLEAN MODE){$forums_id}";
									break;
			case 'topic':$sql_count = "SELECT COUNT(*) FROM ".prefix."_forum_topics AS b
									WHERE MATCH (title) AGAINST ('{$string}' IN BOOLEAN MODE){$forums_id}";
									break;
		}
		
		$count = $mysql->result($sql_count);
		
		$countPages = ceil($count / $limitCount);
		if($countPages < $pageNo)
			return $output = information('Подстраницы не существует', $title = 'Информация');
		
		if ($pageNo < 1) $pageNo = 1;
		if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;
		
		if ($countPages > 1 && $countPages >= $pageNo){
			//$paginationParams = checkLinkAvailable('forum', 'search')?
			//	array('pluginName' => 'forum', 'pluginHandler' => 'search', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)):
			//	array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'search'), 'xparams' => array(), 'paginator' => array('page', 1, false));
			
			$paginationParams = checkLinkAvailable('forum', 'search')?
				array('pluginName' => 'forum', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url, 'forum_id' => $forum_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'xparams' => array(), 'paginator' => array('page', 0, false)):
				array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'search'), 'xparams' => array('keywords' => $get_url, 'forum_id' => $forum_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'paginator' => array('page', 1, false));
			
			$navigations = LoadVariables();
			$pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
		}
		
		
		
		
		switch($search_in){
			case 'all':$sql_two = 'SELECT a.`id`, a.`message`, a.`tid`, a.`author`, b.`id`, b.`title` 
									FROM `'.prefix.'_forum_posts` AS a INNER JOIN `'.prefix.'_forum_topics` AS b
									ON a.`tid` = b.`id` 
									WHERE MATCH (a.`message`, b.`title`) AGAINST (\''.$string.'\' IN BOOLEAN MODE)'.$forums_id.'
									ORDER BY MATCH (a.`message`, b.`title`) AGAINST (\''.$string.'\' IN BOOLEAN MODE) DESC
									LIMIT '.$limitStart.', '.$limitCount; break;
			case 'post':$sql_two = 'SELECT a.`id`, a.`message`, a.`tid`, a.`author`, b.`fid`, b.`title` 
									FROM `'.prefix.'_forum_posts` AS a INNER JOIN `'.prefix.'_forum_topics` AS b
									ON a.`tid` = b.`id` 
									WHERE MATCH (a.`message`) AGAINST (\''.$string.'\' IN BOOLEAN MODE)'.$forums_id.'
									ORDER BY MATCH (a.`message`) AGAINST (\''.$string.'\' IN BOOLEAN MODE) DESC
									LIMIT '.$limitStart.', '.$limitCount; break;
			case 'topic':$sql_two = 'SELECT `id`, `title`, `author`, `fid` 
									FROM `'.prefix.'_forum_topics` AS b
									WHERE MATCH (`title`) AGAINST (\''.$string.'\' IN BOOLEAN MODE)'.$forums_id.'
									ORDER BY MATCH (`title`) AGAINST (\''.$string.'\' IN BOOLEAN MODE) DESC 
									LIMIT '.$limitStart.', '.$limitCount; break;
		}
		
		foreach ($mysql->select($sql_two) as $row_two){
			/* print '<pre>';
			print_r ($row_two);
			print '</pre>'; */
			
			$tEntry[] = array (
				'subject' => $row_two['title'],
				'topic_link' => link_topic($row_two['id']),
				'user' => $row_two['author'],
				'message' => preg_replace("/\b(".$text.")(.*?)\b/i", "<span style='color:red; font-weight:bold'>\\0</span>", bb_codes($row_two['message']))
			);
		}
		
		if( empty($row_two) )
			return $output = information('По вашему запросу <b>'.$get_url.'</b> ничего не найдено', $title = 'Информация');
	}else{
		foreach ($mysql->select('SELECT `id`, `title` FROM `'.prefix.'_forum_forums` ORDER BY `position`') as $row){
			$tEntry[] = array (
				'forum_id' => $row['id'],
				'forum_name' => $row['title'],
			);
		}
	}
	
	
	$tVars = array(
		'entries' => isset($tEntry)?$tEntry:'',
		'submit' => (isset($_REQUEST['submit']) && $_REQUEST['submit'])?0:1,
		'pages' => array(
			'true' => (isset($pages) && $pages)?1:0,
			'print' => isset($pages)?$pages:''
		),
		'prevlink' => array(
					'true' => !empty($limitStart)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'search')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url?$get_url:'', 'forum_id' => $forum_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'search'), 'xparams' => array('keywords' => isset($get_url)?$get_url:'', 'forum_id' => isset($forum_id)?$forum_id:'', 'search_in' => isset($search_in)?$search_in:'', 'submit'=> 'Отправить'), 'paginator' => array('page', 1, false)), 
													$prev = floor((isset($limitStart) && $limitStart)?$limitStart:10 / (isset($limitCount) && $limitCount)?$limitCount:'5')), 
												isset($navigations['prevlink'])?$navigations['prevlink']:''
											)
					),
		),
		'nextlink' => array(
					'true' => ($prev + 2 <= $countPages)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'search')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url, 'forum_id' => $forum_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'search'), 'xparams' => array('keywords' => $get_url, 'forum_id' => $forum_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'paginator' => array('page', 1, false)), $prev+2), 
												isset($navigations['nextlink'])?$navigations['nextlink']:''
											)
					),
		),
	);
	
	$output = $xt->render($tVars);