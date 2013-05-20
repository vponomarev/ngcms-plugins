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

ignore_user_abort(true);

global $timer_forum;
include_once(dirname(__FILE__).'/includes/timer.php');
$timer_forum = new timer;
$timer_forum -> start_forum();

register_plugin_page('forum','','plugin_show_forum');
register_plugin_page('forum','showforum','plugin_showforum_forum');
register_plugin_page('forum','showtopic','plugin_showtopic_forum');
register_plugin_page('forum','userlist','plugin_userlist_forum');
register_plugin_page('forum','search','plugin_search_forum');
register_plugin_page('forum','register','plugin_register_forum');
register_plugin_page('forum','login','plugin_login_forum');
register_plugin_page('forum','profile','plugin_profile_forum');
register_plugin_page('forum','out','plugin_out_forum');
register_plugin_page('forum','addreply','plugin_newpost_forum');
register_plugin_page('forum','newtopic','plugin_newtopic_forum');
register_plugin_page('forum','delpost','plugin_delpost_forum');
register_plugin_page('forum','edit','plugin_edit_forum');
register_plugin_page('forum','rules','plugin_rules_forum');
register_plugin_page('forum','uns','plugin_unsubscribe_forum');
register_plugin_page('forum','markread','plugin_markread_forum');
register_plugin_page('forum','rep','plugin_reputation_forum');
register_plugin_page('forum','addr','plugin_add_reputation_forum');

register_plugin_page('forum','news','plugin_news_forum');
register_plugin_page('forum','news_feed','plugin_news_feed_forum');
register_plugin_page('forum','rss','plugin_rss_forum');
register_plugin_page('forum','rss_feed','plugin_rss_feed_forum');
register_plugin_page('forum','act','plugin_act_forum');

register_plugin_page('forum','add_thank','plugin_add_thank_forum');
register_plugin_page('forum','thank','plugin_thank_forum');

register_plugin_page('forum','complaints','plugin_complaints_forum');

register_plugin_page('forum','send_pm','plugin_send_pm_forum');
register_plugin_page('forum','del_pm','plugin_del_pm_forum');
register_plugin_page('forum','list_pm','plugin_list_pm_forum');

register_plugin_page('forum','downloads','plugin_downloads_forum');

register_plugin_page('forum','test','plugin_test_forum1');

include_once(dirname(__FILE__).'/includes/constants.php');
include_once(dirname(__FILE__).'/includes/rewrite.php');
include_once(dirname(__FILE__).'/includes/security.php');
include_once(dirname(__FILE__).'/includes/functions.php');
include_once(dirname(__FILE__).'/includes/main.php');
include_once(dirname(__FILE__).'/includes/bb_code.php');
include_once(dirname(__FILE__).'/includes/cache.php');

function plugin_show_forum()
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $CurrentHandler, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/index.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(true, $output, true, true);
}

