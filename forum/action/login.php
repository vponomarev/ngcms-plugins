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
if (checkLinkAvailable('forum', 'login')) {
	if ($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
		return redirect_forum(link_login());
}
$tpath = locatePluginTemplates(array('login'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
$xt = $twig->loadTemplate($tpath['login'] . 'login.tpl');
$time = time() + ($config['date_adjust'] * 60);
$username = isset($_REQUEST['username']) ? secureinput($_REQUEST['username']) : '';
$password = isset($_REQUEST['password']) ? secureinput($_REQUEST['password']) : '';
$password = EncodePassword($password);
if (is_array($userROW))
	return $output = information('Вы и так уже авторизованы', $title = 'Информация');
if (isset($_REQUEST['submit'])) {
	if (!empty($_REQUEST['forum_captcha_sess'])) {
		$sql = 'select * from ' . prefix . '_users WHERE name = ' . securemysql($username) . ' and pass=' . securemysql($password) . ' limit 1';
		$row = $mysql->record($sql);
		if (isset($row) && $row) {
			$auth_db->save_auth($row);

			return $output = announcement_forum('Вы зашли на форум', link_home(), 2, true);
		} else {
			$error_text[] = 'Неправельный логин или пароль';
			add_banned_users();
			if ($ban[$ip] == '1')
				$error_text[] = 'Ошибки наказумы))<br /> осталось 2 попытки!!!';
			elseif ($ban[$ip] == '2')
				$error_text[] = 'Ошибки наказумы))<br /> Кажется вы редиска!!!';
			elseif ($ban[$ip] == '3')
				$error_text[] = 'К чему шел то и получил!!! <br />Бан нах)))';
		}
	} else {
		add_banned_users();
		if ($ban[$ip] == '1')
			return $output = information('Уходи отсюда, по хорошему)))<br /> У вас осталась 2 попытки!!!');
		elseif ($ban[$ip] == '2')
			return $output = information('Тырдец тебе будет)))<br /> У вас осталась 1 попытка!!!');
		elseif ($ban[$ip] == '3')
			return $output = information('А вы были настойчивы)))<br /> Вы заработали бан!!!');
	}
}
if (isset($error_text) && is_array($error_text))
	foreach ($error_text as $error)
		$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
else $error_input = '';
$tVars = array(
	'error'    => $error_input,
	'username' => $username,
);
$output = $xt->render($tVars);