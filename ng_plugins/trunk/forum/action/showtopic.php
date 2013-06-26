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
	
	$tpath = locatePluginTemplates(array('show_topic', ':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):0;
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	if(isset($params['page']))
		$pageNo = isset($params['page'])?intval($params['page']):0;
	else
		$pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
	
	if(isset($params['pid']))
		$pid = isset($params['pid'])?intval($params['pid']):0;
	else
		$pid = isset($_REQUEST['pid'])?intval($_REQUEST['pid']):0;
	
	if(isset($params['act']))
		$act = isset($params['act'])?intval($params['act']):'';
	else
		$act = isset($_REQUEST['act'])?descript($_REQUEST['act']):'';
	
	$url = pluginGetVariable('forum', 'url');
	
	if(checkLinkAvailable('forum', 'showtopic')){
		if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
		if($id)
			return redirect_forum(generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => $search_p, 'xparams' => array(), 'paginator' => array('page', 0, false)), intval($pageNo)));
		elseif($pid)
			return redirect_forum(link_topic($pid, 'pid'));
	}
	
	$limitCount = intval(pluginGetVariable('forum', 'topic_per_page'));
	
	if (($limitCount < 2) or ($limitCount > 2000)) $limitCount = 2;
	
	if($pid){
		$id = $mysql->result('SELECT tid FROM '.prefix.'_forum_posts WHERE id= '.securemysql($pid).' LIMIT 1', -1);
		if(empty($id))
			return $output = information('Этой темы не существует', $title = 'Информация');
		$count = $mysql->result('SELECT COUNT(*) FROM '.prefix.'_forum_posts WHERE tid = '.securemysql($id).' AND id < '.securemysql($pid)) +1;
		
		$pageNo = ceil($count / $limitCount);
		if($pageNo > 1)
			$CurrentHandler['params']['page'] = $pageNo;
	}
	//print "<pre>".var_export($CurrentHandler, true)."</pre>";
	if(isset($params['s']) or isset($_REQUEST['s'])){
		$s = $params['s']?secure_search_forum($params['s']):secure_search_forum($_REQUEST['s']);
		if( strlen($s) < 3 )
			return $output = information('Слишком короткое слово', $title = 'Информация', true);
		$search = 'AND MATCH (p.message) AGAINST (\''.mysql_real_escape_string($s).'\')';
		$search_p = array('id' => $id, 's'=> $s);
		if(checkLinkAvailable('forum', 'showtopic')){
			$link_topic_curent = generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => array('id' => $id, 's'=> $s), 'xparams' => array(), 'paginator' => array('page', 0, false)), 0);
			if(!$CurrentHandler['params']['s'])
				redirect_forum($link_topic_curent);
		}
	} else
		$search_p = array('id' => $id);
	
	if(is_array($userROW))
		$result = $mysql->record('SELECT t.id as tid, t.title as Ttitle, t.author_id, t.state, t.int_post, f.id as fid, f.title as Ftitle, f.moderators, f.lock_passwd, f.redirect_url, s.uid 
				FROM '.prefix.'_forum_topics AS t 
				INNER JOIN '.prefix.'_forum_forums AS f ON f.id = t.fid
				LEFT JOIN '.prefix.'_forum_subscriptions AS s ON s.tid = t.id AND s.uid = '.securemysql($userROW['id']).'
				WHERE t.id = '.securemysql($id).' LIMIT 1');
	else
		$result = $mysql->record('SELECT t.id as tid, t.title as Ttitle, t.author_id, t.state, t.int_post, f.id as fid, f.title as Ftitle, f.moderators, f.lock_passwd, f.redirect_url 
				FROM '.prefix.'_forum_topics AS t 
				INNER JOIN '.prefix.'_forum_forums AS f ON f.id = t.fid 
				WHERE t.id = '.securemysql($id).' LIMIT 1');
	if(empty($result))
		return $output = information('Этой темы не существует', $title = 'Информация');
	
	if((isset($result['lock_passwd']) && $result['lock_passwd']) && empty($_SESSION['lock_passwd_'.$id]))
		return redirect_forum(link_lock_passwd($id));
	
	if((isset($result['redirect_url']) && $result['redirect_url']))
		return redirect_forum($result['redirect_url']);
	
	$moderators = unserialize($result['moderators']);
	if(array_key_exists(strtolower($userROW['name']), $moderators)){
		$MODE_PS = $MODE_PERM[$result['fid']];
	}else
		$MODE_PS = array();
	
	print "<pre>".var_export($MODE_PERM[$result['fid']], true)."</pre>";
	print "<pre>".var_export($FORUM_PS[$result['fid']], true)."</pre>";
	
	if(empty($FORUM_PS[$result['fid']]['forum_read']) or empty($FORUM_PS[$result['fid']]['topic_read']))
		return $output = permissions_forum('Доступ в тему запрещен');
	
	
	$SYSTEM_FLAGS['info']['title']['item'] = $result['Ftitle'];
	$SYSTEM_FLAGS['info']['title']['name_topic'] = $result['Ttitle'];
	if(isset($search))
		$count_2 = $mysql->result('SELECT COUNT(*) FROM `'.prefix.'_forum_posts` AS p WHERE p.`tid` = '.securemysql($id).' '.$search);
	else 
		$count_2 = $result['int_post'] + 1;
	
	$countPages = ceil($count_2 / $limitCount);
	if($countPages < $pageNo)
		return $output = information('Подстраницы не существует', $title = 'Информация');
	
	if ($pageNo < 1) $pageNo = 1;
	if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;
	$navigations = LoadVariables();
	if ($countPages > 1 && $countPages >= $pageNo){
		$paginationParams = checkLinkAvailable('forum', 'showtopic')?
			array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => $search_p, 'xparams' => array(), 'paginator' => array('page', 0, false)):
			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showtopic'), 'xparams' => $search_p, 'paginator' => array('page', 1, false));
		
		//$pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations, true);
		$pages = generatePagination_forum($countPages, $pageNo, $paginationParams, $navigations, true);
	}
	
	$result_2 = $mysql->select('SELECT p.id as pid, p.message, p.author_id, p.author, p.tid, 
					p.e_date, p.who_e_author, p.author_ip, p.c_data,
					u.reg, u.id as uid, u.name, u.int_post, u.avatar, u.reputation, u.site, u.last, u.status, u.signature, u.mail, u.int_thank
				FROM '.prefix.'_forum_posts AS p 
				LEFT JOIN '.prefix.'_users AS u ON u.id = p.author_id 
				WHERE p.tid = '.securemysql($id).' '.$search.' ORDER BY p.c_data ASC 
				LIMIT '.$limitStart.', '.$limitCount);
	if (empty($result_2) and isset($search) and $pageNo == 1){
		return $output = information('Ничего не найдено', $title = 'Информация', true);
	}/* elseif(empty($result_2) and isset($search) and $pageNo > 1){
		$link_topic_zero = checkLinkAvailable('forum', 'showtopic')?
			generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => $search_p, 'xparams' => array(), 'paginator' => array('page', 0, false)), 1):
			generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showtopic'), 'xparams' => $search_p, 'paginator' => array('page', 1, false)), 1);
		return $output = announcement_forum('Сбрасываем подстраницу', $link_topic_zero, 0);
	}else */
	if(empty($result_2)){
		//$mysql->query('DELETE FROM '.prefix.'_forum_topics WHERE id = '.securemysql($id).' LIMIT 1');
		//$mysql->query('DELETE FROM '.prefix.'_forum_subscriptions WHERE tid = '.securemysql($id));
		return $output = information('У этой темы нет сообщений', $title = 'Информация', true);
	}
	
	update_review($id);
	
	$i = $limitStart;
	
	$time = time() + ($config['date_adjust'] * 60);
	$list_thank = array(); $list_thank_user = array(); $list_attach = array();
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_thank WHERE tid = '.securemysql($id).' ORDER BY NULL') as $row){
		$list_thank[$row['pid']][] = str_replace( array('{url}', '{name}'), array( link_profile($row['author_id'], '', $row['author']), $row['author']), $lang_forum['active_users']);
		$list_thank_user[$row['pid']][] = $row['author_id'];
	}
	
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_attach WHERE tid = '.securemysql($id).' ORDER BY NULL') as $row){
		$list_attach[$row['pid']][] = array(
			'file'=> $row['file'],
			'file_link'=> link_downloads($row['id']),
			'size'=> round($row['size']/1024, 2),
			'int_file'=> $row['downloads'],
		);
	}
	
	$last_time = time() + ($config['date_adjust'] * 60) - pluginGetVariable('forum', 'online_time');
	$users_online = array();
	if( is_array($online) ) foreach ($online as $row) if($row['last_time'] > $last_time) $users_online[$row['users_id']] = $row;
	
	//print "<pre>".var_export($list_attach[13], true)."</pre>";
	
	foreach ($result_2 as $row){
		$i++;
		
		if(isset($MODE_PS) && $MODE_PS)
			$post_send = $MODE_PS['m_post_send'];
		elseif($FORUM_PS[$result['fid']]['post_send'])
			$post_send = true;
		else $post_send = false;
		
		if(isset($MODE_PS) && $MODE_PS)
			$post_modify = $MODE_PS['m_post_modify'];
		elseif($FORUM_PS[$result['fid']]['post_modify'])
			$post_modify = true;
		elseif($FORUM_PS[$result['fid']]['post_modify_your']){
			if($userROW['id'] == $row['author_id'])
				$post_modify = true;
			else
				$post_modify = false;
		}else $post_modify = false;
		
		if(isset($MODE_PS) && $MODE_PS){
			$post_remove = $MODE_PS['m_post_remove'];
		}elseif($FORUM_PS[$result['fid']]['post_remove']){
			$post_remove = true;
		}elseif($FORUM_PS[$result['fid']]['post_remove_your']){
			if($userROW['id'] == $row['author_id'])
				$post_remove = true;
			else
				$post_remove = false;
		} else $post_remove = false;
		
		$tEntry[] = array(
			'i' => $i,
			'editdate' => array(
				'true' => ($row['who_e_author'] && $row['e_date']),
				'time' => $row['e_date'],
				'edited_by' => $row['who_e_author']
			),
			'post_modify' => $post_modify,
			'post_remove' => $post_remove,
			'post_send' => $post_send,
			'del_link' => link_del_post($row['pid']),
			'edit_link' => link_edit_post($row['pid']),
			'tc' => ($result['uid'] == $row['uid'])?1:0,
			'ip' => array(
				'true' => ($userROW['status'] == 1)?1:0,
				'print' => $row['author_ip']
			),
			'mail' => array(
				'true' => ($userROW['status'] == 1)?1:0,
				'print' => $row['mail']
			),
			'active' => ($users_online[$row['uid']])?1:0,
			'site' => array(
				'true' => ($row['site'])?1:0,
				'print' => $row['site']
			),
			'avatar' => array(
				'true' => ($row['avatar'] != '')?1:0,
				'print' => ($row['avatar'] != '')?avatars_url.'/'.$row['avatar']:avatars_url
			),
			'topic_link' => link_topic($row['pid'], 'pid'),
			'complaints_link' => link_complaints($row['pid']),
			'date' => $row['c_data'],
			'post_id' => $row['pid'],
			'author_id' => $row['author_id'],
			'author' => $row['author'],
			'profile_link' => link_profile($row['uid'], '', $row['name']),
			'reputation_link' => link_reputation($row['uid']),
			'sum' => $row['reputation'],
			'plus' => link_add_rep($row['pid'], 1),
			'minus' => link_add_rep($row['pid'], 2),
			'data_reg' => $row['reg'],
			
			'send_pm' => link_send_pm($row['uid']),
			
			'uid' => $row['uid'],
			
			'quote' => array(
				'true' => (($userROW['id'] == $row['uid'] && (($time - $row['c_data']) < (pluginGetVariable('forum','edit_del_time')*60) or (pluginGetVariable('forum','edit_del_time') == 0))) or ($userROW['status'] == 1))?1:0,
				'print' => link_new_post($row['tid'], 'pid', $row['pid']),
				'quote' => str_replace(array("\r", "\n"), array('', ''), $row['message'])
			),
			
			'add_thank_link' =>  (!in_array($userROW['id'], $list_thank_user[$row['pid']]) && is_array($userROW) && ($userROW['id'] != $row['uid']))?link_add_thank($row['pid']):'',
			'list_thank' => implode(', ', $list_thank[$row['pid']]),
			'list_attach' => $list_attach[$row['pid']],
			'thank_link' => link_thank($row['uid']),
			'int_thank' => $row['int_thank'],
			
			'message' => bb_codes($row['message']),
			'signature' =>  bb_codes($row['signature']),
			'num_post' =>  $row['int_post'],
			'userstatus' => $GROUP_PERM[$row['status']]['name'],
		);
	}
	
	$link_topic_s = checkLinkAvailable('forum', 'showtopic')?
			generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), 1):
			generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showtopic'), 'xparams' => array(), 'paginator' => array('page', 1, false)), 1);
	
	$xt = $twig->loadTemplate($tpath['show_topic'].'show_topic.tpl');
	$tVars = array(
		'entries' => isset($tEntry)?$tEntry:'',
		'pages' => array(
			'true' => (isset($pages) && $pages)?1:0,
			'print' => isset($pages)?$pages:''
		),
		'prevlink' => array(
					'true' => !empty($limitStart)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'showtopic')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => $search_p, 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showtopic'), 'xparams' => $search_p, 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)), 
												isset($navigations['prevlink'])?$navigations['prevlink']:''
											)
					),
		),
		'nextlink' => array(
					'true' => ($prev + 2 <= $countPages)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('forum', 'showtopic')?
												generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'showtopic', 'params' => $search_p, 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'showtopic'), 'xparams' => $search_p, 'paginator' => array('page', 1, false)), $prev+2), 
												isset($navigations['nextlink'])?$navigations['nextlink']:''
											)
					),
		),
		'tid'=>$result['tid'],
		'num_page'=>$pageNo,
		'search'=>$s,
		'link_topic_s' => $link_topic_s,
		'state' => $result['state'],
		'addpost' => link_new_post($id),
		'home_link' => link_home(),
		'forum_link' => link_forum($result['fid']),
		'forum_name' => $result['Ftitle'],
		'subject' => $result['Ttitle'],
		'post_send' => (isset($MODE_PS) && $MODE_PS)?$MODE_PS['m_post_send']:$FORUM_PS[$result['fid']]['post_send'],
		'local' => array(
				'num_guest_loc' => $viewers['num_guest_loc'],
				'num_user_loc' => $viewers['num_user_loc'],
				'num_bot_loc' => $viewers['num_bot_loc'],
				'list_loc_user' => $viewers['list_loc_user'],
				'list_loc_bot' => $viewers['list_loc_bot']
		),
		'subscript' => array(
				'true' => isset($result['uid'])?1:0,
				'uns' => link_inf_subs($result['tid'], 2),
				'sus' => link_inf_subs($result['tid'], 1),
		),
	);
	
	$output = $xt->render($tVars);