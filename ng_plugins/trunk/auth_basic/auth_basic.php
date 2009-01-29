<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


//
// ����������� ���� ������
//
global $AUTH_METHOD;
global $AUTH_CAPABILITIES;
global $config;

$AUTH_METHOD['basic']	= new auth_basic;
$AUTH_CAPABILITIES['basic'] = array('login' => '1', 'db' => '1');

if (extra_get_param('auth_basic','en_dbprefix')) { $config['uprefix'] = extra_get_param('auth_basic','dbprefix'); }

class auth_basic {

	// ����������� ����
	// $username	= �����
	// $password	= ������
	// $auto_scan	= ���� 1, �� ������� ���� ������ ����� ������ ��������� ����� POST'��
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
	// ��������� � �� ���������� � ���, ��� ������������ �������������
	// $dbrow	= ������ �� ����� ������� �������������
	function save_auth($dbrow) {
		global $config, $mysql, $ip;

	        // ������ random cookie
	        $auth_cookie = md5($config['crypto_salt'].uniqid(rand(),1));

		$query = "update ".uprefix."_users set last = ".db_squote(time()).", ip=".db_squote($ip).", authcookie = ".db_squote($auth_cookie)." where id=".db_squote($dbrow['id']);
		$mysql->query($query);

		// �������� ����� ����
		@setcookie('zz_auth', $auth_cookie, ($config['remember']?(time() + 3600 * 24 * 365):0), '/');

		return 1;
	}

	//
	// ��������� ����������� ������������
	function check_auth() {
	 	global $config, $mysql;

	 	$auth_cookie = $_COOKIE['zz_auth'];
	 	if (!$auth_cookie) { return ''; }

	 	$query = "select * from ".uprefix."_users where authcookie = ".db_squote($auth_cookie);
	 	$row = $mysql->record($query);

	 	if ($row['name']) { return $row; }
	 	return '';
	}

	//
	// �������� �����������
	function drop_auth() {
	 	global $config, $mysql;

	 	$auth_cookie = $_COOKIE['zz_auth'];
	 	if (!$auth_cookie) { return; }

		$mysql->query("update ".uprefix."_users set authcookie = '' where userid=".db_squote($userid));
		@setcookie('zz_auth', '', time() - 3600 * 24 * 365, '/');
	 	return;
	}

	//
	// ������� ������ ����������, ����������� ��� �����������
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
	// �������� �����������
	// params = ��������� ���������� �� get_reg_params()
	// values = �������� ��� ������������� ����������
	// msg	= ��������� �� ������
	// ������������ ��������:
	// 0 - ������
	// 1 - �� ok
	function register(&$params, $values, &$msg) {
	 	global $config, $mysql, $lang;

	 	$error = 0;
	 	$values['login'] = trim($values['login']);

	 	// Preprocess login
	 	if (strlen($values['login'])<3) {
	 		// ������� �������� �����
	 		$msg = $lang['auth_login_short'];
	 		return 0;
	 	}

	 	if (preg_match('/[&<>'."'".']/', $values['login'])) {
	 		// ����������� HTML �������
	 		$msg = $lang['auth_login_html'];
	 		return 0;
	 	}


	 	if ($config['register_type'] == "3") {
	 		if (strlen($values['password']) < 3) {
		 		// ������� �������� ������
		 		$msg = $lang['auth_pass_short'];
		 		return 0;

		 	} else if ($values['password'] != $values['password2']) {
		 		// ������������ �������
		 		$msg = $lang['auth_pass_diff'];
		 		return 0;
		 	}
		}

		if ((strlen($values['email']) > "70") || (!preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $values['email']))) {
			// �������� email
			$msg = $lang['auth_email_wrong'];
			return 0;

		}

		$row = $mysql->record("select * from ".uprefix."_users where lower(name)=".db_squote(strtolower($values['login']))." or mail=".db_squote($values['email']));
		if (is_array($row)) {
			// ������������ ������/email'�
			if (strtolower($row['mail']) == strtolower($values['email'])) {
				// email dup
				$msg = $lang['auth_email_dup'];
				return 0;
			}
			// ���� �� ����, �� �����
			$msg = $lang['auth_login_dup'];
			return 0;
		}

		// �� � �������, �����
		$add_time = time() + ($config['date_adjust'] * 60);

