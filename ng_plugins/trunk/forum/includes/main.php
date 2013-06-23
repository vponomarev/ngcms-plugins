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

function header_show()
{global $CurrentHandler, $SYSTEM_FLAGS, $SUPRESS_TEMPLATE_SHOW, $template, $twig, $titles;
	
	$tpath = locatePluginTemplates(array(':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	
	$SYSTEM_FLAGS['info']['title']['group'] = pluginGetVariable('forum', 'forum_title');
	
	if(isset($CurrentHandler['params']['page']) && $CurrentHandler['params']['page'])
		$pageNo = isset($CurrentHandler['params']['page'])?str_replace('%count%',intval($CurrentHandler['params']['page']), pluginGetVariable('forum', 'num_title')):'';
	else
		$pageNo = isset($_REQUEST['page'])?str_replace('%count%',intval($_REQUEST['page']), pluginGetVariable('forum', 'num_title')):'';
	
	//print "<pre>".var_export($CurrentHandler['handlerName'], true)."</pre>";
	switch ($CurrentHandler['handlerName']){
		case '':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					pluginGetVariable('forum', 'home_title'));
		break;
		case 'showforum':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%', '%cat_forum%', '%num%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['item'], $pageNo),
					pluginGetVariable('forum', 'forums_title'));
		break;
		case 'showtopic':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%', '%cat_forum%', '%name_topic%', '%num%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['item'], $SYSTEM_FLAGS['info']['title']['name_topic'], $pageNo),
					pluginGetVariable('forum', 'topic_title'));
		break;
		case 'userlist':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					pluginGetVariable('forum', 'userlist_title'));
		break;
		case 'search':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%' ),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					pluginGetVariable('forum', 'search_title'));
		break;
		case 'register':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					pluginGetVariable('forum', 'register_title'));
		break;
		case 'login':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					pluginGetVariable('forum', 'login_title'));
		break;
		case 'profile':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%', '%others%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['item']),
					pluginGetVariable('forum', 'profile_title'));
		break;
		case 'out':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					pluginGetVariable('forum', 'out_title'));
		break;
		case 'addreply':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Добавить сообщение / %name_forum%');
		break;
		case 'newtopic':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Добавить тему / %name_forum%');
		break;
		case 'delpost':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Удалить сообщение / %name_forum%');
		break;
		case 'edit':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Редактировать / %name_forum%');
		break;
		case 'rules':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					pluginGetVariable('forum', 'rules_title'));
		break;
		case 'show_new':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Последние сообщения / %name_forum%');
		break;
		case 'markread':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Всё прочитано / %name_forum%');
		break;
		case 'rep':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%', '%others%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
					'Репутация участника %others% / %name_forum%');
		break;
		case 'addr':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Добавить репутацию / %name_forum%');
		break;
		case 'news':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%', '%name_news%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
					'%name_news% / Новости / %name_forum%');
		break;
		case 'news_feed':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%',  '%num%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $pageNo),
					'Вся лента / %name_forum% [/ %num%]');
		break;
		case 'act':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%', '%others%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
					'%others% / %name_forum%');
		break;
		case 'thank':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%', '%others%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
					'История благодарностей участнику %others% / %name_forum%');
		break;
		case 'complaints':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Сообщить модератору / %name_forum%');
		break;
		case 'send_pm':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Новое сообщение / %name_forum%');
		break;
		case 'list_pm':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Личное сообщение / %name_forum%');
		break;
		case 'del_pm':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Удалить сообщение / %name_forum%');
		break;
		case 'downloads':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Загрузка файла / %name_forum%');
		break;
		case 'erro404':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Информация / %name_forum%');
		break;
		case 'perm':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Нет доступа / %name_forum%');
		break;
		case 'lock_passwd':
			$titles = str_replace(
					array ('%name_site%', '%name_forum%'),
					array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group']),
					'Форум запаролен / %name_forum%');
		break;
	}
	
	$titles  = preg_replace('/\[([^\[\]]+)\]/' , (isset($pageNo) && $pageNo)?'\\1':'', $titles);
	
	if(empty($SUPRESS_TEMPLATE_SHOW)){
		add_act('index_post', 'header_show');
		
		register_htmlvar('css', $tpath['url::'].'/style.css');
		
		return $template['vars']['titles'] = $titles;
	}
}

