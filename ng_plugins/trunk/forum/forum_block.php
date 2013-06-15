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

include_once(dirname(__FILE__).'/includes/bb_code.php');
include_once(dirname(__FILE__).'/includes/rewrite.php');

function forum_show_topics($params) {
	global $twig, $mysql;
	
	$limit = $params['limit']?$params['limit']:5;
	$templates = $params['temp']?$params['temp']:'show_topics';
	$tpath = locatePluginTemplates(array($templates), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'), 'block');
	$xt = $twig->loadTemplate($tpath[$templates].$templates.'.tpl');
	
	$i=1;
	foreach ($mysql->select('SELECT t.id as tid, t.title as Ttitle, t.l_author_id , t.l_author, t.int_views, t.int_post, t.c_data, f.id as fid, f.title as Ftitle FROM '.prefix.'_forum_topics AS t LEFT JOIN '.prefix.'_forum_forums AS f ON t.fid = f.id ORDER BY t.l_date DESC LIMIT '.$limit) as $row){
		$last_topic[] = array(
			'num'=>$i++,
			'topic_link' => link_topic($row['tid'], 'last'),
			'topic_date' => $row['c_data'],
			'forum_link' => link_forum($row['fid']),
			'subject' => $row['Ttitle'],
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

function forum_show_topics_top($params) {
	global $twig, $mysql;
	
	$limit = $params['limit']?$params['limit']:5;
	$templates = $params['temp']?$params['temp']:'show_topics_top';
	$tpath = locatePluginTemplates(array($templates), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'), 'block');
	$xt = $twig->loadTemplate($tpath[$templates].$templates.'.tpl');
	
	$i=1;
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_topics ORDER BY int_views DESC LIMIT '.$limit) as $row){
		
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
	
	$limit = $params['limit']?$params['limit']:5;
	$templates = $params['temp']?$params['temp']:'show_a_users';
	$tpath = locatePluginTemplates(array($templates), pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'), 'block');
	$xt = $twig->loadTemplate($tpath[$templates].$templates.'.tpl');
	
	$i=1;
	foreach ($mysql->select('SELECT * FROM '.prefix.'_users ORDER BY int_post DESC LIMIT '.$limit) as $row){
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
	
	$limit = $params['limit']?$params['limit']:5;
	$templates = $params['temp']?$params['temp']:'show_news';
	$tpath = locatePluginTemplates(array($templates), 'forum', 1, '', 'block');
	$xt = $twig->loadTemplate($tpath['show_news'].$templates.'.tpl');
	
	$i=1;
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_news ORDER BY c_data LIMIT '.$limit) as $row){
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
twigRegisterFunction('forum', 'ShowTopicsTop', forum_show_topics_top);
twigRegisterFunction('forum', 'ShowAUsers', forum_show_a_users);