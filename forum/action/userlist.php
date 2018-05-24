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
//checkLinkAvailable('forum', 'userlist')
$tpath = locatePluginTemplates(array('userlist'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
$xt = $twig->loadTemplate($tpath['userlist'] . 'userlist.tpl');
if (isset($params['page']))
	$pageNo = isset($params['page']) ? intval($params['page']) : 0;
else
	$pageNo = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 0;
$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
$show_group = isset($_REQUEST['show_group']) ? $_REQUEST['show_group'] : '';
$sort_by = isset($_REQUEST['sort_by']) ? $_REQUEST['sort_by'] : '';
$sort_dir = isset($_REQUEST['sort_dir']) ? $_REQUEST['sort_dir'] : '';
if (isset($_REQUEST['submit'])) {
	switch ($show_group) {
		case 1:
			$where[] = 'status = \'1\'';
			break;
		case 2:
			$where[] = 'status = \'2\'';
			break;
		case 3:
			$where[] = 'status = \'3\'';
			break;
		case 4:
			$where[] = 'status = \'4\'';
			break;
	}
	if (isset($username) && $username)
		$where[] = 'name LIKE ' . securemysql('%' . $username . '%') . '';
	if (is_array($where) && $where)
		$where = 'WHERE ' . implode(' AND ', $where);
}
switch ($sort_dir) {
	case 'ASC':
		$sort_d = 'ASC';
		break;
	case 'DESC':
		$sort_d = 'DESC';
		break;
	default:
		$sort_d = 'DESC';
}
switch ($sort_by) {
	case 'username':
		$sort_b = 'name';
		break;
	case 'registered':
		$sort_b = 'reg';
		break;
	case 'num_posts':
		$sort_b = 'int_post';
		break;
	default:
		$sort_b = 'l_post';
}
$limitCount = intval(pluginGetVariable('forum', 'user_per_page'));
if (($limitCount < 2) || ($limitCount > 2000)) $limitCount = 1;
$count = $mysql->result('SELECT COUNT(*) FROM `' . prefix . '_users` ' . $where . '');
$countPages = ceil($count / $limitCount);
if ($countPages < $pageNo)
	return $output = information('Подстраницы не существует', $title = 'Информация');
if ($pageNo < 1) $pageNo = 1;
if (!isset($limitStart)) $limitStart = ($pageNo - 1) * $limitCount;
if ($countPages > 1 && $countPages >= $pageNo) {
	$paginationParams = checkLinkAvailable('forum', 'userlist') ?
		array('pluginName' => 'forum', 'pluginHandler' => 'userlist', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)) :
		array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'userlist'), 'xparams' => array('username' => $username, 'show_group' => $show_group, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir), 'paginator' => array('page', 1, false));
	$navigations = LoadVariables();
	$pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
}
$status = array(
	'4' => 'Пользователь',
	'3' => 'Модератор',
	'2' => 'Глоб. Модератор',
	'1' => 'Администратор'
);
foreach ($mysql->select('SELECT id, name, mail, status, last, reg, site, icq, info, avatar, photo, activation, ip, newpw, authcookie, timezone, signature, int_post, l_post
				FROM ' . prefix . '_users 
				' . $where . '
				ORDER BY ' . $sort_b . ' ' . $sort_d . '
				LIMIT ' . $limitStart . ', ' . $limitCount) as $row) {
	$tEntry[] = array(
		'profile_link' => link_profile($row['id'], '', $row['name']),
		'profile'      => $row['name'],
		'status'       => $status[$row['status']],
		'date'         => $row['reg'],
		'num_post'     => $row['int_post'],
	);
}
$tVars = array(
	'username'                  => isset($_REQUEST['username']) ? secureinput($_REQUEST['username']) : '',
	'show_group_' . $show_group => 1,
	'sort_by_' . $sort_by       => 1,
	'sort_dir_' . $sort_dir     => 1,
	'entries'  => isset($tEntry) ? $tEntry : '',
	'pages'    => array(
		'true'  => (isset($pages) && $pages) ? 1 : 0,
		'print' => isset($pages) ? $pages : ''
	),
	'prevlink' => array(
		'true' => !empty($limitStart) ? 1 : 0,
		'link' => str_replace('%page%',
			"$1",
			str_replace('%link%',
				checkLinkAvailable('forum', 'userlist') ?
					generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'userlist', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)) :
					generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'userlist'), 'xparams' => array('username' => $username, 'show_group' => $show_group, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)),
				isset($navigations['prevlink']) ? $navigations['prevlink'] : ''
			)
		),
	),
	'nextlink' => array(
		'true' => ($prev + 2 <= $countPages) ? 1 : 0,
		'link' => str_replace('%page%',
			"$1",
			str_replace('%link%',
				checkLinkAvailable('forum', 'userlist') ?
					generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'userlist', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev + 2) :
					generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'userlist'), 'xparams' => array('username' => $username, 'show_group' => $show_group, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir), 'paginator' => array('page', 1, false)), $prev + 2),
				isset($navigations['nextlink']) ? $navigations['nextlink'] : ''
			)
		),
	),
);
$output = $xt->render($tVars);