function show_main_page($a_stat = false, $output = '', $welcome = false, $event = false)
{global $userROW, $template, $online, $SUPRESS_TEMPLATE_SHOW, $CurrentHandler, $list_news, $lang_forum, $ban, $last_topic, $new_user, $active_user, $SYSTEM_FLAGS, $twig, $show_main, $ipis, $ip, $titles, $timer, $timer_forum, $mysql, $stat, $viewers, $result_last_users, $topic_sum, $post_sum, $result_users, $list_bans;
	
	header_show();
	
	if(isset($show_main) && $show_main) return $template['vars']['mainblock'] = $output;
	
	suppress_show();
	
	list_ban_forum();
	
	if($a_stat)
		statistics_forum();
	
	if($ban[$ip] < 3){
		if($welcome)
			show_news_forum();
		
		if($event)
			recent_events_forum();
	
	}else{$welcome = false; $event = false;}
	
	if(is_array($userROW) && $GROUP_PERM[$GROUP_STATUS]['pm'])
		$int_pm = $mysql->result('SELECT COUNT(*) FROM `'.prefix.'_pm` WHERE to_id = '.securemysql($userROW['id']).' AND viewed = \'0\' AND folder=\'inbox\'');
	
	$tpath = locatePluginTemplates(array('main_page', ':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['main_page'].'main_page.tpl');
	
	$tVars = array(
		'headr' => ($SUPRESS_TEMPLATE_SHOW)?1:0,
		'css' => $tpath['url::'],
		'titles' => $titles,
		'keywords' => (isset($SYSTEM_FLAGS['meta']['keywords'])&& $SYSTEM_FLAGS['meta']['keywords'])?$SYSTEM_FLAGS['meta']['keywords']:pluginGetVariable('forum','forum_keywords'),
		'description' => (isset($SYSTEM_FLAGS['meta']['description'])&& $SYSTEM_FLAGS['meta']['description'])?$SYSTEM_FLAGS['meta']['description']:pluginGetVariable('forum','forum_description'),
		'title' => pluginGetVariable('forum', 'forum_title'),
		
		'out' => link_out(),
		'home' => link_home(),
		'pm' => link_list_pm(0,0,'inbox'),
		'login' => link_login(),
		'search' => link_search(),
		'register' => link_register(),
		'show_new' => link_act('show_new'),
		'show_24' => link_act('show_24'),
		'markread' => link_markread(),
		'rss_feed' => link_rss_feed(),
		'userslist' => link_userslist(),
		'news_feed' => link_news_feed(),
		'administration' => '/engine/admin.php?mod=extra-config&plugin=forum',
		
		'num_pm' => $int_pm,
		
		'rules' => array(
			'true' => (pluginGetVariable('forum', 'rules_on_off'))?1:0,
			'print' => link_rules()
		),
		
		'profile' => link_profile($userROW['id'], '', $userROW['name']),
		'last_visit_u' => $userROW['last'],
		'last_visit_g' => $ipis[$ip],
		'announc_on_off' => (pluginGetVariable('forum', 'announcement_on_off'))?1:0,
		'announce' => bb_codes(pluginGetVariable('forum', 'announcement')),
		
		'content' => $output,
		
		'stat' => ($a_stat)?1:0,
		
		'welcome' => $welcome,
		'event' => $event,
		
		'local' => array(
			'num_guest_loc' => $viewers['num_guest_loc'],
			'num_user_loc' => $viewers['num_user_loc'],
			'num_bot_loc' => $viewers['num_bot_loc'],
			'list_loc_user' => $viewers['list_loc_user'],
			'list_loc_bot' => $viewers['list_loc_bot']
		),
		
		'entries_last_topic' => $last_topic,
		'entries_new_user' => $new_user,
		'entries_active_user' => $active_user,
		'entries_list_news' => $list_news,
		
		'avatar' => array(
			'true' => ($userROW['avatar'] != '')?1:0,
			'print' => ($userROW['avatar'] != '')?avatars_url.'/'.$userROW['avatar']:avatars_url
		),
		
		'num_today' => array(
			'true' => ($stat['num_today'])?1:0,
			'print' => $stat['num_today']
		),
		'num_guest_today' => array(
			'true' => ($stat['num_guest_today'])?1:0,
			'print' => $stat['num_guest_today']
		),
		'total_users' => $result_users,
		'last_user' => array(
			'url' => link_profile($result_last_users['id'], '', $result_last_users['name']),
			'name' => $result_last_users['name']
		),
		
		'list_bans' => $list_bans,
		
		'total_topics' => $topic_sum,
		'total_posts' => $post_sum,
		'online' => array(
		'true' => (isset($stat['active_users']) && is_array($stat['active_users']))?1:0,
			'print' => (isset($stat['online']))?$stat['online']:''
		),
		'users_today' => array(
			'true' => (isset($stat['users_today']) && is_array($stat['users_today']))?1:0,
			'print' => (isset($stat['today_list']))?$stat['today_list']:''
		),
		'num_users' => $stat['num_user'],
		'num_guest' => $stat['num_guest'],
		
		'debug_queries' => '<b><u>SQL queries:</u></b><br>'.implode("<br />\n",$mysql->query_list)."<br />",
		'debug_profiler' => '<b><u>Time profiler:</u></b>'.$timer->printEvents(1)."<br />",
		
		'version' => FORUM_VERSION,
		'queries' => $mysql->qcnt(),
		'exectime' => $timer->stop(),
		'exectime_forum' => $timer_forum->stop_forum(),
		'memory' => forum_memory_usage(),
	);
	
	$template['vars']['mainblock'] = $xt->render($tVars);
}

function show_news_forum()
{global $mysql, $list_news; $i=0;
	
	if(empty($GROUP_PERM[$GROUP_STATUS]['news']))
		return false;
	
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_news ORDER BY c_data DESC LIMIT 5') as $row){
		$i++;
		$list_news[] = array(
			'num'=> $i,
			'news_id' => $row['id'],
			'create_data' => $row['c_data'],
			'title' => $row['title'],
			'content' => bb_codes($row['content']),
			'link_news' => link_news($row['id']),
		);
	}
}

function list_ban_forum(){
global $ban, $list_bans;
	
	if(!is_array($ban)) return;
	
	foreach($ban as $key => $value)
		if($value >= 3)
			$list_ban[] = $key;
	
	if( isset($list_ban) && is_array($list_ban) )
		$list_bans = implode(', ', $list_ban);
}

function viewers_forum(){
global $viewers, $online, $CurrentHandler, $lang_forum, $GROUP_PERM;
	
	$last_time = time() + ($config['date_adjust'] * 60) - pluginGetVariable('forum', 'online_time');
	
	$viewers['num_guest_loc'] = 0; $viewers['num_user_loc'] = 0; $viewers['num_bot_loc'] = 0;
	if( is_array($online) ){
		foreach ($online as $row){
			if($row['last_time'] > $last_time){
				if($row['location'] == $CurrentHandler['handlerName']){
					//print "<pre>".var_export($row, true)."</pre>";
					if(isset($row['users_status']) && $row['users_status'] == 0){
						$viewers['num_guest_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 1){
						$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
						$viewers['active_users_loc'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['admin_url'] );
						$viewers['num_user_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 2){
						$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
						$viewers['active_users_loc'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['editor_url'] );
						$viewers['num_user_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 3){
						$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
						$viewers['active_users_loc'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['publicist_url'] );
						$viewers['num_user_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 4){
						$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
						$viewers['active_users_loc'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['com_url'] );
						$viewers['num_user_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 5){
						$viewers['active_bot_loc'][] = $lang_forum[$row['users']];
						$viewers['num_bot_loc']++;
					}
				}
			}
		}
	}
	
	if( isset($viewers['active_users_loc']) && is_array($viewers['active_users_loc']) )
		$viewers['list_loc_user'] = implode(", ", $viewers['active_users_loc']);
	
	if( isset($viewers['active_bot_loc']) && is_array($viewers['active_bot_loc']) )
		$viewers['list_loc_bot'] = implode(", ", $viewers['active_bot_loc']);
}

function recent_events_forum()
{global $mysql, $last_topic, $new_user, $active_user, $GROUP_PERM;
	$i=1;
	foreach ($mysql->select('SELECT t.l_post, t.title as Ttitle, t.l_author_id , t.l_author, t.int_views, t.int_post, t.c_data, f.id as fid, f.title as Ftitle FROM '.prefix.'_forum_topics AS t LEFT JOIN '.prefix.'_forum_forums AS f ON t.fid = f.id ORDER BY t.l_date DESC LIMIT 10') as $row){
		$last_topic[] = array(
			'num'=>$i++,
			'topic_link' => link_topic($row['l_post'], 'pid').'#'.$row['l_post'],
			'topic_date' => $row['c_data'],
			'forum_link' => link_forum($row['fid']),
			'subject' => $row['Ttitle'],
			'profile_link' => link_profile($row['l_author_id'], '',$row['l_author'] ),
			'profile' => $row['l_author'],
			'num_views' => $row['int_views'],
			'num_replies' => $row['int_post'],
		);
	}
	
	$i=1;
	foreach ($mysql->select('SELECT * FROM '.prefix.'_users ORDER BY reg DESC LIMIT 10') as $row){
		switch($row['status']){
			case 1: $color_start = '<span style="color:'.$GROUP_PERM[$row['status']]['group_color'].';">'; $color_end = '</span>'; break;
			case 2: $color_start = '<span style="color:'.$GROUP_PERM[$row['status']]['group_color'].';">'; $color_end = '</span>'; break;
			case 3: $color_start = '<span style="color:'.$GROUP_PERM[$row['status']]['group_color'].';">'; $color_end = '</span>'; break;
			case 4: $color_start = '<span style="color:'.$GROUP_PERM[$row['status']]['group_color'].';">'; $color_end = '</span>'; break;
			default: $color_start = ''; $color_end = '';
		}
		$new_user[] = array(
			'num'=>$i++,
			'profile_link' => link_profile($row['id'], '', $row['name']),
			'profile' => $row['name'],
			'num_post' => $row['int_post'],
			'color_start' => $color_start,
			'color_end' => $color_end,
		);
	}
	
	$i=1;
	foreach ($mysql->select('SELECT * FROM '.prefix.'_users ORDER BY int_post DESC LIMIT 10') as $row){
		switch($row['status']){
			case 1: $color_start = '<span style="color:'.$GROUP_PERM[$row['status']]['group_color'].';">'; $color_end = '</span>'; break;
			case 2: $color_start = '<span style="color:'.$GROUP_PERM[$row['status']]['group_color'].';">'; $color_end = '</span>'; break;
			case 3: $color_start = '<span style="color:'.$GROUP_PERM[$row['status']]['group_color'].';">'; $color_end = '</span>'; break;
			case 4: $color_start = '<span style="color:'.$GROUP_PERM[$row['status']]['group_color'].';">'; $color_end = '</span>'; break;
			default: $color_start = ''; $color_end = '';
		}
		
		$active_user[] = array(
			'num'=>$i++,
			'profile_link' => link_profile($row['id'], '', $row['name']),
			'profile' => $row['name'],
			'num_post' => $row['int_post'],
			'color_start' => $color_start,
			'color_end' => $color_end,
		);
	}
}

function statistics_forum()
{global $mysql, $online, $lang_forum, $config, $twig, $stat, $result_last_users, $topic_sum, $post_sum, $result_users, $GROUP_PERM;
	
	$last_time = time() + ($config['date_adjust'] * 60) - pluginGetVariable('forum', 'online_time');
	
	generate_statistics_cache();
	
	if( file_exists(FORUM_CACHE.'/cache_index.php') )
		include (FORUM_CACHE.'/cache_statistics.php');
	
	$stat['num_guest_today'] = 0; $stat['num_today'] = 0; $stat['num_user'] = 0; $stat['num_guest'] = 0;
	if( is_array($online) ){
		foreach ($online as $row){
			if($row['last_time'] > $last_time){
				if(isset($row['users_status']) && $row['users_status'] == 0){
					$stat['num_guest']++;
				}elseif(isset($row['users_status']) && $row['users_status'] == 1){
					$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
					$stat['active_users'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['admin_url'] );
					$stat['num_user']++;
				}elseif(isset($row['users_status']) && $row['users_status'] == 2){
					$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
					$stat['active_users'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['editor_url'] );
					$stat['num_user']++;
				}elseif(isset($row['users_status']) && $row['users_status'] == 3){
					$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
					$stat['active_users'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['publicist_url'] );
					$stat['num_user']++;
				}elseif(isset($row['users_status']) && $row['users_status'] == 4){
					$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
					$stat['active_users'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end), $lang_forum['com_url'] );
					$stat['num_user']++;
				}elseif(isset($row['users_status']) && $row['users_status'] == 5){
					$stat['active_users'][] =  $lang_forum[$row['users']];
					$stat['num_user']++;
				}
			}
			if(date('Y-m-d', $row['last_time']) == date('Y-m-d', strtotime('now'))){
				if(isset($row['users_status']) && $row['users_status'] == 0){
					$stat['num_guest_today']++;
				}elseif(isset($row['users_status']) && ($row['users_status'] == 1 or $row['users_status'] == 2 or $row['users_status'] == 3 or $row['users_status'] == 4)){
					switch($row['users_status']){
						case 1: $color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>'; break;
						case 2: $color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>'; break;
						case 3: $color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>'; break;
						case 4: $color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>'; break;
						default: $color_start = ''; $color_end = '';
					}
					$last_date = date('H:i:s', intval($row['last_time']));
					$stat['num_today']++;
					$stat['users_today'][] = str_replace( array('{url}', '{date}', '{name}', '{color_start}', '{color_end}'), array(link_profile($row['users_id'], '', $row['users']), date('H:i:s', $row['last_time']), $row['users'], $color_start, $color_end), $lang_forum['users'] );
				}elseif(isset($row['users_status']) && $row['users_status'] == 5){
					$stat['num_today']++;
					$stat['users_today'][] = $lang_forum[$row['users']];
				}
			}
		}
	}
	
	if( isset($stat['active_users']) && is_array($stat['active_users']) )
		$stat['online'] = implode(", ", $stat['active_users']);
	
	if( isset($stat['users_today']) && is_array($stat['users_today']) )
		$stat['today_list'] = implode(", ", $stat['users_today']);
}

function announcement_forum($message, $url, $banned = 0,  $referer = false)
{global $SYSTEM_FLAGS, $confArray, $CurrentHandler, $show_main, $twig;
	$tpath = locatePluginTemplates(array('redirect', ':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['redirect'].'redirect.tpl');
	
	suppress_show();
	
	$show_main = true;
	
	$SYSTEM_FLAGS['info']['title']['item'] = 'Переодресация';
	
	switch ($banned){
		case 2: $banned = "Запрос обработан: ${message}"; break;
		case 1: $banned = "Ошибка 404: ${message}"; break;
		case 0:
		default: $banned = $message;
	}
	
	$url_referer = $confArray['predefined']['HTTP_REFERER'];
	
	$url_perse = parse_url($url_referer);
	
	if($url_perse['host'] == $SYSTEM_FLAGS['mydomains'][0] && $referer && $CurrentHandler['handlerName'] != 'login')
		$url = $url_referer;
	
	header('Refresh: '.pluginGetVariable('forum','redirect_time').'; url='.$url);
	
	$tVars = array(
		'title' => $banned,
		'info' => $message,
		'url' => $url,
	);
	
	return $xt->render($tVars);
}

function information($info, $title = 'Информация', $error_404 = false)
{global $twig, $SYSTEM_FLAGS, $CurrentHandler;
	
	$CurrentHandler['handlerName'] = 'erro404';
	
	if($error_404)
		header($_SERVER['SERVER_PROTOCOL']. ' 404 Not Found');
	
	$tpath = locatePluginTemplates(array('information'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	
	$xt = $twig->loadTemplate($tpath['information'].'information.tpl');
	
	$tVars = array(
		'title' => $title,
		'info' => $info,
	);
	
	return $xt->render($tVars);
}

function permissions_forum($info, $title = 'Информация')
{global $twig, $SYSTEM_FLAGS, $CurrentHandler;
	
	$CurrentHandler['handlerName'] = 'perm';
	
	$tpath = locatePluginTemplates(array('permissions'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	
	$xt = $twig->loadTemplate($tpath['permissions'].'permissions.tpl');
	
	$tVars = array(
		'action' => link_login(),
		'title' => $title,
		'info' => $info,
	);
	
	return $xt->render($tVars);
}