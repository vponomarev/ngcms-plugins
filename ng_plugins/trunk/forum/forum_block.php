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

function forum_show_topics($params) {
	global $CurrentHandler, $twig, $mysql;
	
	$tpath = locatePluginTemplates(array('show_topics'), 'forum', 1, '', 'block');
	$xt = $twig->loadTemplate($tpath['show_topics'].'show_topics.tpl');
	
	$i=1;
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_topics ORDER BY l_date DESC LIMIT '.$params['limit']) as $row){
		
		$entries[] = array(
			'num'=>$i++,
			'topic_link' => link_topic($row['id'], 'last'),
			'subject' => $row['title'],
			'profile_link' => link_profile($row['l_author_id'], '',$row['l_author'] ),
			'profile' => $row['l_author'],
			'num_views' => $row['int_views'],
			'num_replies' => $row['int_post'],
		);
	}
	
	$tVars = array(
		'entries' => $entries,
	);
	
	return $xt->render($tVars);

}

function forum_show_a_users($params) {
	global $CurrentHandler, $twig, $mysql;
	
	$tpath = locatePluginTemplates(array('show_a_users'), 'forum', 1, '', 'block');
	$xt = $twig->loadTemplate($tpath['show_a_users'].'show_a_users.tpl');
	
	$i=1;
	foreach ($mysql->select('SELECT * FROM '.prefix.'_users ORDER BY int_post DESC LIMIT '.$params['limit']) as $row){
		switch($row['status']){
			case 1: $color_start = '<span style="color:red;">'; $color_end = '</span>'; break;
			case 2: $color_start = '<span style="color:green;">'; $color_end = '</span>'; break;
			case 3: $color_start = '<span style="color:blue;">'; $color_end = '</span>'; break;
			default: $color_start = ''; $color_end = '';
		}
		
		$entries[] = array(
			'num'=>$i++,
			'profile_link' => link_profile($row['id'], '', $row['name']),
			'profile' => $row['name'],
			'num_post' => $row['int_post'],
			'color_start' => $color_start,
			'color_end' => $color_end,
		);
	}
	
	$tVars = array(
		'entries' => $entries,
	);
	
	return $xt->render($tVars);

}

function forum_show_news($params)
{global $CurrentHandler, $twig, $mysql;
	include_once(dirname(__FILE__).'/includes/bb_code.php');
	include_once(dirname(__FILE__).'/includes/rewrite.php');
	
	$tpath = locatePluginTemplates(array('show_news'), 'forum', 1, '', 'block');
	$xt = $twig->loadTemplate($tpath['show_news'].'show_news.tpl');
	
	$i=1;
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_news ORDER BY c_data LIMIT '.$params['limit']) as $row){
		$i++;
		$entries[] = array(
			'num'=> $i,
			'news_id' => $row['id'],
			'create_data' => $row['c_data'],
			'title' => $row['title'],
			'content' => bb_codes($row['content']),
			'link_news' => link_news($row['id']),
		);
	}
	
	$tVars = array(
		'entries' => $entries,
	);
	
	return $xt->render($tVars);
}

twigRegisterFunction('forum', 'ShowListNews', forum_show_news);
twigRegisterFunction('forum', 'ShowTopics', forum_show_topics);
twigRegisterFunction('forum', 'ShowAUsers', forum_show_a_users);