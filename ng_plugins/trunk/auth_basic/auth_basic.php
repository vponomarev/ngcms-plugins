<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


//
// Прописываем свой модуль
//
global $AUTH_METHOD;
global $AUTH_CAPABILITIES;
global $config;

class auth_basic {

	// Осуществить вход
	// $username	= логин
	// $password	= пароль
	// $auto_scan	= если 1, то функция сама должна найти нужные параметры среди POST'ов
	function login($auto_scan = 1, $username = '', $password = '') {
		global $mysql;

	        if ($auto_scan) {
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
		}
		$password = EncodePassword($password);

		$sql = "select * from ".uprefix."_users where name = ".db_squote($username)." and pass=".db_squote($password);
		$row = $mysql->record($sql);

		if ($row) { return $row; }
		return '';
	}

	//
	// Сохранить в БД информацию о том, что пользователь авторизовался
	// $dbrow	= строка из нашей таблицы пользователей
	function save_auth($dbrow) {
		global $config, $mysql, $ip;

        // создаём random cookie
        $auth_cookie = md5($config['crypto_salt'].uniqid(rand(),1));

		$query = "update ".uprefix."_users set last = ".db_squote(time()).", ip=".db_squote($ip).", authcookie = ".db_squote($auth_cookie)." where id=".db_squote($dbrow['id']);
		$mysql->query($query);

		// Вставить юзеру куку
		@setcookie('zz_auth', $auth_cookie, ($config['remember']?(time() + 3600 * 24 * 365):0), '/');

		return 1;
	}

	//
	// Проверить авторизацию пользователя
	function check_auth() {
	 	global $config, $mysql;

	 	$auth_cookie = $_COOKIE['zz_auth'];
	 	if (!$auth_cookie) { return ''; }

	 	$query = "select * from ".uprefix."_users where authcookie = ".db_squote($auth_cookie)." limit 1";
	 	$row = $mysql->record($query);

		// Auth done
	 	if ($row['name']) {
			// Check if we need to update last visit field
	 		if ((pluginGetVariable('auth_basic', 'lastupdate') > 0) && ((time() - $row['last']) > pluginGetVariable('auth_basic', 'lastupdate'))) {
	 			$query = "update ".uprefix."_users set last = ".db_squote(time()).", ip=".db_squote($ip)." where id=".db_squote($row['id']);
	 			$mysql->query($query);
	 		}

	 		return $row;
	 	}
	 	return '';
	}

	//
	// Отменить авторизацию
	function drop_auth() {
	 	global $config, $mysql, $userROW;

	 	$auth_cookie = $_COOKIE['zz_auth'];
	 	if (!$auth_cookie) { return; }

		$mysql->query("update ".uprefix."_users set authcookie = '' where id=".db_squote($userROW['id']));
		@setcookie('zz_auth', '', time() - 3600 * 24 * 365, '/');
	 	return;
	}

	//
	// Вернуть массив параметров, необходимых при регистрации
	function get_reg_params() {
		global $config, $lang;
		$params = array();
		LoadPluginLang('auth_basic', 'auth','','auth');
		array_push($params, array('name' => 'login', title => $lang['auth_login'], 'descr' => $lang['auth_login_descr'],'type' => 'input'));
		if ($config['register_type'] == "3") {
                	array_push($params, array('name' => 'password', title => $lang['auth_pass'], 'descr' => $lang['auth_pass_descr'], 'type' => 'password'));
			array_push($params, array('name' => 'password2', title => $lang['auth_pass2'], 'descr' => $lang['auth_pass2_descr'],'type' => 'password'));
		}
		array_push($params, array('name' => 'email', title => $lang['auth_email'], 'descr' => $lang['auth_email_descr'],'type' => 'input'));
		return $params;
	}

