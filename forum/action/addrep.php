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
$tpath = locatePluginTemplates(array('send_rep'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
$xt = $twig->loadTemplate($tpath['send_rep'] . 'send_rep.tpl');
if (isset($params['pid']))
	$pid = isset($params['pid']) ? intval($params['pid']) : 0;
else
	$pid = isset($_REQUEST['pid']) ? intval($_REQUEST['pid']) : 0;
if (isset($params['metod']))
	$method = isset($params['metod']) ? intval($params['metod']) : 0;
else
	$method = isset($_REQUEST['metod']) ? intval($_REQUEST['metod']) : 0;
$message = isset($_REQUEST['message']) ? secureinput($_REQUEST['message']) : '';
$time = time() + ($config['date_adjust'] * 60);
if (empty($pid))
	return $output = information('Сообщение не выбрано', $title = 'Информация');
if (!is_array($userROW))
	return $output = information('У вас нет прав доступа', $title = 'Информация');
switch ($method) {
	case 1:
		$plus = 1;
		$minus = 0;
		break;
	case 2:
		$plus = 0;
		$minus = 1;
		break;
	default:
		return $output = information('Ошибка', $title = 'Информация');
}
$row = $mysql->record('SELECT * FROM ' . prefix . '_forum_posts WHERE id = ' . securemysql($pid) . ' LIMIT 1');
if (empty($row))
	return $output = information('Такого сообщения не существует', $title = 'Информация');
$row_2 = $mysql->result('SELECT 1 FROM ' . prefix . '_users WHERE id = ' . securemysql($row['author_id']) . ' LIMIT 1');
if (empty($row_2))
	return $output = information('Такого пользователя нет', $title = 'Информация');
if ($userROW['id'] == $row['author_id'])
	return $output = information('За себя нельзя голосовать', $title = 'Информация');
if (isset($_REQUEST['submit'])) {
	if (empty($message)) {
		$error_text[] = 'Вы не добавили сообщение';
	} else {
		$mysql->query('insert into ' . prefix . '_forum_reputation (
				to_author_id,
				author_id,
				author,
				c_data,
				message,
				plus,
				minus,
				pid,
				tid
				) VALUES (
				' . securemysql($row['author_id']) . ', 
				' . securemysql($userROW['id']) . ', 
				' . securemysql($userROW['name']) . ', 
				' . securemysql($time) . ', 
				' . securemysql($message) . ', 
				' . securemysql($plus) . ', 
				' . securemysql($minus) . ', 
				' . securemysql($row['id']) . ',
				' . securemysql($row['tid']) . ')
			');
		$count = $mysql->result('SELECT SUM(plus) - SUM(minus) FROM ' . prefix . '_forum_reputation WHERE to_author_id = ' . securemysql($row['author_id']));
		$mysql->query('UPDATE ' . prefix . '_users SET reputation = ' . securemysql($count) . ' WHERE id = ' . securemysql($row['author_id']) . ' LIMIT 1');

		return $output = announcement_forum('Данные внесены', link_topic($pid, 'pid') . '#' . $pid, 2);
	}
}
$error_input = '';
if (isset($error_text) && is_array($error_text))
	foreach ($error_text as $error)
		$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
else $error_input = '';
$tVars = array(
	'addusers' => $userROW['name'],
	'users'    => $row['author'],
	'info'     => array(
		'method' => $method,
	),
	'error'    => array(
		'true'  => ($error_input) ? 1 : 0,
		'print' => $error_input
	)
);
$output = $xt->render($tVars);
