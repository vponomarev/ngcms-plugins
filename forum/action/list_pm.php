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
if (empty($GROUP_PS['group_pm']))
	return $output = permissions_forum('У вас нет доступа к сообщениям');
$tpath = locatePluginTemplates(array('list_pm'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
$xt = $twig->loadTemplate($tpath['list_pm'] . 'list_pm.tpl');
if (!is_array($userROW))
	return $output = information('У вас нет прав доступа', $title = 'Информация');
if (isset($params['id']))
	$id = isset($params['id']) ? intval($params['id']) : 0;
else
	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if (isset($params['folder']))
	$folder = isset($params['folder']) ? $params['folder'] : 0;
else
	$folder = isset($_REQUEST['folder']) ? $_REQUEST['folder'] : 0;
switch ($folder) {
	case 'inbox':
		$io = 'inbox';
		$_sql = 'pm.from_id = u.id';
		$show_id = 'to_id';
		break;
	case 'outbox':
		$io = 'outbox';
		$_sql = 'pm.to_id = u.id';
		$show_id = 'from_id';
		break;
	default:
		$io = 'inbox';
		$_sql = 'pm.from_id = u.id';
		$show_id = 'to_id';
}
if ($_POST['submit']) {
	if (is_array($_POST['sel_pm'])) {
		foreach ($_POST["sel_pm"] as $pid => $value) {
			$pid = intval($pid);
			$mysql->query('DELETE FROM ' . prefix . '_pm WHERE ((`from_id`=' . db_squote($userROW['id']) . ' AND `folder`=\'outbox\') OR (`to_id`=' . db_squote($userROW['id']) . ') AND `folder`=\'inbox\') AND id = \'' . intval($pid) . '\'');
		}

		return $output = announcement_forum('Сообщения удалены', link_list_pm(0, 0, $io), 2);
	}
}
if ($id) {
	$row = $mysql->record('SELECT pm.id as pid, pm.to_id, pm.from_id, pm.subject as title, pm.message, pm.date as pmdate, pm.viewed as viewed, u.id as uid, u.name as uname, u.status as ustatus, u.avatar, u.site, u.int_post, u.signature FROM ' . prefix . '_pm as pm
			LEFT JOIN ' . prefix . '_users as u ON pm.from_id = u.id 
			WHERE pm.id = ' . securemysql($id) . ' LIMIT 1');
	switch ($row['ustatus']) {
		case 1:
			$userstatus = "Администратор";
			break;
		case 2:
			$userstatus = "Редактор";
			break;
		case 3:
			$userstatus = "Журналист";
			break;
		case 4:
			$userstatus = "Пользователь";
			break;
		default:
			$userstatus = "Забанен или удален";
	};
	//print "<pre>".var_export($row, true)."</pre>";
	if ($userROW['id'] == $row[$show_id]) {
		if (empty($row['viewed']))
			$mysql->query('UPDATE ' . prefix . '_pm SET `viewed` = \'1\' WHERE `id` = ' . securemysql($id) . ' LIMIT 1');
		$last_time = time() + ($config['date_adjust'] * 60) - pluginGetVariable('forum', 'online_time');
		$users_online = array();
		if (is_array($online)) foreach ($online as $row_2) if ($row_2['last_time'] > $last_time) $users_online[$row_2['users_id']] = $row_2;
		$pm = array(
			'pm_true'       => 1,
			'userstatus'    => $userstatus,
			'avatar'        => array(
				'true'  => ($row['avatar'] != '') ? 1 : 0,
				'print' => ($row['avatar'] != '') ? avatars_url . '/' . $row['avatar'] : avatars_url
			),
			'num_post'      => $row['int_post'],
			'site'          => array(
				'true'  => ($row['site']) ? 1 : 0,
				'print' => $row['site']
			),
			'content'       => bb_codes($row['message']),
			'signature'     => bb_codes($row['signature']),
			'send_pm'       => link_send_pm($row['uid']),
			'active'        => ($users_online[$row['uid']]) ? 1 : 0,
			'pmid'          => $row['pid'],
			'pmdate'        => $row['pmdate'],
			'pmdate2'       => $row['pmdate'],
			'title'         => $row['title'],
			'link_pm_reply' => link_send_pm($row['uid'], $row['pid']),
			'link_pm_quote' => link_send_pm($row['uid'], '', $row['pid']),
			'link_del_pm'   => link_del_pm($row['pid'], $io),
			'profile_link'  => link_profile($row['uid'], '', $row['uname']),
			'profile'       => $row['uname'],
		);
	}
}
$limitCount = intval(pluginGetVariable('forum', 'list_pm_per_page'));
if (($limitCount < 2) || ($limitCount > 2000)) $limitCount = 2;
if (isset($params['page']))
	$pageNo = isset($params['page']) ? intval($params['page']) : 0;
else
	$pageNo = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 0;
$count = $mysql->result('SELECT COUNT(*) FROM `' . prefix . '_pm` WHERE ' . $show_id . ' = ' . securemysql($userROW['id']) . ' AND folder=\'' . $io . '\'');
$countPages = ceil($count / $limitCount);
if ($countPages < $pageNo)
	return $output = information('Подстраницы не существует', $title = 'Информация');
if ($pageNo < 1) $pageNo = 1;
if (!isset($limitStart)) $limitStart = ($pageNo - 1) * $limitCount;
if ($countPages > 1 && $countPages >= $pageNo) {
	$paginationParams = checkLinkAvailable('forum', 'list_pm') ?
		array('pluginName' => 'forum', 'pluginHandler' => 'list_pm', 'params' => array('folder' => $io), 'xparams' => array(), 'paginator' => array('page', 0, false)) :
		array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'list_pm'), 'xparams' => array('folder' => $io), 'paginator' => array('page', 1, false));
	$navigations = LoadVariables();
	$pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
}
foreach ($mysql->select('SELECT pm.id as pid, pm.subject as title, pm.date as pmdate, pm.viewed as viewed, u.id as uid, u.name as uname FROM ' . prefix . '_pm as pm
		LEFT JOIN ' . prefix . '_users as u ON ' . $_sql . ' 
		WHERE pm.' . $show_id . ' = ' . securemysql($userROW['id']) . ' AND folder=\'' . $io . '\'
		ORDER BY pm.id DESC LIMIT ' . $limitStart . ', ' . $limitCount) as $row) {
	$tEntry[] = array(
		'pmid'         => $row['pid'],
		'pmdate'       => $row['pmdate'],
		'title'        => $row['title'],
		'link_pm'      => link_list_pm($row['pid'], $pageNo, $io),
		'profile_link' => link_profile($row['uid'], '', $row['uname']),
		'profile'      => $row['uname'],
		'viewed'       => $row['viewed']
	);
}
$tVars = array(
	'pm'                  => $pm,
	'entries_' . $io . '' => isset($tEntry) ? $tEntry : '',
	'pages'               => array(
		'true'  => (isset($pages) && $pages) ? 1 : 0,
		'print' => isset($pages) ? $pages : ''
	),
	'local'               => array(
		'num_guest_loc' => $viewers['num_guest_loc'],
		'num_user_loc'  => $viewers['num_user_loc'],
		'num_bot_loc'   => $viewers['num_bot_loc'],
		'list_loc_user' => $viewers['list_loc_user'],
		'list_loc_bot'  => $viewers['list_loc_bot']
	),
	'inbox_link'          => link_list_pm(0, 0, 'inbox'),
	'outbox_link'         => link_list_pm(0, 0, 'outbox'),
	'case_io'             => $io,
	'home_link'           => link_home(),
	'send_pm'             => link_send_pm(),
	'prevlink'            => array(
		'true' => !empty($limitStart) ? 1 : 0,
		'link' => str_replace('%page%',
			"$1",
			str_replace('%link%',
				checkLinkAvailable('forum', 'list_pm') ?
					generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'list_pm', 'params' => array('folder' => $io), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)) :
					generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'list_pm'), 'xparams' => array('folder' => $io), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)),
				isset($navigations['prevlink']) ? $navigations['prevlink'] : ''
			)
		),
	),
	'nextlink'            => array(
		'true' => ($prev + 2 <= $countPages) ? 1 : 0,
		'link' => str_replace('%page%',
			"$1",
			str_replace('%link%',
				checkLinkAvailable('forum', 'list_pm') ?
					generatePageLink(array('pluginName' => 'forum', 'pluginHandler' => 'list_pm', 'params' => array('folder' => $io), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev + 2) :
					generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'forum', 'handler' => 'list_pm'), 'xparams' => array('folder' => $io), 'paginator' => array('page', 1, false)), $prev + 2),
				isset($navigations['nextlink']) ? $navigations['nextlink'] : ''
			)
		),
	),
);
$output = $xt->render($tVars);