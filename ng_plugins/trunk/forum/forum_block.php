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

function forum_show_block($params) {
	global $CurrentHandler, $twig, $mysql;
	
	$tpath = locatePluginTemplates(array('show_block'), 'forum', 1, '', 'block');
	$xt = $twig->loadTemplate($tpath['show_block'].'show_block.tpl');
	
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

twigRegisterFunction('forum', 'ShowBlock', forum_show_block);