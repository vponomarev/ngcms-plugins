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
$tpath = locatePluginTemplates(array('profile'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
$xt = $twig->loadTemplate($tpath['profile'] . 'profile.tpl');
if (isset($params['id']))
	$id = isset($params['id']) ? intval($params['id']) : 0;
else
	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if (isset($params['name']))
	$name = isset($params['name']) ? secureinput($params['name']) : '';
if (isset($params['act']))
	$action = isset($params['act']) ? secureinput($params['act']) : '';
else
	$action = isset($_REQUEST['act']) ? secureinput($_REQUEST['act']) : '';
if (empty($id) && empty($name))
	return $output = information('id пользователя не передан', $title = 'Информация');
switch ($action) {
	case 'edit':
		if (checkLinkAvailable('forum', 'profile')) {
			if ($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
				return redirect_forum(link_profile($userROW['id'], 'edit', $userROW['name']));
		}
		if ($id != $userROW['id'] && $name != $userROW['name'])
			return $output = information('Нечего тебе здесь делать :)', $title = 'Информация');
		$edit = 1;
		$mail = isset($_REQUEST['mail']) ? secureinput($_REQUEST['mail']) : $userROW['mail'];
		$site = isset($_REQUEST['site']) ? secureinput($_REQUEST['site']) : $userROW['site'];
		$signature = isset($_REQUEST['signature']) ? secureinput($_REQUEST['signature']) : $userROW['signature'];
		$name = $userROW['name'];
		$reg = $userROW['reg'];
		$last = $userROW['last'];
		$last_post = $userROW['last_post'];
		$num_post = $userROW['num_post'];
		$icq = $userROW['icq'];
		$info = $userROW['info'];
		$avatar = $userROW['avatar'];
		$user_status = $userROW['status'];
		$SYSTEM_FLAGS['info']['title']['item'] = 'Редактирование пользователя: ' . $name;
		if (isset($_REQUEST['submit'])) {
			if (empty($site)) {
			} else {
				if (substr($site, 0, 7) != 'http://')
					$site = 'http://' . $site;
				if (!filter_var($site, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
					$error_text['url'] = 'Не корректный адрес';
			}
			if (empty($mail))
				$error_text['mail'] = 'e-mail не указан';
			else {
				if (!filter_var($mail, FILTER_VALIDATE_EMAIL))
					$error_text['mail'] = 'Неверный e-mail';
			}
			if (empty($error_text)) {
				$mysql->query('UPDATE ' . prefix . '_users SET mail = ' . securemysql($mail) . ', site = ' . securemysql($site) . ', signature = ' . securemysql($signature) . ' WHERE id = ' . intval($userROW['id']) . ' LIMIT 1');

				return $output = announcement_forum('Профиль обновлен', link_profile($userROW['id'], '', $userROW['name']), 2);
			}
		}
		$status = array(
			'4' => 'Пользователь',
			'3' => 'Модератор',
			'2' => 'Глоб. Модератор',
			'1' => 'Администратор'
		);
		break;
	case '':
		$profile = 1;
		//if(!is_array($userROW))
		//	return  $output = announcement_forum('Сначала вы должны авторизироваться чтобы смотреть профиль', link_login(), 2);
		$status = array(
			'4' => 'Пользователь',
			'3' => 'Модератор',
			'2' => 'Глоб. Модератор',
			'1' => 'Администратор'
		);
		$sql = 'SELECT id, name, mail, status, last, reg, site, icq, info, avatar, photo, activation, ip, newpw, authcookie, timezone, signature, int_post, l_post
					FROM ' . prefix . '_users 
					WHERE ' . (empty($name) ? 'id = ' . securemysql($id) . '' : 'name = ' . securemysql($name) . '') . ' LIMIT 1';
		$row = $mysql->record($sql);
		if (empty($row))
			return $output = information('Такого пользователя не существует', $title = 'Информация');
		if (checkLinkAvailable('forum', 'profile')) {
			if ($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
				return redirect_forum(link_profile($row['id'], '', $row['name']));
		}
		$mail = $row['mail'];
		$site = $row['site'];
		$signature = bb_codes($row['signature']);
		$name = $row['name'];
		$reg = $row['reg'];
		$last = $row['last'];
		$last_post = $row['l_post'];
		$num_post = $row['int_post'];
		$icq = $row['icq'];
		$info = $row['info'];
		$info = $row['avatar'];
		$user_status = $row['status'];
		$SYSTEM_FLAGS['info']['title']['item'] = 'Профиль пользователя: ' . $name;
		break;
}
//print $userROW['last'].'<br />';
//print $last;
if (isset($error_text) && is_array($error_text))
	foreach ($error_text as $error)
		$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
else $error_input = '';
$tVars = array(
	'action'     => array(
		'profile' => isset($profile) ? 1 : 0,
		'edit'    => isset($edit) ? 1 : 0,
	),
	'site'       => array(
		'true'  => ($site) ? 1 : 0,
		'print' => ($site) ? $site : '',
	),
	'auth_admin' => ($userROW['status'] == 1) ? 1 : 0,
	'auth_user'  => ($userROW['id'] == $id or $userROW['name'] == $name) ? 1 : 0,
	'name'       => $name,
	'regdate'    => $reg,
	'status'     => $status[$user_status],
	'lastvisit'  => $last,
	'lastpost'   => $last_post,
	'totalposts' => $num_post,
	'mail'       => $mail,
	'edit'       => link_profile($userROW['id'], 'edit', $userROW['name']),
	'icq'        => $icq,
	'about'      => $info,
	'signature'  => $signature,
	'avatar'     => $avatar,
	'error'      => array(
		'url'  => array(
			'true'  => isset($error_input['url']) ? 1 : 0,
			'print' => isset($error_input['url']) ? $error_input['url'] : '',
		),
		'mail' => array(
			'true'  => isset($error_input['mail']) ? 1 : 0,
			'print' => isset($error_input['mail']) ? $error_input['mail'] : '',
		),
	),
);
$output = $xt->render($tVars);