		// ������ ������������ �� ���������
		$regstatus = intval(extra_get_param('auth_basic','regstatus'));
		if (($regstatus < 1)||($regstatus > 4))
			$regstatus = 4;

		if ($config['register_type'] == "0") {
			$newpassword = MakeRandomPassword();
			$mysql->query("INSERT INTO ".uprefix."_users (name, pass, mail, status, reg, last) VALUES (".db_squote($values['login']).", ".db_squote(EncodePassword($newpassword)).", ".db_squote($values['email']).", ".$regstatus.", '".$add_time."', '')");
			msg(array("text" => $lang['msgo_registered'], "info" => sprintf($lang['msgo_info1'], $newpassword)));
		}
		if ($config['register_type'] == "1") {
			$newpassword = MakeRandomPassword();
			$mysql->query("INSERT INTO ".uprefix."_users (name, pass, mail, status, reg, last) VALUES (".db_squote($values['login']).", ".db_squote(EncodePassword($newpassword)).", ".db_squote($values['email']).", ".$regstatus.", '".$add_time."', '')");
			zzMail($values['email'], $lang['letter_title'], sprintf($lang['letter_text'], home, home).sprintf($lang['your_info'], $values['login'], $newpassword), 'html');
			msg(array("text" => $lang['msgo_registered'], "info" => $lang['msgo_info2']));
		}
		if ($config['register_type'] == "2") {
			$newpassword		=	MakeRandomPassword();
			$actcode		=	MakeRandomPassword();
			$mysql->query("INSERT INTO ".uprefix."_users (name, pass, mail, status, reg, last, activation) VALUES (".db_squote($values['login']).", ".db_squote(EncodePassword($newpassword)).", ".db_squote($values['email']).", ".$regstatus.", '".$add_time."', '', '".$actcode."')");
			$userid			=	$mysql->record('select LAST_INSERT_ID() as id');
			$link			=	GetLink('activation_do', array('userid' => $userid['id'], 'code' => $actcode));
			$actlink		=	'<a href="'.$link.'">'.$link.'</a>';
			zzMail($values['email'], $lang['letter_title'], sprintf($lang['letter_text'], home, home).sprintf($lang['your_info'], $values['login'], $newpassword).sprintf($lang['activate'], $actlink), 'html');
			//echo $lastid;
			msg(array("text" => $lang['msgo_registered'], "info" => $lang['msgo_info3']));
		}
		if ($config['register_type'] == "3") {
			$mysql->query("INSERT INTO ".uprefix."_users (name, pass, mail, status, reg, last) VALUES (".db_squote($values['login']).", ".db_squote(EncodePassword($values['password'])).", ".db_squote($values['email']).", ".$regstatus.", '".$add_time."', '')");
			zzMail($values['email'], $lang['letter_title'], sprintf($lang['letter_text'], home, home).sprintf($lang['your_info'], $values['login'], $values['password']), 'html');
			msg(array("text" => $lang['msgo_registered']));
			//print var_dump($lang);
		}
		return 1;

	}

	//
	// ������� ������ ����������, ����������� ��� �������������� ������
	function get_restorepw_params() {
		global $config, $lang;
		$params = array();

		LoadPluginLang('auth_basic', 'auth','','auth');
		$mode = extra_get_param('auth_basic','restorepw');
		if (!$mode) {
			array_push($params, array('text' => $lang['auth_norestore']));
			return $params;
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
	// ������������ ������
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
		 	// ����� �����
			$newpassword = MakeRandomPassword();
			$mysql->query("UPDATE ".uprefix."_users SET newpw=".db_squote(EncodePassword($newpassword))." WHERE id=".$row['id']);

			$tvars['vars'] = array( 'login' => $row['name'],
						'home' => home,
						'newpw' => $newpassword,
						'pwurl' => home.'/?action=lostpassword&type=confirm&uid='.$row['id'].'&secret='.EncodePassword($newpassword));
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
  // ������������� �������������� ������
  //
  function confirm_restorepw(&$msg) {
		global $config, $mysql, $lang, $tpl;

		LoadPluginLang('auth_basic', 'auth','','auth');

	 	$reqid = $_REQUEST['uid'];
	 	$reqsecret = $_REQUEST['secret'];
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
?>