	//
	// Провести регистрацию
	// params = параметры полученные из get_reg_params()
	// values = значения для вышеуказанных параметрах
	// msg	= сообщение об ошибке
	// Возвращаемые значения:
	// 0 - ошибка
	// 1 - всё ok
	function register(&$params, $values, &$msg) {
	 	global $config, $mysql, $lang;

	 	$error = 0;
	 	$values['login'] = trim($values['login']);

	 	// Preprocess login
	 	if (strlen($values['login'])<3) {
	 		// Слишком короткий логин
	 		$msg = $lang['auth_login_short'];
	 		return 0;
	 	}

	 	if (preg_match('/[&<>\xFF'."'".']/', $values['login'])) {
	 		// Запрещенные HTML символы
	 		$msg = $lang['auth_login_html'];
	 		return 0;
	 	}


	 	if ($config['register_type'] == "3") {
	 		if (strlen($values['password']) < 3) {
		 		// Слишком короткий пароль
		 		$msg = $lang['auth_pass_short'];
		 		return 0;

		 	} else if ($values['password'] != $values['password2']) {
		 		// Несовпадение паролей
		 		$msg = $lang['auth_pass_diff'];
		 		return 0;
		 	}
		}

		if ((strlen($values['email']) > "70") || (!preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $values['email']))) {
			// Неверный email
			$msg = $lang['auth_email_wrong'];
			return 0;

		}

		$row = $mysql->record("select * from ".uprefix."_users where lower(name)=".db_squote(strtolower($values['login']))." or mail=".db_squote($values['email']));
		if (is_array($row)) {
			// Дублирование логина/email'а
			if (strtolower($row['mail']) == strtolower($values['email'])) {
				// email dup
				$msg = $lang['auth_email_dup'];
				return 0;
			}
			// Если не мыло, то логин
			$msg = $lang['auth_login_dup'];
			return 0;
		}

		// Всё в порядке, регим
		$add_time = time() + ($config['date_adjust'] * 60);

		// Статус пользователя по умолчанию
		$regstatus = intval(extra_get_param('auth_basic','regstatus'));
		if (($regstatus < 1)||($regstatus > 4))
			$regstatus = 4;

		switch ($config['register_type']) {
			case 0:
				$newpassword = MakeRandomPassword();
				$mysql->query("INSERT INTO ".uprefix."_users (name, pass, mail, status, reg, last) VALUES (".db_squote($values['login']).", ".db_squote(EncodePassword($newpassword)).", ".db_squote($values['email']).", ".$regstatus.", '".$add_time."', '')");
				msg(array("text" => $lang['msgo_registered'], "info" => sprintf($lang['msgo_info1'], $newpassword)));
				break;
			case 1:
				$newpassword = MakeRandomPassword();
				$mysql->query("INSERT INTO ".uprefix."_users (name, pass, mail, status, reg, last) VALUES (".db_squote($values['login']).", ".db_squote(EncodePassword($newpassword)).", ".db_squote($values['email']).", ".$regstatus.", '".$add_time."', '')");
				zzMail($values['email'], $lang['letter_title'], sprintf($lang['letter_text'], home, home).sprintf($lang['your_info'], $values['login'], $newpassword), 'html');
				msg(array("text" => $lang['msgo_registered'], "info" => $lang['msgo_info2']));
				break;
			case 2:
				$newpassword	=	MakeRandomPassword();
				$actcode		=	MakeRandomPassword();
				$mysql->query("INSERT INTO ".uprefix."_users (name, pass, mail, status, reg, last, activation) VALUES (".db_squote($values['login']).", ".db_squote(EncodePassword($newpassword)).", ".db_squote($values['email']).", ".$regstatus.", '".$add_time."', '', '".$actcode."')");
				$userid			=	$mysql->record('select LAST_INSERT_ID() as id');
				$link			=	generatePluginLink('core', 'activation', array('userid' => $userid['id'], 'code' => $actcode), array(), false, true);

				$actlink		=	'<a href="'.$link.'">'.$link.'</a>';
				zzMail($values['email'], $lang['letter_title'], sprintf($lang['letter_text'], home, home).sprintf($lang['your_info'], $values['login'], $newpassword).sprintf($lang['activate'], $actlink), 'html');
				msg(array("text" => $lang['msgo_registered'], "info" => $lang['msgo_info3']));
				break;
			case 3:
				$mysql->query("INSERT INTO ".uprefix."_users (name, pass, mail, status, reg, last) VALUES (".db_squote($values['login']).", ".db_squote(EncodePassword($values['password'])).", ".db_squote($values['email']).", ".$regstatus.", '".$add_time."', '')");
				zzMail($values['email'], $lang['letter_title'], sprintf($lang['letter_text'], home, home).sprintf($lang['your_info'], $values['login'], $values['password']), 'html');
				msg(array("text" => $lang['msgo_registered']));
		}

