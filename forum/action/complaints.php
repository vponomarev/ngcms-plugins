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
$tpath = locatePluginTemplates(array('complaints'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
$xt = $twig->loadTemplate($tpath['complaints'] . 'complaints.tpl');
if (isset($params['id']))
	$id = isset($params['id']) ? intval($params['id']) : '';
else
	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
if (!is_array($userROW))
	return $output = information('У вас нет прав доступа', $title = 'Информация');
if (empty($id))
	return $output = information('id сообщения не передан', $title = 'Информация');
if (!$mysql->record('SELECT 1 FROM ' . prefix . '_forum_posts WHERE id = ' . securemysql($id) . ' LIMIT 1'))
	return $output = information('Этого сообщения не существует', $title = 'Информация');
$message = isset($_REQUEST['message']) ? secureinput($_REQUEST['message']) : '';
$time = time() + ($config['date_adjust'] * 60);
if (isset($_REQUEST['submit'])) {
	if (empty($message)) $error_text[] = 'Сообщение пусто';
	if (empty($error_text)) {
		$mysql->query('INSERT INTO ' . prefix . '_forum_complaints(
					pid,
					author,
					author_id,
					message,
					c_data
				) values (
					' . securemysql($id) . ',
					' . securemysql($userROW['name']) . ',
					' . securemysql($userROW['id']) . ',
					' . securemysql($message) . ',
					' . securemysql($time) . '
				)
			');

		return $output = announcement_forum('Данные внесены', link_topic($id, 'pid') . '#' . $id, 2);
	}
}
$error_input = '';
if (isset($error_text) && is_array($error_text))
	foreach ($error_text as $error)
		$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
else $error_input = '';
$tVars = array(
	'message' => $message,
	'error'   => array(
		'true'  => ($error_input) ? 1 : 0,
		'print' => $error_input
	)
);
$output = $xt->render($tVars);