function plugin_showforum_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $CurrentHandler, $ban, $twig, $viewers, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	viewers_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/showforum.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_showtopic_forum($params)
{global $userROW, $mysql,  $ip, $SYSTEM_FLAGS, $CurrentHandler, $ban, $online, $twig, $viewers, $lang_forum, $group_perm;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	viewers_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/showtopic.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_userlist_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $CurrentHandler, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/userlist.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_search_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $CurrentHandler, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	include('includes/root_word.php');
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/search.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_register_forum()
{global $userROW, $template, $config, $ip, $mysql, $CurrentHandler, $SYSTEM_FLAGS, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/register.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_login_forum()
{global $userROW, $config, $ip, $template, $mysql, $auth_db, $CurrentHandler, $SYSTEM_FLAGS, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/login.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_profile_forum($params)
{global $userROW, $template, $mysql, $SYSTEM_FLAGS, $CurrentHandler,  $ban, $twig, $ip, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/profile.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_out_forum()
{global $userROW, $config, $template, $mysql, $CurrentHandler, $auth_db, $ban, $ip, $SYSTEM_FLAGS, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/out.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_newpost_forum($params)
{global $userROW, $config, $ip, $ban, $template, $CurrentHandler, $mysql, $SYSTEM_FLAGS, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/newpost.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
	
	unset($userROW, $config, $ip, $mysql, $template, $SYSTEM_FLAGS);
}

function plugin_newtopic_forum($params)
{global $userROW, $config, $ip, $mysql, $ban, $CurrentHandler, $template, $SYSTEM_FLAGS, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/newtopic.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_delpost_forum($params)
{global $userROW, $mysql, $template, $ip, $CurrentHandler, $ban, $SYSTEM_FLAGS, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/delpost.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_edit_forum($params)
{global $userROW, $config, $mysql, $template, $CurrentHandler, $ip, $ban, $SYSTEM_FLAGS, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/edit.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_rules_forum()
{global $userROW, $template, $SYSTEM_FLAGS, $CurrentHandler, $ip, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/rules.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_unsubscribe_forum($params)
{global $userROW, $mysql, $template, $ip, $CurrentHandler, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/uns.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_markread_forum()
{global $userROW, $mysql, $template, $SYSTEM_FLAGS, $CurrentHandler, $ip, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/markread.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_reputation_forum($params)
{global $userROW, $mysql, $template, $SYSTEM_FLAGS, $CurrentHandler, $ip, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/reputation.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_add_reputation_forum($params)
{global $userROW, $mysql, $config, $template, $CurrentHandler, $SYSTEM_FLAGS, $ip, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/addrep.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_act_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $CurrentHandler, $ban, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/act.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output, true);
}

function plugin_news_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $CurrentHandler, $ban, $twig, $viewers, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	viewers_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/news.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output, true);
}

function plugin_news_feed_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $CurrentHandler, $ban, $twig, $viewers, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	viewers_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/news_feed.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output, true);
}

function plugin_rss_feed_forum($params)
{global $userROW, $mysql, $config, $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW, $CurrentHandler, $ip, $SYSTEM_FLAGS, $ban, $twig, $lang_forum;
	
	$SUPRESS_TEMPLATE_SHOW = 1;
	$SUPRESS_MAINBLOCK_SHOW = 0;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if($ban[$ip] < 3)
		$output = rss_export_generate_forum();
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output, true);
}

function plugin_rss_forum($params)
{global $userROW, $mysql, $config, $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW, $CurrentHandler, $ip, $SYSTEM_FLAGS, $ban, $twig, $lang_forum;
	
	$SUPRESS_TEMPLATE_SHOW = 1;
	$SUPRESS_MAINBLOCK_SHOW = 0;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):0;
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	if($ban[$ip] < 3)
		$output = rss_export_generate_forum($id);
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output, true);
}

function plugin_add_thank_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $ban, $CurrentHandler, $twig, $viewers, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	viewers_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/add_thank.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_thank_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $ban, $CurrentHandler, $twig, $viewers, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	viewers_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/thank.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_complaints_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $ban, $CurrentHandler, $twig, $viewers, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	viewers_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/complaints.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_send_pm_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $ban, $CurrentHandler, $twig, $viewers, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	viewers_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/send_pm.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_list_pm_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $online, $CurrentHandler, $ban, $twig, $viewers, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	check_online_forum();
	
	viewers_forum();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/list_pm.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_del_pm_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $ban, $CurrentHandler, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/del_pm.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_downloads_forum($params)
{global $userROW, $mysql, $ip, $SYSTEM_FLAGS, $ban, $CurrentHandler, $twig, $lang_forum;
	
	//set_error_handler('my_error_handler');
	
	banned_users();
	
	if($ban[$ip] < 3)
		include(FORUM_DIR.'/action/downloads.php');
	else
		$output = information('�� ��������!!!');
	
	show_main_page(false, $output);
}

function plugin_test_forum1()
{global $template, $mysql, $parse, $userROW, $config, $SUPRESS_TEMPLATE_SHOW, $ip, $lang_forum;
	
 	$id = 9437;
	$subject = '�������� ��������';
	$message = '�������� ��������';
	$time = time() + ($config['date_adjust'] * 60);
	/*
	while (true)
	{
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
			
			update_forum($topic_id, $subject, $time, $userROW['name'], $userROW['id'], $id);
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
			
			
	}
	generate_index_cache(true); */
	while (true)
	{
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
				update_topic($time, $userROW['name'], $userROW['id'], $id);
				update_forum($id, $result['title'], $time, $userROW['name'], $userROW['id'], $result['fid']);
				
				if(isset($_REQUEST['subscribe']))
					subscribe($userROW['id'], $id);
				
				generate_index_cache(true);
	}
}