		return 1;

	}

	//
	// Вернуть массив параметров, необходимых для восстановления пароля
	function get_restorepw_params() {
		global $config, $lang;
		$params = array();

		LoadPluginLang('auth_basic', 'auth','','auth');
		$mode = extra_get_param('auth_basic','restorepw');
		if (!$mode) {
			return false;
			//array_push($params, array('text' => $lang['auth_norestore']));
			//return $params;
	        }

		array_push($params, array('text' => $lang['auth_restore_'.$mode]));
	        if ($mode != 'email') {
			array_push($params, array('name' => 'login', title => $lang['auth_login'],'type' => 'input'));
		}
		if ($mode != 'login') {
			array_push($params, array('name' => 'email', title => $lang['auth_email'],'type' => 'input'));
		}
		return $params;
	}

	//
	// Восстановить пароль
	function restorepw(&$params, $values, &$msg) {
	 	global $config, $mysql, $lang, $tpl;

	 	$error = 0;
	 	$values['login'] = trim($values['login']);
	 	$values['email'] = trim($values['email']);

		LoadPluginLang('auth_basic', 'auth','','auth');
		$mode = extra_get_param('auth_basic','restorepw');

	 	if (!$mode) {
	 		$msg = $lang['auth_norestore'];
	 		return 0;
	 	}

	 	$px = array();

		if ($mode != 'email') {
			if (!$values['login']) {
	 			$msg = $lang['auth_login_require'];
	 			return 0;
	 		}
	 		array_push($px, "name = ".db_squote($values['login']));
	 	}

	 	if ($mode != 'login') {
	 		if (!$values['email']) {
				$msg = $lang['auth_email_require'];
				return 0;
			}
	 		array_push($px, "mail = ".db_squote($values['email']));
		}

		$query = "select * from ".uprefix."_users where ".implode(' and ',$px);
		$row = $mysql->record($query);
		if (is_array($row)) {
		 	// Нашли юзера
			$newpassword = MakeRandomPassword();
			$mysql->query("UPDATE ".uprefix."_users SET newpw=".db_squote(EncodePassword($newpassword))." WHERE id=".$row['id']);

			$tvars['vars'] = array( 'login' => $row['name'],
						'home' => home,
						'newpw' => $newpassword);
			$tvars['vars']['pwurl'] = generatePluginLink('core', 'lostpassword', array('userid' => $row['id'], 'code' => EncodePassword($newpassword)), array(), false, true);

			$tpl -> template('restorepw', GetPluginLangDir('auth_basic'));
			$tpl -> vars('restorepw', $tvars);

			zzMail($row['mail'],$lang['auth_mail_subj'],$tpl->show('restorepw'));
			msg(array("text" => $lang['msgo_sent']));

			return 1;

		} else {
			$msg = $lang['auth_nouser'];
			return 0;
		}
        }

	//
  // Подтверждение восстановления пароля
  //
  function confirm_restorepw(&$msg, $reqid = NULL, $reqsecret = NULL) {
		global $config, $mysql, $lang, $tpl;

		LoadPluginLang('auth_basic', 'auth','','auth');

	 	$row = $mysql->record("select * from ".uprefix."_users where id = ".db_squote($reqid));
	 	if (is_array($row)) {
	 		if ($reqsecret == $row['newpw']) {
	 			// OK !!!
	 			$msg = $lang['auth_newpw_ok'];
	 			$mysql->query('update '.uprefix.'_users set pass=newpw where id = '.db_squote($reqid));
	 			return 1;
	 		}
	 	}
		$msg = $lang['auth_newpw_fail'];
		return 0;
	}
}

$AUTH_METHOD['basic']	= new auth_basic;
$AUTH_CAPABILITIES['basic'] = array('login' => '1', 'db' => '1');

if (extra_get_param('auth_basic','en_dbprefix')) {
	$config['uprefix'] = extra_get_param('auth_basic','dbprefix');
}