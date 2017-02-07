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
$tpath = locatePluginTemplates(array('rss_feed'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
$limitCount = intval(pluginGetVariable('forum', 'rss_per_page'));
$select = $mysql->select('SELECT p.author_id, p.author, p.message, p.id, p.c_data, t.title as Ttitle, t.l_date  FROM ' . prefix . '_forum_posts AS p
		LEFT JOIN ' . prefix . '_forum_topics AS t ON t.id = p.tid 
		ORDER BY p.id DESC LIMIT ' . $limitCount);
foreach ($select as $row) {
	$tEntry[] = array(
		'profile_link' => link_profile($row['author_id'], '', $row['author']),
		'profile'      => $row['author'],
		'content'      => bb_codes($row['message']),
		'pid'          => $row['id'],
		'topic_link'   => $config['home_url'] . link_topic($row['id'], 'pid'),
		'Ttitle'       => $row['Ttitle'],
		'c_data'       => $row['c_data'],
	);
}
$xt = $twig->loadTemplate($tpath['rss_feed'] . 'rss_feed.tpl');
$tVars = array(
	'entries' => $tEntry,
	'title'   => pluginGetVariable('forum', 'forum_title'),
	'home'    => $config['home_url'] . link_home(),
	'entries' => $tEntry,
	'date'    => $row['l_date'],
);
echo $xt->render($tVars);