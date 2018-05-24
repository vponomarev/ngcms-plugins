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
$tpath = locatePluginTemplates(array('reputation'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
$xt = $twig->loadTemplate($tpath['reputation'] . 'reputation.tpl');
if (isset($params['id']))
	$id = isset($params['id']) ? intval($params['id']) : 0;
else
	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if (isset($params['send']))
	$send = isset($params['send']) ? intval($params['send']) : 0;
else
	$send = isset($_REQUEST['send']) ? intval($_REQUEST['send']) : 0;
if (empty($id))
	return $output = information('Этой темы не существует', $title = 'Информация');
//if(!is_array($userROW))
//	return $output = information('У вас нет прав доступа', $title = 'Информация');
$limitCount = intval(pluginGetVariable('forum', 'reput_per_page'));
if (($limitCount < 2) || ($limitCount > 2000)) $limitCount = 2;
if (isset($params['page']))
	$pageNo = isset($params['page']) ? intval($params['page']) : 0;
else
	$pageNo = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 0;
$count = $mysql->result('SELECT COUNT(*) FROM `' . prefix . '_forum_reputation` WHERE to_author_id= ' . securemysql($id));
$countPages = ceil($count / $limitCount);
if ($countPages < $pageNo)
	return $output = information('Подстраницы не существует', $title = 'Информация');
if ($pageNo < 1) $pageNo = 1;
if (!isset($limitStart)) $limitStart = ($pageNo - 1) * $limitCount;
if ($countPages > 1 && $countPages >= $pageNo) {
	$paginationParams = checkLinkAvailable('forum', 'rep') ?
		array('pluginName' => 'forum', 'pluginHandler' => 'rep', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)) :
		array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'rep'), 'xparams' => array('id' => $id), 'paginator' => array('page', 1, false));
	$navigations = LoadVariables();
	$pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
}
$name = $mysql->result('SELECT name FROM ' . prefix . '_users WHERE id = ' . securemysql($id) . ' LIMIT 1');
if (empty($name))
	return $output = information('Такого пользователя нет', $title = 'Информация');
$SYSTEM_FLAGS['info']['title']['others'] = $name;
foreach ($mysql->select('SELECT r.tid, r.pid, r.author, r.author_id, r.c_data, r.message, r.to_author_id, r.plus, r.minus, t.title FROM ' . prefix . '_forum_reputation AS r 
		LEFT JOIN ' . prefix . '_forum_topics AS t ON t.id = r.tid
		WHERE r.to_author_id= ' . securemysql($id) . ' 
		ORDER BY r.id DESC
		LIMIT ' . $limitStart . ', ' . $limitCount
) as $row) {
	$tEntry[] = array(
		'profile_link' => link_profile($row['author_id'], '', $row['author']),
		'profile'      => $row['author'],
		'message'      => $row['message'],
		'subject'      => $row['title'],
		'post_id'      => $row['pid'],
		'topic_link'   => link_topic($row['pid'], 'pid'),
		'date'         => $row['c_data'],
		'rep'          => array(
			'plus'  => $row['plus'],
			'minus' => $row['minus'],
		),
	);
}
$plus = $mysql->result('SELECT SUM(plus) FROM ' . prefix . '_forum_reputation WHERE to_author_id = ' . securemysql($id) . '');
$tVars = array(
	'entries'   => isset($tEntry) ? $tEntry : '',
	'pages'     => array(
		'true'  => (isset($pages) && $pages) ? 1 : 0,
		'print' => isset($pages) ? $pages : ''
	),
	'to_author' => $name,
	'plus'      => $plus,
	'min'       => $count - $plus,
	'home_link' => link_home(),
	'prevlink'  => array(
		'true' => !empty($limitStart) ? 1 : 0,
		'link' => str_replace('%page%',
			"$1",
			str_replace('%link%',
				checkLinkAvailable('forum', 'rep') ?
					generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'rep', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)) :
					generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'rep'), 'xparams' => array('id' => $id), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)),
				isset($navigations['prevlink']) ? $navigations['prevlink'] : ''
			)
		),
	),
	'nextlink'  => array(
		'true' => ($prev + 2 <= $countPages) ? 1 : 0,
		'link' => str_replace('%page%',
			"$1",
			str_replace('%link%',
				checkLinkAvailable('forum', 'rep') ?
					generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'rep', 'params' => array('id' => $id), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev + 2) :
					generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'rep'), 'xparams' => array('id' => $id), 'paginator' => array('page', 1, false)), $prev + 2),
				isset($navigations['nextlink']) ? $navigations['nextlink'] : ''
			)
		),
	),
);
$output = $xt->render($tVars);