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
	
	$tpath = locatePluginTemplates(array('show_new'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['show_new'].'show_new.tpl');
	
	if(isset($params['s']))
		$s = isset($params['s'])?secureinput($params['s']):'';
	else
		$s = isset($_REQUEST['s'])?secureinput($_REQUEST['s']):'';
	
	$time = time() + ($config['date_adjust'] * 60) - 86400;
	
	switch($s){
		case 'show_new':
			if(!is_array($userROW))
				return $output = information('� ��� ��� ���� �������', $title = '����������');
			
			$SYSTEM_FLAGS['info']['title']['others'] = '����� ���������';
			$where_1 = '`l_date` >= '.securemysql($userROW['last']).'';
			$where_2 = 't.l_date >= '.securemysql($userROW['last']).' ORDER BY t.`l_date` DESC';
		break;
		case 'show_24';
			$SYSTEM_FLAGS['info']['title']['others'] = '��������� ���������';
			$where_1 = '`l_date` >= '.securemysql($time).'';
			$where_2 = 't.l_date >= '.securemysql($time).' ORDER BY t.`l_date` DESC';
		break;
		default: return $output = information('������ ��������', $title = '����������', true);
	}
	
	$limitCount = intval(pluginGetVariable('forum', 'act_per_page'));
	
	if (($limitCount < 2)||($limitCount > 2000)) $limitCount = 2;
	
	if(isset($params['page']))
		$pageNo = isset($params['page'])?intval($params['page']):0;
	else
		$pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
	
	$count = $mysql->result('SELECT COUNT(*) FROM `'.prefix.'_forum_topics` WHERE '.$where_1.'');
	
	$countPages = ceil($count / $limitCount);
	if($countPages < $pageNo)
		return $output = information('����������� �� ����������', $title = '����������');
	
	if ($pageNo < 1) $pageNo = 1;
	if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;
	
	if ($countPages > 1 && $countPages >= $pageNo){
		$paginationParams = checkLinkAvailable('forum', 'act')?
			array('pluginName' => 'forum', 'pluginHandler' => 'act', 'params' => array('s' => $s), 'xparams' => array(), 'paginator' => array('page', 0, false)):
			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'act'), 'xparams' => array('s' => $s), 'paginator' => array('page', 1, false));
		
		$navigations = LoadVariables();
		$pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
	}
	
	foreach ($mysql->select('SELECT t.`id` as tid, t.`author`, t.`title` as Ttitle, t.`l_date`, t.`l_author_id`, t.`l_author`, t.`int_post`, t.`l_post`, t.`state`, f.`id` as fid, f.`title` as Ftitle
			FROM `'.prefix.'_forum_topics` AS t
			LEFT JOIN '.prefix.'_forum_forums AS f ON f.id = t.fid
			WHERE '.$where_2.'
			LIMIT '.$limitStart.', '.$limitCount) as $row){
		$tEntry[] = array (
			'forum_link' => link_forum($row['fid']),
			'forum_name' => $row['Ftitle'],
			'subject' => $row['Ttitle'],
			'topic_link' => link_topic($row['tid']),
			'user' => $row['author'],
			'num_replies' => $row['int_post'],
			'status' => status_forum($row['l_date']),
			'closed' => $row['state'],
			'last_post_forum' => array(
				'topic_link' => link_topic($row['l_post'], 'pid').'#'.$row['l_post'],
				'date' => $row['l_date'],
				'profile_link' => link_profile($row['l_author_id'], '', $row['l_author_id']),
				'profile' => $row['l_author'],
			),
		);
	}
	
	$tVars = array(
		'entries' => isset($tEntry)?$tEntry:'',
		'pages' => array(
			'true' => (isset($pages) && $pages)?1:0,
			'print' => isset($pages)?$pages:''
		),
		'home_link' => link_home(),
		'prevlink' => array(
					'true' => !empty($limitStart)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'act')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'act', 'params' => array('s' => $s), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'act'), 'xparams' => array('s' => $s), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)), 
												isset($navigations['prevlink'])?$navigations['prevlink']:''
											)
					),
		),
		'nextlink' => array(
					'true' => ($prev + 2 <= $countPages)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'act')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'act', 'params' => array('s' => $s), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'act'), 'xparams' => array('s' => $s), 'paginator' => array('page', 1, false)), $prev+2), 
												isset($navigations['nextlink'])?$navigations['nextlink']:''
											)
					),
		),
	);
	
	$output = $xt->render($tVars);